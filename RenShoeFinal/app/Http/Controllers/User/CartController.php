<?php

namespace App\Http\Controllers\User;

use App\Helper\Cart;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function view(Request $request, Product $product)
    {
        $user = $request->user();
        if($user){
            $cartItems = CartItem::where('user_id', $user->id)->get();
            $userAddress = UserAddress::where('user_id', $user->id)->where('isMain', 1)->first();
            if($cartItems->count() > 0){
                return Inertia::render('User/CartList',
                [
                    'cartItems' => $cartItems,
                    'userAddress' => $userAddress
                ]);
            }
        }
        else{
            $cartItems = Cart::getCookieCartItems();
            if(count($cartItems) > 0){
                $cartItems = new CartResource(Cart::getProductsAndCartItems());
                return Inertia::render('User/CartList', ['cartItems' => $cartItems]);
            }else{
                return redirect()->back();
            }
        }
        
    }
    
    /*public function store(Request $request, Product $product){
        $quantity = $request->post('quantity', 1);
        $user = $request->user();

        if($user){
            $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();
            if($cartItem){
                $cartItem->increment('quantity');
            }else{
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                ]);
            }
        }else{
            $cartItems = Cart::getCookieCartItems();
            $isProductExists = false;
            foreach($cartItems as $item){
                if($item['product_id'] === $product->id)
                {
                    $item['quantity'] += $quantity;
                    $isProductExists = true;
                    break;
                }

            }

            if(!$isProductExists){
                $cartItems[] = [
                    'user_id' => null,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ];
            }
            Cart::setCookieCartItems($cartItems);


        }
        return redirect()->back()->with('success', 'cart added successfully!');

    }
    */

    public function store(Request $request, Product $product)
{
    $quantity = $request->post('quantity', 1); // Default quantity is 1
    $user = $request->user();

    // Check if there's enough running stock
    if ($product->running_stock < $quantity) {
        return redirect()->back()->with('error', 'Not enough stock available');
    }

    if ($user) {
        // User is logged in
        $cartItem = CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->first();
        if ($cartItem) {
            // Update the quantity if cart item exists
            $newQuantity = $cartItem->quantity + $quantity;

            if ($product->running_stock < $newQuantity) {
                return redirect()->back()->with('error', 'Not enough stock available for this quantity.');
            }

            $cartItem->increment('quantity', $quantity);
        } else {
            // Create a new cart item if it doesn't exist
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }
    } else {
        // User is a guest, handle cart with cookies
        $cartItems = Cart::getCookieCartItems();
        $isProductExists = false;

        foreach ($cartItems as &$item) {
            if ($item['product_id'] === $product->id) {
                // Update the quantity for the existing cart item
                $newQuantity = $item['quantity'] + $quantity;

                if ($product->running_stock < $newQuantity) {
                    return redirect()->back()->with('error', 'Not enough stock available for this quantity.');
                }

                $item['quantity'] += $quantity;
                $isProductExists = true;
                break;
            }
        }

        if (!$isProductExists) {
            // Add new product to the guest cart
            if ($product->running_stock < $quantity) {
                return redirect()->back()->with('error', 'Not enough stock available.');
            }

            $cartItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        // Save updated cart to cookies
        Cart::setCookieCartItems($cartItems);
    }

    // Deduct running stock
    $product->decrement('running_stock', $quantity);

    return redirect()->back()->with('success', 'Product added to cart successfully!');
}



    public function update(Request $request, Product $product){
        $quantity = $request->integer('quantity');
        $user = $request->user();
        if($user){
            CartItem::where(['user_id' => $user->id, 'product_id' => $product->id])->update(['quantity' => $quantity]);

        }else{
            $cartItems = Cart::getCookieCartItems();
            foreach($cartItems as &$item){
                if ($item['product_id'] === $product->id){
                    $item['quantity'] = $quantity;
                    break;
                }
            }
            Cart::setCookieCartItems($cartItems);

        }
        return redirect()->back();

    }
    public function delete(Request $request, Product $product){
        $user = $request->user();
        if($user){
            CartItem::query()->where(['user_id' => $user->id, 'product_id' => $product->id])->first()?->delete();
            if(CartItem::count() <= 0){
                return redirect()->route('home')->with('info', 'your cart is empty');
            }else{
                return redirect()->back()->with('success', 'item removed successfully!');
            }
        }else{
            $cartItems = Cart::getCookieCartItems();
            foreach($cartItems as $i => &$item){
                if($item['product_id'] === $product->id){
                    array_splice($cartItems, $i, 1);
                    break;
                }
            }
            Cart::setCookieCartItems($cartItems);
            if(count($cartItems) <= 0){
                return redirect()->route('home')->with('info', 'your cart is empty');
            }else{
                return redirect()->back()->with('success', 'item removed successfully');
            }

        }

    }
}
