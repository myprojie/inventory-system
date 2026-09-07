<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $itemId = $request->query('item_id');
        $type = $request->query('type');
        $userId = $request->query('user_id');

        $query = StockMovement::with(['item.category', 'user']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                    ->orWhere('reference_type', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

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

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $movements = $query->latest('id')->paginate(15)->withQueryString();
        $items = Item::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('stock_movements.index', [
            'movements' => $movements,
            'items' => $items,
            'users' => $users,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemId' => $itemId,
            'type' => $type,
            'userId' => $userId,
        ]);
    }
}
