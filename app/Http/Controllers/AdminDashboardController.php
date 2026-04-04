<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function status()
    {
        $totalRevenue = Order::sum('total_price');

        $activeUsers = User::where('role', 'user')->count();

        $totalOrders = Order::count();

        $revenueByMonth = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_price) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $currentMonthRevenue = Order::whereMonth('created_at', now()->month)->sum('total_price');
        $lastMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)->sum('total_price');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2) . '%'
            : '0%';

        $currentMonthUsers = User::where('role', 'user')
            ->whereMonth('created_at', now()->month)->count();

        $lastMonthUsers = User::where('role', 'user')
            ->whereMonth('created_at', now()->subMonth()->month)->count();

        $usersChange = $lastMonthUsers > 0
            ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 2) . '%'
            : '0%';

        $currentMonthOrders = Order::whereMonth('created_at', now()->month)->count();
        $lastMonthOrders = Order::whereMonth('created_at', now()->subMonth()->month)->count();

        $ordersChange = $lastMonthOrders > 0
            ? round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 2) . '%'
            : '0%';

        return response()->json([
            'revenue' => $totalRevenue,
            'revenueByMonth' => $revenueByMonth,
            'activeUsers' => $activeUsers,
            'orders' => $totalOrders,
            'revenueChange' => $revenueChange,
            'usersChange' => $usersChange,
            'ordersChange' => $ordersChange,
        ]);
    }
}
