<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');
        $status = $request->query('status');

        $query = Item::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'habis') {
            $query->where('stock', '<=', 0);
        } elseif ($stockStatus === 'menipis') {
            $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'minimum_stock');
        } elseif ($stockStatus === 'aman') {
            $query->whereColumn('stock', '>', 'minimum_stock');
        }

        if ($status !== null && $status !== '') {
            $query->where('status', (bool) $status);
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items.index', [
            'items' => $items,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'stockStatus' => $stockStatus,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('items.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:30'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ], [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama barang wajib diisi.',
            'name.max' => 'Nama barang maksimal 150 karakter.',
            'unit.required' => 'Satuan barang wajib diisi.',
            'unit.max' => 'Satuan maksimal 30 karakter.',
            'stock.required' => 'Jumlah stok awal wajib diisi.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
            'minimum_stock.min' => 'Batas stok minimum tidak boleh negatif.',
            'location.max' => 'Lokasi penyimpanan maksimal 100 karakter.',
            'status.required' => 'Status barang wajib ditentukan.',
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::create($validated);

            if ($item->stock > 0) {
                StockMovement::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'type' => 'IN',
                    'quantity' => $item->stock,
                    'stock_before' => 0,
                    'stock_after' => $item->stock,
                    'reference_type' => 'InitialStock',
                    'reference_id' => $item->id,
                    'notes' => 'Pencatatan stok awal saat penambahan barang baru.',
                ]);
            }
        });

        return redirect()->route('items.index')
            ->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function show(Item $item): View
    {
        $item->load('category');
        $movements = $item->stockMovements()
            ->with('user')
            ->latest('id')
            ->paginate(10);

        return view('items.show', [
            'item' => $item,
            'movements' => $movements,
        ]);
    }

    public function edit(Item $item): View
    {
        $categories = Category::orderBy('name')->get();

        return view('items.edit', [
            'item' => $item,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ], [
            'category_id.required' => 'Kategori barang wajib dipilih.',
            'name.required' => 'Nama barang wajib diisi.',
            'unit.required' => 'Satuan barang wajib diisi.',
            'minimum_stock.required' => 'Batas stok minimum wajib diisi.',
            'minimum_stock.min' => 'Batas stok minimum tidak boleh negatif.',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->stockInDetails()->exists() || $item->stockOutDetails()->exists() || $item->stockMovements()->where('reference_type', '!=', 'InitialStock')->exists()) {
            return back()->with('error', 'Barang "' . $item->name . '" tidak dapat dihapus karena telah memiliki riwayat transaksi.');
        }

        try {
            DB::transaction(function () use ($item) {
                $item->stockMovements()->delete();
                $item->delete();
            });

            return redirect()->route('items.index')
                ->with('success', 'Barang "' . $item->name . '" berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}
