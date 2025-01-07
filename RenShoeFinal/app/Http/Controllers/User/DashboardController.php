<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    function index(){
        $orders   = Order::with('order_items.product.brand', 'order_items.product.category')->get();
        $users    = User::all();
        $authUser = auth()->user(); 
        $address  = UserAddress::where('user_id', $authUser->id)->first();

        return Inertia::render('User/Dashboard', [
                                                  'orders'   => $orders,
                                                  'users'    => $users,
                                                  'authUser' => $authUser,
                                                  'address'  => $address,
                                                ]);
    }
}
