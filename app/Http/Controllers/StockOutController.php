<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOutController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = StockOut::with(['user', 'details.item']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details.item', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        $stockOuts = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        return view('stock_outs.index', [
            'stockOuts' => $stockOuts,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function create(): View
    {
        $items = Item::where('status', true)->with('category')->orderBy('name')->get();
        
        $today = date('Ymd');
        $countToday = StockOut::whereDate('created_at', date('Y-m-d'))->count() + 1;
        $suggestedNumber = 'OUT-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        return view('stock_outs.create', [
            'items' => $items,
            'suggestedNumber' => $suggestedNumber,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_number' => ['required', 'string', 'max:50', 'unique:stock_outs,transaction_number'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'transaction_number.required' => 'Nomor transaksi wajib diisi.',
            'transaction_number.unique' => 'Nomor transaksi sudah digunakan.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'items.required' => 'Minimal harus ada 1 barang dalam transaksi.',
            'items.*.item_id.required' => 'Barang wajib dipilih.',
            'items.*.item_id.exists' => 'Barang yang dipilih tidak valid.',
            'items.*.quantity.required' => 'Jumlah barang wajib diisi.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Group requested quantities by item ID to prevent multi-row overdraw
                $itemTotals = [];
                foreach ($request->input('items') as $row) {
                    $itemId = (int) $row['item_id'];
                    $qty = (int) $row['quantity'];
                    $itemTotals[$itemId] = ($itemTotals[$itemId] ?? 0) + $qty;
                }

                // Lock items and check stock sufficiency
                $lockedItems = [];
                foreach ($itemTotals as $itemId => $totalRequired) {
                    $item = Item::lockForUpdate()->findOrFail($itemId);
                    if ($item->stock < $totalRequired) {
                        throw new \Exception('Stok tidak mencukupi untuk barang "' . $item->name . '". (Stok tersedia: ' . $item->stock . ' ' . $item->unit . ', diminta: ' . $totalRequired . ' ' . $item->unit . ')');
                    }
                    $lockedItems[$itemId] = $item;
                }

                $stockOut = StockOut::create([
                    'transaction_number' => $request->input('transaction_number'),
                    'transaction_date' => $request->input('transaction_date'),
                    'user_id' => Auth::id(),
                    'notes' => $request->input('notes'),
                ]);

                foreach ($request->input('items') as $row) {
                    $itemId = (int) $row['item_id'];
                    $quantity = (int) $row['quantity'];
                    $item = $lockedItems[$itemId];

                    $stockBefore = $item->stock;
                    $stockAfter = $stockBefore - $quantity;

                    $item->update(['stock' => $stockAfter]);
                    // Update local reference in case the same item is in another row
                    $item->stock = $stockAfter;

                    StockOutDetail::create([
                        'stock_out_id' => $stockOut->id,
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                    ]);

                    StockMovement::create([
                        'item_id' => $itemId,
                        'user_id' => Auth::id(),
                        'type' => 'OUT',
                        'quantity' => $quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference_type' => 'StockOut',
                        'reference_id' => $stockOut->id,
                        'notes' => 'Stok Keluar No. ' . $stockOut->transaction_number . ($stockOut->notes ? ' - ' . $stockOut->notes : ''),
                    ]);
                }
            });

            return redirect()->route('stock-outs.index')
                ->with('success', 'Stok keluar berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(StockOut $stockOut): View
    {
        $stockOut->load(['user', 'details.item.category']);

        return view('stock_outs.show', [
            'stockOut' => $stockOut,
        ]);
    }
}
