<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Coffee;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Stats
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total');

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::whereIn('order_status', ['pending', 'preparing'])->count();
        $completedOrders = Order::where('order_status', 'completed')->count();
        $totalCustomers = Customer::count();

        // Best selling coffee
        $bestSellerItem = OrderItem::select('coffee_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('coffee_id')
            ->orderByDesc('total_qty')
            ->with('coffee')
            ->first();

        $bestSellingCoffee = $bestSellerItem ? $bestSellerItem->coffee->name : 'N/A';

        // 2. Chart Data - Daily Sales (Last 7 Days)
        $dailyLabels = [];
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyLabels[] = $date->format('M d');
            $rev = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total');
            $dailyData[] = (float) $rev;
        }

        // 3. Chart Data - Monthly Sales (Last 6 Months)
        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $rev = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('payment_status', 'paid')
                ->sum('total');
            $monthlyData[] = (float) $rev;
        }

        $recentOrders = Order::with(['customer', 'table', 'cashier'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todayRevenue',
            'totalOrdersToday',
            'pendingOrders',
            'completedOrders',
            'totalCustomers',
            'bestSellingCoffee',
            'dailyLabels',
            'dailyData',
            'monthlyLabels',
            'monthlyData',
            'recentOrders'
        ));
    }
}
