<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function existe_email($email)
    {
        return $this->db->where('email', $email)
                        ->count_all_results('usuario') > 0;
    }

    public function existe_telefono($telefono)
    {
        return $this->db->where('telefono', $telefono)
                        ->count_all_results('usuario') > 0;
    }

    public function existe_usuario($nombreUsuario)
    {
        return $this->db->where('nombre_usuario', $nombreUsuario)
                        ->count_all_results('usuario') > 0;
    }

    public function crear_usuario($input, $nombre_usuario, $password)
    {
        $this->db->trans_begin();

        try {
            if ($this->existe_usuario($nombre_usuario)) {
                return [
                    'success' => false,
                    'error' => 'El nombre de usuario ya está en uso'
                ];
            }

            if ($this->existe_email($input['email'])) {
                return [
                    'success' => false,
                    'error' => 'El correo ya está registrado'
                ];
            }

            if ($this->existe_telefono($input['telefono'])) {
                return [
                    'success' => false,
                    'error' => 'El teléfono ya está registrado'
                ];
            }

            $personaId = $this->generar_uuid();
            $usuarioId = $this->generar_uuid();

            $this->db->insert('personas', [
                'id' => $personaId,
                'nombre_completo' => $input['nombre_completo'],
                'fecha_nacimiento' => $input['fecha_nacimiento'] ?? null,
                'genero' => $input['genero'] ?? null,
                'pais' => $input['pais'] ?? null,
                'ciudad' => $input['ciudad'] ?? null
            ]);

            $this->db->insert('usuario', [
                'id' => $usuarioId,
                'persona_id' => $personaId,
                'nombre_usuario' => $nombre_usuario,
                'email' => $input['email'],
                'telefono' => $input['telefono'],
                'contrasena_hash' => password_hash($password, PASSWORD_DEFAULT),
                'llave_publica' => 'pendiente',
                'llave_privada_cifrada' => 'pendiente',
                'activo' => 1,
                'password_temporal' => 1
            ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Error al registrar usuario');
            }

            $this->db->trans_commit();

            return [
                'success' => true,
                'usuario_id' => $usuarioId,
                'nombre_usuario' => $nombre_usuario
            ];

        } catch (Exception $e) {
            $this->db->trans_rollback();

            return [
                'success' => false,
                'error' => 'No se pudo registrar el usuario'
            ];
        }
    }

    public function login($usuario, $password)
    {
        $sql = "
            SELECT 
                id,
                nombre_usuario,
                email,
                contrasena_hash,
                activo,
                password_temporal
            FROM usuario
            WHERE nombre_usuario = ?
               OR email = ?
            LIMIT 1
        ";

        $user = $this->db->query($sql, [$usuario, $usuario])->row();

        if (!$user) {
            return [
                'success' => false,
                'error' => 'Usuario no encontrado'
            ];
        }

        if ((int)$user->activo !== 1) {
            return [
                'success' => false,
                'error' => 'La cuenta está inactiva'
            ];
        }

        if (!password_verify($password, $user->contrasena_hash)) {
            return [
                'success' => false,
                'error' => 'Contraseña incorrecta'
            ];
        }

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function cambiar_password($usuario, $password_actual, $password_nueva)
    {
        $sql = "
            SELECT id, contrasena_hash
            FROM usuario
            WHERE nombre_usuario = ?
               OR email = ?
            LIMIT 1
        ";

        $user = $this->db->query($sql, [$usuario, $usuario])->row();

        if (!$user) {
            return [
                'success' => false,
                'error' => 'Usuario no encontrado'
            ];
        }

        if (!password_verify($password_actual, $user->contrasena_hash)) {
            return [
                'success' => false,
                'error' => 'La contraseña actual es incorrecta'
            ];
        }

        $this->db->where('id', $user->id);
        $ok = $this->db->update('usuario', [
            'contrasena_hash' => password_hash($password_nueva, PASSWORD_DEFAULT),
            'password_temporal' => 0
        ]);

        if (!$ok) {
            return [
                'success' => false,
                'error' => 'No se pudo actualizar la contraseña'
            ];
        }

        return [
            'success' => true
        ];
    }

    public function recuperar_password($email)
    {
        $user = $this->db->where('email', $email)->get('usuario')->row();

        if (!$user) {
            return [
                'success' => false,
                'error' => 'No existe una cuenta con ese correo'
            ];
        }

        $passwordTemporal = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$'), 0, 10);

        $this->db->where('id', $user->id);
        $ok = $this->db->update('usuario', [
            'contrasena_hash' => password_hash($passwordTemporal, PASSWORD_DEFAULT),
            'password_temporal' => 1
        ]);

        if (!$ok) {
            return [
                'success' => false,
                'error' => 'No se pudo generar una nueva contraseña'
            ];
        }

        return [
            'success' => true,
            'email' => $user->email,
            'usuario' => $user->nombre_usuario,
            'password_temporal' => $passwordTemporal
        ];
    }

    private function generar_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}