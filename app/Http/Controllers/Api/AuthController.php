<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends ApiController
{
    // Valida las credenciales LDAP e inicia la sesión.
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

        // Evitamos continuar si el usuario solo contiene espacios.
        if ($username === '') {
            return $this->error(
                'Debes ingresar un usuario.',
                422
            );
        }

        // Leemos la conexión LDAP configurada.
        $connectionName = config('ldap.default', 'default');
        $ldapConfig = config("ldap.connections.{$connectionName}", []);

        $ldapHost = $ldapConfig['hosts'][0] ?? null;
        $ldapPort = (int) ($ldapConfig['port'] ?? 389);
        $baseDn = $ldapConfig['base_dn'] ?? null;

        // Comprobamos que exista la configuración necesaria.
        if (! $ldapHost || ! $baseDn) {
            return $this->error(
                'La conexión LDAP no está configurada correctamente.',
                500
            );
        }

        // Preparamos el usuario de red
        $ldapDn = 'CORP\\' . $username;

        // Abrimos la conexión con el servidor LDAP.
        $connection = @ldap_connect($ldapHost, $ldapPort);

        // Si no se pudo conectar, informamos el problema.
        if (! $connection) {
            return $this->error(
                'No fue posible conectar con el servidor LDAP.',
                502
            );
        }

        // Cerraremos la conexión aunque el proceso termine antes.
        try{
            // Configuramos la comunicación con Active Directory.
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

            // Limitamos la espera de la conexión.
            ldap_set_option(
                $connection,
                LDAP_OPT_NETWORK_TIMEOUT,
                (int)($ldapConfig['timeout'] ?? 5)
            );

            // Validamos el usuario y la contraseña.
            $bind = @ldap_bind($connection, $ldapDn, $password);

            if (! $bind){
                return $this->error(
                    'No fue posible validar las credenciales con Active Directory.',
                    401
                );
            }

            // Protegemos el usuario antes de incluirlo en la búsqueda.
            $safeUsername = ldap_escape(
                $username,
                '',
                LDAP_ESCAPE_FILTER
            );

            $filter = "(sAMAccountName={$safeUsername})";

            // Consultamos el usuario, correo y nombre.
            $search = @ldap_search(
                $connection,
                $baseDn,
                $filter,
                ['samaccountname', 'mail', 'cn']
            );

            if(! $search){
                return $this->error(
                    'No fue posible consultar el usuario en Active Directory.',
                    502
                );
            }

            // Leemos los resultados de la búsqueda.
            $entries = ldap_get_entries($connection, $search);

            if($entries===false || ($entries['count'] ?? 0) === 0){
                return $this->error(
                    'Usuario no encontrado en Active Directory.',
                    401
                );
            }

            // Extraemos los datos del usuario.
            $ldapUser = $entries[0];
            $ldapUsername = $ldapUser['samaccountname'][0] ?? $username;
            $ldapEmail = $ldapUser['mail'][0] ?? null;
            $ldapName = $ldapUser['cn'][0] ?? $username;

            // Comprobamos si está autorizado en nuestra aplicación.
            $user = User::query()
                ->where('ldap_username', $ldapUsername)
                ->first();

            if(! $user){
                return $this->error(
                    'El usuario no está autorizado para usar el sistema.',
                    403
                );
            }

            // Impedimos el ingreso de usuarios inactivos.
            if (! $user->is_active) {
                return $this->error(
                    'El usuario está inactivo, contacta al administrador.',
                    403
                );
            }

            // Actualizamos sus datos y registramos el acceso.
            $user->forceFill([
                'name' => $ldapName,
                'email' => $ldapEmail ?: $user->email,
                'last_login_at' => now(),
            ])->save();

            // Iniciamos sesión
            Auth::guard('web')->login($user);

            // Renovamos el identificador de la sesión.
            $request ->session()->regenerate();

            // Devolvemos el usuario, sin generar tokens de API.
            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'ldap_username' => $user->ldap_username,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                    'rol' => $user->rol,
                ],
            ], 'Inicio de sesión exitoso.');
        }finally{
            // Liberamos siempre la conexión LDAP.
            ldap_unbind($connection);
        }
    }

    // Devuelve el usuario autenticado.
    public function me(Request $request): JsonResponse
    {
        return $this->success([
            'user' => $request->user(),
        ]);
    }

    // Cierra la sesión del navegador.
    public function logout(Request $request): JsonResponse
    {
        // Quitamos la autenticación de la sesión actual.
        Auth::guard('web')->logout();

        // Borramos la sesión y renovamos su identificador.
        $request->session()->invalidate();

        // Renovamos la protección de los formularios.
        $request->session()->regenerateToken();

        return $this->success(
            [],
            'Sesión cerrada correctamente.'
        );
    }
}
