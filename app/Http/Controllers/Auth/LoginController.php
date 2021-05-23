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

        // $user = User::where("email", $validated["email"]);

        // if ($user == null) {
        //     return $this->fail(new \Error("Email does not exist."), 401);
        // }

        // $passwordVerified = Hash::check($validated["password"], $user->password);

        // if (!$passwordVerified) {
        //     return $this->fail(new \Error("Password is incorrect"), 401);
        // }

        if (!$token = Auth::attempt($validated)) {
            return $this->fail(new \Error("Unauthorized"), 401);
        }

        return $this->success(compact("token"), "User is authenticated.");

    }
}
