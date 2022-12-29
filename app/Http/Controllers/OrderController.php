<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Medicine;
use App\Models\OrderMedicine;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::all();
        return $order;
    }

    public function cartStatusOrder($user_id)
    {
        $order = Order::where([
            ['user_id', $user_id],
            ['order_status', 'cart'],
        ])->get();
        return $order;
    }

    public function addToCart(Request $request, $user_id)
    {
        $cartIsCreated = $this->cartStatusOrder($user_id);
        // $request->validate([
        //     // 'order_date' => 'required',
        //     'order_price' => 'required',
        //     'order_status' => 'required',
        // ]);
        $ldate = date('Y-m-d H:i:s');
        $medId = $request->get('medicine_id');
        $medicine = Medicine::where([
            ['medicine_id', $medId]
        ])->firstOrFail();

        // var_export(isEmpty($cartIsCreated));
        if ($cartIsCreated -> isEmpty()) {

            $order = Order::create([
                'order_date' => $ldate,
                'order_price' => $request->get('order_price'),
                'order_status' => 'cart',
                'user_id' => $user_id,
            ]);

            $order = Order::where([
                ['user_id', $user_id],
                ['order_status', 'cart'],
            ])->firstOrFail();

            $order -> medicines() -> attach($medicine);

        } else {
            $orderId = Order::where([
                ['user_id', $user_id],
                ['order_status', 'cart'],
            ]) -> value('order_id');
            // echo $orderId;

            $orderMedicine = OrderMedicine::where([
                ['order_id', $orderId],
                ['medicine_id', $medId]
            ]) -> get();
            // echo $orderMedicine;

            if ($orderMedicine -> isEmpty()) {
                $order = Order::where([
                    ['user_id', $user_id],
                    ['order_status', 'cart'],
                ])->firstOrFail();
                $order -> medicines() -> attach($medicine);
            }else{
                echo "Medicine already exist in cart";
            }
            
        }

        return $order;
    }
}
