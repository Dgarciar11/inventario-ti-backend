<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Operacion realizada correctamente',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)){
            $response ['meta'] = $meta;
        }

        return response() -> json ($response, $status);
    }

    public static function created(
        mixed $data = null,
        string $message = 'Registro creado correctamente.'
    ): JsonResponse{
        return self::success($data, $message, 201);
    }

    public static function error(
        string $message = 'Ocurrio un error inesperado',
        int $status = 500,
        array $errors = []
    ):JsonResponse{
        $response =[
            'success' => false,
            'message' => $message
        ];

        if(!empty($errors)){
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    public static function validationError(
        array $errors,
        string $message = 'Los datos enviados no son validos',
    ) : JsonResponse{
        return self::error($message, 422, $errors);
    }

    public static function unauthorized(
        string $message = 'No autenticado'
    ): JsonResponse{
        return self::error($message, 401);
    }

    public static function forbidden(
        string $message = 'No tienes permisos para realizar esta acción.'
    ):JsonResponse{
        return self::error($message, 403);
    }

    public static function notFound(
        string $message = 'El recurso solicitado no existe.'
    ):JsonResponse{
        return self::error($message, 404);
    }
}
