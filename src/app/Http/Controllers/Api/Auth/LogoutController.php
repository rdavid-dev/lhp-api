<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\ResponseWithHttpStatusController;
use Illuminate\Http\Request;

class LogoutController extends ResponseWithHttpStatusController
{
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->responseNoContent();
    }
}
