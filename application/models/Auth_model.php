<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function crear_usuario($input, $nombre_usuario, $password)
    {
        $this->db->trans_begin();

        try {

            $persona_id = $this->generar_uuid();
            $usuario_id = $this->generar_uuid();

            // Crear persona
            $this->db->insert('personas', [
                'id'              => $persona_id,
                'nombre_completo' => $input['nombre_completo'],
                'fecha_nacimiento'=> $input['fecha_nacimiento'] ?? null,
                'genero'          => $input['genero'] ?? null,
                'pais'            => $input['pais'] ?? null,
                'ciudad'          => $input['ciudad'] ?? null
            ]);

            // Crear usuario
            $this->db->insert('usuario', [
                'id'                    => $usuario_id,
                'persona_id'            => $persona_id,
                'nombre_usuario'        => $nombre_usuario,
                'email'                 => $input['email'],
                'telefono'              => $input['telefono'],
                'contrasena_hash'       => password_hash($password, PASSWORD_BCRYPT),
                'llave_publica'         => bin2hex(random_bytes(32)),
                'llave_privada_cifrada' => bin2hex(random_bytes(64)),
                'activo'                => 1
            ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception();
            }

            $this->db->trans_commit();

            return ['success' => true];

        } catch (Exception $e) {

            $this->db->trans_rollback();

            return [
                'success' => false,
                'error' => 'Error al crear usuario'
            ];
        }
    }

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

    public function cambiar_password($usuario, $password_actual, $password_nueva)
    {
        $user = $this->db
            ->where('nombre_usuario', $usuario)
            ->get('usuario')
            ->row();

        if (!$user) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        if (!password_verify($password_actual, $user->contrasena_hash)) {
            return ['success' => false, 'error' => 'Contraseña actual incorrecta'];
        }

        $nuevo_hash = password_hash($password_nueva, PASSWORD_BCRYPT);

        $this->db->where('id', $user->id);
        $this->db->update('usuario', [
            'contrasena_hash' => $nuevo_hash,
            'password_temporal' => 0
        ]);

        return ['success' => true];
    }


    public function login($usuario, $password)
    {
        $user = $this->db
            ->group_start()
            ->where('nombre_usuario', $usuario)
            ->or_where('email', $usuario)
            ->or_where('telefono', $usuario)
            ->group_end()
            ->get('usuario')
            ->row();

        if (!$user) {
            return ['success' => false, 'error' => 'Usuario no encontrado'];
        }

        if (!password_verify($password, $user->contrasena_hash)) {
            return ['success' => false, 'error' => 'Contraseña incorrecta'];
        }

        // Actualizar estado del usuario
        $this->db->where('id', $user->id);
        $this->db->update('usuario', [
            'en_linea' => 1,
            'ultima_conexion' => date('Y-m-d H:i:s')
        ]);

        return [
            'success' => true,
            'user' => $user
        ];
    }
}