<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(){
            
        return Inertia::render('Admin/Dashboard');
    }

    //Total Stocks
    public function getTotalStocks()
{
    // Assuming 'quantity' is the column in the 'products' table
    $totalStocks = Product::sum('quantity');
    return response()->json(['totalStocks' => $totalStocks]);
}
    //Total Users
    public function getTotalUsers()
{
    $totalUsers = User::count(); // Count the number of users in the 'users' table
    return response()->json(['totalUsers' => $totalUsers]);
}
    //Total Placed Orders
    public function getTotalOrders()
{
    $totalOrders = Order::count(); // Count the number of records in the 'orders' table
    return response()->json(['totalOrders' => $totalOrders]);
}
    //Total Earnings
    public function getTotalSales()
{
    // Sum the 'total_price' column in the 'orders' table
    $totalSales = Order::sum('total_price'); // Adjust 'total_price' to your actual column name
    return response()->json(['totalSales' => $totalSales]);
}
}
