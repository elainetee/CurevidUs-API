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
        $order = Order::orderByDesc('order_date')->get();
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
                $cartMedQty = $medicines->pivot->quantity;
                $cartMed = Medicine::find($cartMedId);
                $medPrice = $cartMed->medicine_price;                
                $cartMed['quantity'] = $cartMedQty;
                $cartMed['medTotalPrice'] = $cartMedQty * $medPrice;
                // dd($cartMed);

                $cartMeds->add($cartMed);
            }
            // $cartMeds['totalQty'] = $cartMeds->sum('quantity');
            return $cartMeds;
        } else {
            return response()->json('You have not create any order yet', 400);
        }
        // return $order;
        // return $cartMed;
    }

    public function medicineCheckout($order_id)
    {
        // $user = JWTAuth::user();
        // return $user->order()->where('order_status', 'cart')->exists();
        // if ($user->order()->where('order_status', 'checkout')->exists()) {

            // $orderId = $user->order()->where([
            //     ['order_status', 'checkout'],
            // ])->value('order_id');

            $order = Order::find($order_id);
            $cartMeds = collect();

            foreach ($order->medicines as $medicines) {
                // echo $medicines->pivot->medicine_id;
                $cartMedId = $medicines->pivot->medicine_id;
                $cartMedQty = $medicines->pivot->quantity;
                $cartMed = Medicine::find($cartMedId);
                $medPrice = $cartMed->medicine_price;                
                $cartMed['quantity'] = $cartMedQty;
                $cartMed['medTotalPrice'] = $cartMedQty * $medPrice;

                $cartMeds->add($cartMed);
            }
            return $cartMeds;
        // } else {
            // return response()->json('You have not ordered any medicine yet', 400);
        // }
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

    public function addToCart($medId)
    {
        $user = JWTAuth::user();
        $cartIsCreated = $this->cartStatusOrder();
        // $request->validate([
        //     // 'order_date' => 'required',
        //     'order_price' => 'required',
        //     'order_status' => 'required',
        // ]);
        $ldate = date('Y-m-d');
        // $medId = $request->get('medicine_id');
        $medicine = Medicine::where([
            ['medicine_id', $medId]
        ])->firstOrFail();

        // var_export(isEmpty($cartIsCreated));
        if ($cartIsCreated->isEmpty()) {

            $order = Order::create([
                'order_date' => $ldate,
                'order_price' => $medicine->value('medicine_price'),
                'order_status' => 'cart',
                'user_id' => $user->id,
            ]);
            
            $order = $user->order()->where([
                ['order_status', 'cart'],
            ])->firstOrFail();

            $order->medicines()->attach($medicine, ['quantity' => 1]);
            
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

            $order = $user->order()->where([
                ['order_status', 'cart'],
            ])->firstOrFail();

            if ($orderMedicine->isEmpty()) {
                // $order = $user->order()->where([
                //     ['order_status', 'cart'],
                // ])->firstOrFail();
                
                $order->medicines()->attach($medicine, ['quantity' => 1]);
                
                $orderPrice = $this->sumUpOrder();
                $price['order_price'] = $orderPrice;
                $order->update($price);

            } else {
                // $order = $user->order()->where([
                //     ['order_status', 'cart'],
                // ])->firstOrFail();
                // $quantity = $order->medicines()->where([
                //     ['order_medicine.medicine_id', $medId],
                // ])->first()->pivot->quantity;
                $quantity = $order->medicines()->firstWhere('order_medicine.medicine_id', $medId)->pivot->quantity;
                $order->medicines()->updateExistingPivot($medicine, ['quantity' => $quantity +1]);
                
                $orderPrice = $this->sumUpOrder();
                $price['order_price'] = $orderPrice;
                $order->update($price);

                return response()->json(['success' => true, 'message' => 'Medicine quantity updated in cart']);
                // return response()->json('Medicine already exist in cart, please add the quantity', 400);
            }
        }
        // echo $order;
        return response()->json(['success' => true, 'message' => 'Medicine added to cart successfully']);
    }

    public function updateQty(Request $request, $medId){
        $user = JWTAuth::user();
        $medicine = Medicine::where([
            ['medicine_id', $medId]
        ])->firstOrFail();

        $order = $user->order()->where([
            ['order_status', 'cart'],
        ])->firstOrFail();
        // $quantity = $order->medicines()->first()->pivot->quantity;
        $order->medicines()->updateExistingPivot($medicine, ['quantity' => $request->get('quantity')]);
        $orderPrice = $this->sumUpOrder();
        $price['order_price'] = $orderPrice;
        $order->update($price);
        return response()->json(['success' => true, 'message' => 'Medicine quantity updated in cart']);
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

    public function checkout($order_id)
    {
        // $user = JWTAuth::user();
        $ldate = date('Y-m-d');
        // $order = $user->order()->where([
        //     ['order_id', $order_id],
        // ])->firstOrFail();
        $order = Order::where([
            ['order_id', $order_id],
        ])->firstOrFail();

        $order->update([
            'order_date' => $ldate,
            'order_status' => 'checkout'
        ]);

        return response()->json(['success' => true, 'message' => 'Ordered successfully']);
    }

    public function updateStatus(Request $request, $order_id){
        $order = Order::where([
            ['order_id', $order_id],
        ])->firstOrFail();

        $order->update([
            'order_status' => $request->get('order_status')
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function sumUpOrder(){
        $cartMeds = $this->medicineInCart();
        $orderPrice = $cartMeds->sum('medTotalPrice');
        return $orderPrice;
    }

    public function sumUpQty(){
        $cartMeds = $this->medicineInCart();
        $totalQty = $cartMeds->sum('quantity');
        return $totalQty;
    }

    public function sumUpCheckoutOrderQty($order_id){
        $cartMeds = $this->medicineCheckout($order_id);
        $totalQty = $cartMeds->sum('quantity');
        return $totalQty;
    }
}
