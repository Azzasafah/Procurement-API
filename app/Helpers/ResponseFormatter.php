<?php

namespace App\Helpers;

class ResponseFormatter
{
    protected static $response = [
        'meta' => [
            'code'    => 200,
            'status'  => 'success',
            'message' => null,
        ],
        'result' => null,
    ];

    // success response
    public static function success($data = null, $message = null, $code = 200)
    {
        self::$response['meta']['code']    = $code;
        self::$response['meta']['status']  = 'success';
        self::$response['meta']['message'] = $message;
        self::$response['result']          = $data;

        return response()->json(self::$response, self::$response['meta']['code']);
    }

    // error response
    public static function error($message = null, $code = 400)
    {
        self::$response['meta']['code']    = $code;
        self::$response['meta']['status']  = 'error';
        self::$response['meta']['message'] = $message;
        self::$response['result']          = null;

        return response()->json(self::$response, self::$response['meta']['code']);
    }
}
