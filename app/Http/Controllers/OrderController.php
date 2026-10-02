<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'table', 'cashier', 'items.coffee']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cQ) use ($search) {
                      $cQ->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'table', 'cashier', 'items.coffee', 'payment']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => ['required', 'in:pending,preparing,ready,completed,cancelled'],
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $request->order_status;

        DB::transaction(function() use ($order, $oldStatus, $newStatus) {
            $order->update(['order_status' => $newStatus]);

            if ($newStatus === 'completed' && $order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                // Restore coffee stock
                foreach ($order->items as $item) {
                    if ($item->coffee) {
                        $item->coffee->increment('stock', $item->quantity);
                        if ($item->coffee->status === 'out_of_stock' && $item->coffee->stock > 0) {
                            $item->coffee->update(['status' => 'available']);
                        }
                    }
                }
                // Free up table if assigned
                if ($order->table_id) {
                    Table::where('id', $order->table_id)->update(['status' => 'available']);
                }
            }
        });

        return back()->with('success', "Order status updated to {$newStatus}.");
    }

    public function destroy(Order $order)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only Admins can delete orders.');
        }

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
