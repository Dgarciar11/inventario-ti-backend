<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    // Inicia sesión validando al usuario contra Active Directory (LDAP).
    public function login(Request $request): JsonResponse
    {
        // El frontend debe enviar usuario y contraseña.
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Quitamos espacios accidentales del usuario.
        $username = trim($request->input('username'));
        $password = $request->input('password');

        // Usuario de red completo
        $ldapDn = 'CORP\\' . $username;

        // Datos de conexión LDAP desde .env.
        $ldapHost = config('ldap.connections.default.hosts.0', env('LDAP_HOST'));
        $ldapPort = env('LDAP_PORT', 389);

        // Abrimos la conexión con el servidor LDAP.
        $connection = @ldap_connect($ldapHost, $ldapPort);

        // Si no se pudo conectar, informamos el problema.
        if (! $connection) {
            return $this->error(
                'No fue posible conectar con el servidor LDAP.',
                502
            );
        }

        // Usamos LDAP versión 3 y evitamos redirecciones automáticas.
        ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

        // Validamos el usuario y la contraseña contra Active Directory.
        $bind = @ldap_bind($connection, $ldapDn, $password);

        // Si las credenciales no son válidas, no permitimos entrar.
        if (! $bind) {
            return $this->error(
                'Credenciales inválidas.',
                401
            );
        }

        // Buscamos al usuario dentro del dominio de la empresa.
        $baseDn = env('LDAP_BASE_DN');
        $filter = "(sAMAccountName={$username})";

        // Pedimos nombre, correo y usuario de red.
        $search = @ldap_search(
            $connection,
            $baseDn,
            $filter,
            ['samaccountname', 'mail', 'cn', 'objectguid']
        );

        // Si la búsqueda falla, cerramos la conexión y avisamos.
        if (! $search) {
            ldap_unbind($connection);

            return $this->error(
                'No fue posible consultar el usuario en Active Directory.',
                502
            );
        }

        // Leemos los resultados que devolvió Active Directory.
        $entries = ldap_get_entries($connection, $search);

        // Si AD no encontró al usuario, rechazamos el acceso.
        if (($entries['count'] ?? 0) === 0) {
            ldap_unbind($connection);

            return $this->error(
                'Usuario no encontrado en Active Directory.',
                401
            );
        }

        // Extraemos los datos principales del usuario.
        $ldapUser = $entries[0];
        $ldapUsername = $ldapUser['samaccountname'][0] ?? $username;
        $ldapEmail = $ldapUser['mail'][0] ?? null;
        $ldapName = $ldapUser['cn'][0] ?? $username;

        // Buscamos si el usuario fue autorizado previamente en la aplicación.
        $user = User::query()
            ->where('ldap_username', $ldapUsername)
            ->first();

        // LDAP dice quién es, pero la base define quién puede usar el sistema.
        if (! $user) {
            ldap_unbind($connection);

            return $this->error(
                'El usuario no está autorizado para usar el sistema.',
                403
            );
        }

        // Los usuarios inactivos no pueden iniciar sesión.
        if (! $user->is_active) {
            ldap_unbind($connection);

            return $this->error(
                'El usuario está inactivo.',
                403
            );
        }

        // Actualizamos los datos desde AD y registramos el último acceso.
        $user->forceFill([
            'name' => $ldapName,
            'email' => $ldapEmail ?: $user->email,
            'last_login_at' => now(),
        ])->save();

        // Creamos el token que usará el frontend para las demás peticiones.
        $token = $user->createToken('inventario-ti')->plainTextToken;

        // Cerramos la conexión LDAP.
        ldap_unbind($connection);

        // Devolvemos el token y los datos del usuario.
        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'ldap_username' => $user->ldap_username,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'rol' => $user->rol,
            ],
        ], 'Inicio de sesión exitoso.');
    }

    // Devuelve los datos del usuario que está autenticado con el token.
    public function me(Request $request): JsonResponse
    {
        return $this->success([
            'user' => $request->user(),
        ]);
    }

    // Elimina el token actual y cierra la sesión en este dispositivo.
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(
            [],
            'Sesión cerrada correctamente.'
        );
    }
}
