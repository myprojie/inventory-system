<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use App\Models\StockOutDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $totalItems = Item::count();
        $totalCategories = Category::count();
        $lowStockCount = Item::where('stock', '>', 0)->whereColumn('stock', '<=', 'minimum_stock')->count();
        $outOfStockCount = Item::where('stock', '<=', 0)->count();

        return view('reports.index', [
            'totalItems' => $totalItems,
            'totalCategories' => $totalCategories,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ]);
    }

    public function stock(Request $request): View
    {
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');

        $query = Item::with('category');

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

        $items = $query->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('reports.stock', [
            'items' => $items,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'stockStatus' => $stockStatus,
            'print' => $request->boolean('print'),
        ]);
    }

    public function stockIn(Request $request): View
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $itemId = $request->query('item_id');

        $query = StockInDetail::with(['stockIn.user', 'item.category'])
            ->whereHas('stockIn', function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $details = $query->get();
        $items = Item::orderBy('name')->get();

        return view('reports.stock_in', [
            'details' => $details,
            'items' => $items,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemId' => $itemId,
            'print' => $request->boolean('print'),
        ]);
    }

    public function stockOut(Request $request): View
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $itemId = $request->query('item_id');

        $query = StockOutDetail::with(['stockOut.user', 'item.category'])
            ->whereHas('stockOut', function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->whereDate('transaction_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('transaction_date', '<=', $endDate);
                }
            });

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        $details = $query->get();
        $items = Item::orderBy('name')->get();

        return view('reports.stock_out', [
            'details' => $details,
            'items' => $items,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemId' => $itemId,
            'print' => $request->boolean('print'),
        ]);
    }

    public function movements(Request $request): View
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-d'));
        $itemId = $request->query('item_id');
        $type = $request->query('type');

        $query = StockMovement::with(['item.category', 'user']);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($itemId) {
            $query->where('item_id', $itemId);
        }

        if ($type && in_array($type, ['IN', 'OUT'])) {
            $query->where('type', $type);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        $items = Item::orderBy('name')->get();

        return view('reports.movements', [
            'movements' => $movements,
            'items' => $items,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemId' => $itemId,
            'type' => $type,
            'print' => $request->boolean('print'),
        ]);
    }
}
