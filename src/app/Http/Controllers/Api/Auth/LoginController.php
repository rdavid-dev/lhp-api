<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ResponseWithHttpStatusController;

class LoginController extends ResponseWithHttpStatusController
{
    public function __invoke(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->responseUnProcess('Invalid Credentials');
        }

        $accessToken = $user->createToken("lhp-token-{$user->id}")->plainTextToken;

        return $this->respond([
            'user'  => $user,
            'token' => $accessToken
        ],'Authenticated');
    }
}
