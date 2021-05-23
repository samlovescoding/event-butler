<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data, $message = null, $code = 200)
    {
        if ($message == null) {
            $response = compact("data");
        } else {
            $response = compact("data", "message");
        }
        return response()->json($response, $code);
    }

    public function fail($errors, $code = 500)
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }
        return response()->json(["errors" => $errors], $code);
    }

}
