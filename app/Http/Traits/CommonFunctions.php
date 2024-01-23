<?php

namespace App\Http\Traits;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;

trait CommonFunctions 
{
    
	public function getProducts($status = true)
	{
		return Product::when($status == true, function($query){
			$query->where('status', 'ACTIVE');
		})
		->select('id', 'title', 'category', 'amount', 'status')
		->latest();
	}

	public function getOrders()
	{
		return Order::with([
			'cart:id,product_id',
			'cart.product:id,title'
		])
		->select('id', 'cart_id', 'order_date', 'shipping_charge', 'order_total', 'order_status')
		->latest();
	}

} 