<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenController extends Controller
{
    public function token()
    {
        try {

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => $this->encoder('UNIKTIF_MEDIA_INDONESIA')
            ]);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }

    public function encoder($data)
    {
        $secret = env('APP_KEY');
        $payload = [
            'iss' => "UNIKTIF",
            'sub' => $data,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24
        ];
        $encPayload = JWT::encode($payload, $secret, 'HS256');
        return $encPayload;
    }

    public function decoder($token)
    {
        $payload = JWT::decode($token, new Key(env('APP_KEY'), 'HS256'));
        $jsn = $payload->sub;
        return $jsn;
    }
}
