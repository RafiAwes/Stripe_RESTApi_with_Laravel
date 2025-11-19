<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class TransferController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

  
    public function releaseToSupplier(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $supplier = Supplier::findOrFail($order->supplier_id);

        
        $total = (int) round($order->total_amount * 100);
        $platformFee = (int) round($order->platform_fee * 100); // ensure platform_fee saved earlier
        $supplierShare = $total - $platformFee;

        if ($supplierShare <= 0) {
            return response()->json(['success' => false, 'message' => 'No funds to transfer'], 400);
        }

        $idempotencyKey = 'transfer_order_' . $order->id;

        try {
            
            $transfer = $this->stripe->createTransfer(
                $supplierShare,
                $order->currency,
                $supplier->stripe_account_id,
                ['order_id' => $order->id, 'payment_intent_id' => $order->payment_intent_id],
                $idempotencyKey
            );

          
            $t = Transfer::create([
                'transfer_id' => $transfer->id,
                'supplier_id' => $supplier->id,
                'order_id' => $order->id,
                'payment_intent_id' => $order->payment_intent_id ?? null,
                'amount' => $supplierShare,
                'currency' => $order->currency,
                'status' => 'succeeded',
                'raw_response' => $transfer->toArray(),
            ]);


            return response()->json(['success' => true, 'transfer' => $transfer]);
        } catch (Exception $e) {
            Transfer::create([
                'transfer_id' => Str::uuid()->toString(),
                'supplier_id' => $supplier->id,
                'order_id' => $order->id,
                'amount' => $supplierShare,
                'currency' => $order->currency,
                'status' => 'failed',
                'raw_response' => ['error' => $e->getMessage()],
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
