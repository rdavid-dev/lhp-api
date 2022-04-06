<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Controllers\ResponseWithHttpStatusController;

class RegisterController extends ResponseWithHttpStatusController
{
    /**
     * Create user
     *
     * @param RegisterUserRequest $request
     * @return void
     */
    public function __invoke(RegisterUserRequest $request)
    {
        $user = User::create([
            'name'  => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => bcrypt($request->password)
        ]);

        return $this->responseCreated($user->toArray(), 'Registration');
    }
}
