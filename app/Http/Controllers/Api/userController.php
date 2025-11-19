<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\StripeService;
use Laravel\Sanctum\HasApiTokens;
use App\Services\PasswordService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    protected $stripe;
    protected $passwordService;
    public function __construct(StripeService $stripe, PasswordService $passwordService)
    {
        $this->stripe = $stripe;
        $this->passwordService = $passwordService;
    }

    // login 
    public function loginUser(Request $request)
    {

        $user = User::where('email',$request->email)->first();
        $pass_verify = $this->passwordService->verifyPassword($request->input('password'), $user->password ?? '');
        if ($user && $pass_verify) {
            // token for login
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $token], 200);
        }
        else {
            return $this->registerUser($request);
        }  
    }

    // register
    public function registerUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        $password = $this->passwordService->hashPassword($request->input('password'));

        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $password;
        $user->save();

        // token for login
        $token = $user->createToken('auth_token')->plainTextToken;

       return response()->json(['user' => $user, 'token' => $token], 201);
    }

    // create stripe account
    public function createAccount(Request $request)
    {
        response()->json(['message' => 'Creating Stripe connected account'], 200);
        $account = $this->stripe->createConnectedAccount([
            'email' => $request->input('email'),
        ]);

        $user = auth()->user();
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
