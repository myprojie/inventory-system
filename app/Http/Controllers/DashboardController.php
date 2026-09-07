<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\StockMovement;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalItems = Item::count();
        $totalCategories = Category::count();
        $totalStock = (int) Item::sum('stock');
        
        $lowStockCount = Item::where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->count();

        $outOfStockCount = Item::where('stock', '<=', 0)->count();

        $totalStockInQty = (int) StockInDetail::sum('quantity');
        $totalStockOutQty = (int) StockOutDetail::sum('quantity');

        $lowStockItems = Item::with('category')
            ->where(function ($query) {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            })
            ->orderBy('stock', 'asc')
            ->limit(8)
            ->get();

        $recentMovements = StockMovement::with(['item', 'user'])
            ->latest('id')
            ->limit(8)
            ->get();

        // Monthly chart data for current year
        $currentYear = (int) date('Y');
        $monthlyIn = array_fill(1, 12, 0);
        $monthlyOut = array_fill(1, 12, 0);

        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite' ? "cast(strftime('%m', created_at) as integer)" : 'MONTH(created_at)';

        $inStats = StockMovement::where('type', 'IN')
            ->whereYear('created_at', $currentYear)
            ->select(DB::raw("{$monthExpr} as month"), DB::raw('SUM(quantity) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $outStats = StockMovement::where('type', 'OUT')
            ->whereYear('created_at', $currentYear)
            ->select(DB::raw("{$monthExpr} as month"), DB::raw('SUM(quantity) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        foreach ($inStats as $month => $total) {
            $monthlyIn[(int) $month] = (int) $total;
        }

        foreach ($outStats as $month => $total) {
            $monthlyOut[(int) $month] = (int) $total;
        }

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return view('dashboard.index', [
            'totalItems' => $totalItems,
            'totalCategories' => $totalCategories,
            'totalStock' => $totalStock,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'totalStockInQty' => $totalStockInQty,
            'totalStockOutQty' => $totalStockOutQty,
            'lowStockItems' => $lowStockItems,
            'recentMovements' => $recentMovements,
            'monthLabels' => json_encode($monthNames),
            'monthlyInData' => json_encode(array_values($monthlyIn)),
            'monthlyOutData' => json_encode(array_values($monthlyOut)),
        ]);
    }
}
