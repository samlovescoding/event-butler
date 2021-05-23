<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke()
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string|min:8|max:100',
            'password' => 'required|string|min:8|alpha_dash',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors(), 401);
        }

        $user = new User($validator->validated());

        $user->password = Hash::make($user->password);

        $user->save();

        return $this->success($user, "User was created successfully.");
    }
}
