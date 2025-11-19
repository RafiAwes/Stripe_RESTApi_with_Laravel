<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\StripeService;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    protected $stripe;
    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function loginUser(Request $request)
    {

        $user = User::where('email',$request->email)->first();
        if ($user) {
            // token for login
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        }
        else {
            return $this->registerUser($request);
        }  
    }

    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
            // 'stripe_account_id' => 'required|string',
        ]);

        $password = hash::make($request->input('password'));

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $password;
        $user->save();

        // token for login
        $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function createAccount(Request $request)
    {
        response()->json(['message' => 'Creating Stripe connected account'], 200);
        $account = $this->stripe->createConnectedAccount([
            'email' => $request->input('email'),
        ]);

        $user = auth()->user()->id;
        if ($user) {
            $user->stripe_account_id = $account->id;
            $user->save();
        }

        $returnUrl = config('app.url') . '/supplier/onboarding/complete';
        $refreshUrl = config('app.url') . '/supplier/onboarding/refresh';

        $accountLink = $this->stripe->createAccountLink(
            $account->id,
            $refreshUrl,
            $returnUrl
        );

        return response()->json([
            'user' => $user,
            'onboarding_url' => $accountLink->url,
         ], 201);
    }

    

}
