<?php

namespace App\Http\Traits;


trait ResponseTrait
{

    public function jsonResponse($data, $message, $status) {
        $array = [
            'data'  =>$data,
            'message'=>$message
        ];

        return response()->json($array, $status);
    }
}
