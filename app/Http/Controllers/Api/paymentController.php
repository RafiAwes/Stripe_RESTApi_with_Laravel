<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use App\Services\StripeService;
use Stripe\Stripe;

class paymentController extends BaseController
{
    protected $stripe;
    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }
    public function create(Request $request)
    {
        // validating
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'currency' => 'required|string|size:3',
        ]);

        $order = Order::findOrFail($request->order_id);
        $amount = $order->amount;
        $idempotencyKey = 'order_' . $order->id . '_payment';

        try {
            $intent = $this->stripe->createPaymentIntent(
                $amount,
                $request->currency,
                ['order_id' => $order->id],
                $idempotencyKey
            );

            $payment = new Payment();
            $payment->pay_intent_id = $intent->id;
            $payment->client_secret = $intent->client_secret;
            $payment->amount = $amount;
            $payment->currency = strtolower($request->currency);
            $payment->order_id = $order->id;
            $payment->status = 'pending';
            $payment->raw_response = $intent->toArray();
            $payment->save();

            return response()->json([
                'payment' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Payment Intent creation failed: ' . $e->getMessage()
            ], 500);
        }

    }

    public function confirm (Request $request){
        $request->validate([
            'payment_intend_id' => 'required|string',
        ]);
         
        $paymentIntent = PaymentIntent::retrive($request->payment_intend_id);

        $paymentIntent->confirm();

        return json([
            'status' => $paymentIntent->status,
            'message' => 'Payment confirmed successfully',
        ]);
    }
    
}

  

