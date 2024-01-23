<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CommonFunctions;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use Validator;
use Auth;
use Carbon\Carbon;

class ProductController extends Controller
{
    
    use CommonFunctions;

    public function saveProduct(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'title' => 'required',
            'amount' => 'required'
        ]);
        if ($validateData->fails()) {
            $jsonArray = ['status' => 'validationError', 'messages' => $validateData->messages()];
        } else {
            Product::create($request->all());
            $jsonArray = ['status' => 'success', 'messages' => 'New product successfully added!'];
        }
        return response()->json($jsonArray);
    }

    public function products()
    {
        $products = $this->getProducts(false)->paginate(9);
        // dd($products);
        if (count($products) > 0) {
            $jsonArray = ['status' => 'success', 'products' => $products];
        } else {
            // dd(3);
            $jsonArray = ['status' => 'error', 'message' => 'There is no products!'];
        }
        return response()->json($jsonArray);
    }

    public function cart(Request $request)
    {
        if ($this->productIdValidate($request)->fails()) {
            $jsonArray = ['status' => 'validationsError', 'message' => 'Invalid Product'];
        } else {
            $formData = $request->all();
            $tax = 18;
            $product = $this->getProducts(false)->find($request->input('product_id'));
            $taxAmount = $product->amount * $tax/100;
            $formData['tax'] = $tax;
            $formData['tax_amount'] = $taxAmount;
            $formData['product_amount'] = $product->amount;
            $formData['cart_total_amount'] = $product->amount + $taxAmount;
            $formData['user_id'] = Auth::id();
            Cart::create($formData);
            $jsonArray = ['status' => 'success', 'message' => 'You have successfully added to cart!'];
        }
        return response()->json($jsonArray);
    }

    protected function productIdValidate($request): object
    {
        return Validator::make($request->all(),[
            'product_id' => 'bail|required|exists:products,id'
        ]);
    }

    public function order(Request $request)
    {
        $validateData = Validator::make($request->all(),[
            'cart_id' => 'bail|required|exists:carts,id'
        ]);
        if ($validateData->fails()) {
            $jsonArray = ['status' => 'validationError', 'messages' => $validateData->messages()];
        } else {
            $formData = $request->all();
            $formData['user_id'] = Auth::id();
            $cart = Cart::find($request->input('cart_id'));
            $formData['order_date'] = Carbon::now()->format('Y-m-d');
            $shipingCharge = 50;
            $formData['shipping_charge'] = $shipingCharge;
            $formData['order_total'] = $shipingCharge + $cart->cart_total_amount;
            $formData['order_status'] = 'Confirmed';
            Order::create($formData);
            $jsonArray = ['status' => 'success', 'message' => 'Thank you for order'];
        }
        return response()->json($jsonArray);
    }

    public function orderHistory()
    {
        $orders = $this->getOrders()
            ->where('user_id', Auth::id())
            ->get();
        $jsonArray = ['status' => 'success', 'orders' => $orders];
        return response()->json($jsonArray);
    }

}
