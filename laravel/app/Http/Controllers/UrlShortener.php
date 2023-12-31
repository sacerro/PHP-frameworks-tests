<?php

namespace App\Http\Controllers;

use App\Http\Data\TinyUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class UrlShortener extends Controller
{
    public function shortUrl( Request $request ): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ( $validator->fails() ) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->getMessages(),
            ]);
        }

        $tinyUrlShortener = TinyUrl::shortUrl( $request->get('url') );
        return response()->json([
            'url' => $tinyUrlShortener,
        ]);
    }
}
