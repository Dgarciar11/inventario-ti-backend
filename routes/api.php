<?php


use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Ruta pública para iniciar sesión con LDAP.
// No requiere token porque el usuario aún no ha entrado.
Route::post('/login', [AuthController::class, 'login']);

// Estas rutas solo funcionan si se envía un token Sanctum válido.
Route::middleware('auth:sanctum')->group(function () {

    // Cierra la sesión eliminando el token actual.
    Route::post('/logout', [AuthController::class, 'logout']);

    // Devuelve los datos del usuario autenticado.
    Route::get('/me', [AuthController::class, 'me']);
});
