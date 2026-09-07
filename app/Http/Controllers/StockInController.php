<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = StockIn::with(['user', 'details.item']);

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

        $stockIns = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        return view('stock_ins.index', [
            'stockIns' => $stockIns,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function create(): View
    {
        $items = Item::where('status', true)->with('category')->orderBy('name')->get();
        
        // Auto-generate suggested transaction number
        $today = date('Ymd');
        $countToday = StockIn::whereDate('created_at', date('Y-m-d'))->count() + 1;
        $suggestedNumber = 'IN-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        return view('stock_ins.create', [
            'items' => $items,
            'suggestedNumber' => $suggestedNumber,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_number' => ['required', 'string', 'max:50', 'unique:stock_ins,transaction_number'],
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
                $stockIn = StockIn::create([
                    'transaction_number' => $request->input('transaction_number'),
                    'transaction_date' => $request->input('transaction_date'),
                    'user_id' => Auth::id(),
                    'notes' => $request->input('notes'),
                ]);

                foreach ($request->input('items') as $row) {
                    $item = Item::lockForUpdate()->findOrFail($row['item_id']);
                    $quantity = (int) $row['quantity'];

                    $stockBefore = $item->stock;
                    $stockAfter = $stockBefore + $quantity;

                    $item->update(['stock' => $stockAfter]);

                    StockInDetail::create([
                        'stock_in_id' => $stockIn->id,
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                    ]);

                    StockMovement::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'type' => 'IN',
                        'quantity' => $quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'reference_type' => 'StockIn',
                        'reference_id' => $stockIn->id,
                        'notes' => 'Stok Masuk No. ' . $stockIn->transaction_number . ($stockIn->notes ? ' - ' . $stockIn->notes : ''),
                    ]);
                }
            });

            return redirect()->route('stock-ins.index')
                ->with('success', 'Stok masuk berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menyimpan transaksi stok masuk: ' . $e->getMessage());
        }
    }

    public function show(StockIn $stockIn): View
    {
        $stockIn->load(['user', 'details.item.category']);

        return view('stock_ins.show', [
            'stockIn' => $stockIn,
        ]);
    }
}
