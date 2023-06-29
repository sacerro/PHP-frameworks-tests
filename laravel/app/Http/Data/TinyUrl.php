<?php

namespace App\Http\Data;

use Illuminate\Support\Facades\Http;

class TinyUrl
{
    public static function shortUrl( string $url ): string {
        $response = Http::get( sprintf('https://tinyurl.com/api-create.php?url=%s', $url) );
        return $response->body();
    }
}
