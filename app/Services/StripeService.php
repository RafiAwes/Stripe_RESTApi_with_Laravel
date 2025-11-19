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
            $account = new Account();
            $account->type = 'express';
            $account->email = $accountData['email'];
            $account->save();
            return $account;
            
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function createAccountLink(string $accountId, string $refreshUrl, string $returnUrl): ?AccountLink 
    {
        try {
            $accountLink = new AccountLink();
            $accountLink->account = $accountId;
            $accountLink->refresh_url = $refreshUrl;
            $accountLink->return_url = $returnUrl;
            $accountLink->type = 'account_onboarding';
            $accountLink->save();
            return $accountLink;
        }
        catch (ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        }
    }

    public function createPaymentIntent(int $amount, string $currency, array $metadata, string $idempotencyKey): ?PaymentIntent 
    {
        try {
            $paymentIntent = new PaymentIntent();
            $paymentIntent->amount = $amount;
            $paymentIntent->currency = $currency;
            $paymentIntent->payment_method_types = $paymentMethodTypes;
            $paymentIntent->metadata = $metadata;
            $paymentIntent->idempotency_key = $idempotencyKey;
            $paymentIntent->save();
            return $paymentIntent;
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