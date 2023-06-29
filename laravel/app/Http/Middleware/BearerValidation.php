<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BearerValidation
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request                                                                          $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();
        if (empty($bearer)) {
            return response()->json([
                'status' => false,
                'error' => 'Missing token',
            ]);
        }

        if (!$this->validateToken($bearer)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid token',
            ]);
        }

        return $next($request);
    }

    private function validateToken(string $token): bool
    {
        if (empty($token) || !preg_match('/[{}()\[\]]{2,}/', $token)) {
            return false;
        }
        $chars = str_split($token);
        $combinations = [
            '(' => ')',
            '{' => '}',
            '[' => ']',
        ];
        $openingChars = array_keys($combinations);
        $closingChars = array_values($combinations);

        $currentOpened = [];

        foreach ($chars as $char) {
            if (in_array($char, $openingChars, true)) {
                $currentOpened[] = $char;
                continue;
            }
            if (in_array($char, $closingChars, true)) {
                // Not opened
                if (empty($currentOpened)) {
                    return false;
                }
                // Check expected closing char
                if ($char !== $combinations[array_pop($currentOpened)]) {
                    return false;
                }
            }
        }
        return empty($currentOpened);
    }
}
