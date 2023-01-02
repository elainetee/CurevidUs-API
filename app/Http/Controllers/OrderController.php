<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\OrderMedicine;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isEmpty;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::all();
        return $order;
    }

    public function medicineInCart()
    {
        $user = JWTAuth::user();
        // return $user->order()->where('order_status', 'cart')->exists();
        if ($user->order()->where('order_status', 'cart')->exists()) {

            $orderId = $user->order()->where([
                ['order_status', 'cart'],
            ])->value('order_id');

            $order = Order::find($orderId);
            $cartMeds = collect();

            foreach ($order->medicines as $medicines) {
                // echo $medicines->pivot->medicine_id;
                $cartMedId = $medicines->pivot->medicine_id;
                $cartMed = Medicine::find($cartMedId);

                $cartMeds->add($cartMed);
            }
            return $cartMeds;
        } else {
            return response()->json('You have not create any order yet', 400);
        }
        // return $order;
        // return $cartMed;
    }

    public function medicineCheckout()
    {
        $user = JWTAuth::user();
        // return $user->order()->where('order_status', 'cart')->exists();
        if ($user->order()->where('order_status', 'checkout')->exists()) {

            $orderId = $user->order()->where([
                ['order_status', 'checkout'],
            ])->value('order_id');

            $order = Order::find($orderId);
            $cartMeds = collect();

            foreach ($order->medicines as $medicines) {
                // echo $medicines->pivot->medicine_id;
                $cartMedId = $medicines->pivot->medicine_id;
                $cartMed = Medicine::find($cartMedId);

                $cartMeds->add($cartMed);
            }
            return $cartMeds;
        } else {
            return response()->json('You have not ordered any medicine yet', 400);
        }
        // return $order;
        // return $cartMed;
    }

    public function cartStatusOrder()
    {
        $user = JWTAuth::user();
        $order = $user->order()->where([
            ['order_status', 'cart'],
        ])->get();
        // $order = Order::where([
        //     ['user_id', $user_id],
        //     ['order_status', 'cart'],
        // ])->get();
        return $order;
    }

    public function checkoutStatusOrder()
    {
        $user = JWTAuth::user();
        $order = $user->order()->where([
            ['order_status', 'checkout'],
        ])->get();
        // $order = Order::where([
        //     ['user_id', $user_id],
        //     ['order_status', 'cart'],
        // ])->get();
        return $order;
    }

    // public function cartStatus($user_id)
    // {
    //     $order = Order::where([
    //         ['user_id', $user_id],
    //         ['order_status', 'cart'],
    //     ])->firstOrFail();
    //     return $order;
    // }

    public function addToCart(Request $request)
    {
        $user = JWTAuth::user();
        $cartIsCreated = $this->cartStatusOrder();
        // $request->validate([
        //     // 'order_date' => 'required',
        //     'order_price' => 'required',
        //     'order_status' => 'required',
        // ]);
        $ldate = date('Y-m-d');
        $medId = $request->get('medicine_id');
        $medicine = Medicine::where([
            ['medicine_id', $medId]
        ])->firstOrFail();

        // var_export(isEmpty($cartIsCreated));
        if ($cartIsCreated->isEmpty()) {

            $order = Order::create([
                'order_date' => $ldate,
                'order_price' => $request->get('order_price'),
                'order_status' => 'cart',
                'user_id' => $user->id,
            ]);

            $order = $user->order()->where([
                ['order_status', 'cart'],
            ])->firstOrFail();

            $order->medicines()->attach($medicine);
        } else {
            $orderId = $user->order()->where([
                ['order_status', 'cart'],
            ])->value('order_id');
            // echo $orderId;

            $orderMedicine = OrderMedicine::where([
                ['order_id', $orderId],
                ['medicine_id', $medId]
            ])->get();
            // echo $orderMedicine;

            if ($orderMedicine->isEmpty()) {
                $order = $user->order()->where([
                    ['order_status', 'cart'],
                ])->firstOrFail();

                $order->medicines()->attach($medicine);
            } else {
                return response()->json('Medicine already exist in cart', 400);
            }
        }
        // echo $order;
        return response()->json(['success' => true, 'message' => 'Medicine added to cart successfully']);
    }

    public function dltFromCart($medicine_id)
    {
        $user = JWTAuth::user();
        $order = $user->order()->where([
            ['order_status', 'cart'],
        ])->firstOrFail();
        $medicine = Medicine::where([
            ['medicine_id', $medicine_id]
        ])->firstOrFail();

        $order->medicines()->detach($medicine);

        return response()->json(['success' => true, 'message' => 'Medicine removed from cart successfully']);
    }

    public function checkout()
    {
        $user = JWTAuth::user();
        $ldate = date('Y-m-d');
        $order = $user->order()->where([
            ['order_status', 'cart'],
        ])->firstOrFail();

        $order->update([
            'order_date' => $ldate,
            'order_status' => 'checkout'
        ]);

        return response()->json(['success' => true, 'message' => 'Ordered successfully']);
    }
}
