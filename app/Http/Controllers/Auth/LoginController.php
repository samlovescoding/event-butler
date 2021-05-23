<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __invoke()
    {
        $validator = Validator::make(request()->all(), [
            "email" => "required|email",
            "password" => "required|string|min:8",
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 401);
        }

        $validated = $validator->validated();

        if (!$token = Auth::attempt($validated)) {
            return $this->fail("Invalid email/password combination", 401);
        }

        return $this->success(compact("token"), "User is authenticated.");

    }
}
