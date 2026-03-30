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
        // Total Revenue
        $totalRevenue = Order::sum('total_price');

        // Active Users
        $activeUsers = User::where('role','user')->count();

        // Total Orders
        $totalOrders = Order::count();

        $revenueByMonth = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueChange = '+0%';
        $usersChange = '+0%';
        $ordersChange = '+0%';

        return response()->json([
            'revenue' => $totalRevenue,
            'revenueByMonth' => $revenueByMonth,
            'users' => $activeUsers,
            'orders' => $totalOrders,
            'revenueChange' => $revenueChange,
            'usersChange' => $usersChange,
            'ordersChange' => $ordersChange,
        ]);
    }
}
