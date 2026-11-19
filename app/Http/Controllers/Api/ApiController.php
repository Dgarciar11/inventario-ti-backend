<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;

abstract class ApiController extends Controller
{
    protected function success(
        mixed $data = null,
        string $message = 'Operación realizada correctamente.',
        int $status = 200,
        array $meta = []
    ){
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function created(
        mixed $data = null,
        string $message = 'Registro creado correctamente.'
    ){
        return ApiResponse::created($data, $message);
    }

    protected function error(
        string $message = 'Ocurrió un error inesperado.',
        int $status = 500,
        array $errors = []
    ){
        return ApiResponse::error($message, $status, $errors);
    }

    protected function validationError(
        array $errors,
        string $message = 'Los datos enviados no son validos'
    ){
        return ApiResponse::validationError($errors, $message);
    }

    protected function unauthorized(
        string $message = 'No autenticado'
    ){
        return ApiResponse::unauthorized($message);
    }

    protected function forbidden(
        string $message = 'No tienes permisos para realizar esta acción.'
    ) {
        return ApiResponse::forbidden($message);
    }

    protected function notFound(
        string $message = 'El recurso solicitado no existe.'
    ) {
        return ApiResponse::notFound($message);
    }
}
