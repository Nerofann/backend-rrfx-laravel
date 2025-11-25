<?php
namespace App\Support;

use Illuminate\Http\Response;

class ApiResponse
{
    protected static function statusText(int $statusCode): ?string 
    {
        $text = Response::$statusTexts[ $statusCode ] ?? null;
        return $text 
            ? strtoupper(str_replace(' ', '_', $text)) 
            : null;
    }

    public static function success(string $message = 'Success', array $data = [], int $code = 200)
    {
        return response()
            ->json([
                'message' => $message,
                'data' => $data
            ], 
            $code
        );
    }

    public static function error(string $message = '', array $errors = [], int $code = 400)
    {
        return response()
            ->json([
                'message' => $message ?: self::statusText($code),
                'errors' => $errors ?: (object)[]
            ], 
            $code
        );
    }

    public static function validationError(string $message = 'Validation Error', array $errors = [], int $code = 422)
    {
        return response()
            ->json([
                'message' => $message ?? self::statusText($code),
                'errors' => $errors
            ], 
            $code
        );
    }
}