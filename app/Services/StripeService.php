<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use App\Models\Transfer;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Exception;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }

    public function createConnectedAccount(array $accountData): ?Account 
    {
        try {
            $account = Account::Create([
                'type' => 'express',
                'email' => $accountData['email'],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);;
            return $account;
            
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function createAccountLink(string $accountId, string $refreshUrl, string $returnUrl): ?AccountLink 
    {
        try {
            $accountLink = AccountLink::create([
                'account' => $accountId,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
            ]);
            return $accountLink;
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function createPaymentIntent(int $amount, string $currency, array $metadata, string $idempotencyKey): ?PaymentIntent 
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['card'],
                'metadata' => $metadata,
            ], [
                'idempotency_key' => $idempotencyKey,
            ]);
            // $paymentIntent = new PaymentIntent();
            // $paymentIntent->amount = $amount;
            // $paymentIntent->currency = $currency;
            // $paymentIntent->payment_method_types = $paymentMethodTypes;
            // $paymentIntent->metadata = $metadata;
            // $paymentIntent->idempotency_key = $idempotencyKey;
            // $paymentIntent->save();
            // return $paymentIntent;
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function createTransfer(int $amount, string $currency, string $destinationAccountId, string $sourceTransactionId, array $metadata, string $idempotencyKey): ?\Stripe\Transfer 
    {
        try {
            $transfer = Transfer::create([
                'amount' => $amount,
                'currency' => $currency,
                'destination' => $destinationAccountId,
                'source_transaction' => $sourceTransactionId,
                'metadata' => $metadata,
            ], [
                'idempotency_key' => $idempotencyKey,
            ]);
            return $transfer;
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }


}