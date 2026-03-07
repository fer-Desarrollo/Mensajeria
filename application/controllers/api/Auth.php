<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library(['email']);
        $this->output->set_content_type('application/json');
    }

    public function registro()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->error('JSON inválido', 400);
        }

        if (empty($input['nombre_completo']) || empty($input['email']) || empty($input['telefono'])) {
            return $this->error('Campos requeridos faltantes', 400);
        }

        if ($this->Auth_model->existe_email($input['email'])) {
            return $this->error('Email ya registrado', 409);
        }

        if ($this->Auth_model->existe_telefono($input['telefono'])) {
            return $this->error('Teléfono ya registrado', 409);
        }

        $nombre_usuario = !empty($input['nombre_usuario'])
            ? $input['nombre_usuario']
            : strtolower(str_replace(' ', '', $input['nombre_completo'])) . rand(100,999);

        $password = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$'), 0, 10);

        $resultado = $this->Auth_model->crear_usuario(
            $input,
            $nombre_usuario,
            $password
        );

        if (!$resultado['success']) {
            return $this->error($resultado['error'], 500);
        }

        $this->email->from('daysgone8336@gmail.com', 'Mensajeria App');
        $this->email->to($input['email']);
        $this->email->subject('Credenciales de acceso');

        $this->email->message("
            <h3>Bienvenido {$input['nombre_completo']}</h3>
            <p><b>Usuario:</b> {$nombre_usuario}</p>
            <p><b>Contraseña temporal:</b> {$password}</p>
            <p>Debes cambiarla al iniciar sesión.</p>
        ");

        if (!$this->email->send()) {
            return $this->error('Usuario creado pero email no enviado', 500);
        }

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Usuario creado y correo enviado'
            ]));
    }

    private function error($mensaje, $code)
    {
        return $this->output
            ->set_status_header($code)
            ->set_output(json_encode(['error' => $mensaje]));
    }

    public function cambiar_password()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->error('JSON inválido', 400);
        }

        if (empty($input['usuario']) || empty($input['password_actual']) || empty($input['password_nueva'])) {
            return $this->error('Datos incompletos', 400);
        }

        if (strlen($input['password_nueva']) < 8) {
            return $this->error('La contraseña debe tener mínimo 8 caracteres', 400);
        }

        $resultado = $this->Auth_model->cambiar_password(
            $input['usuario'],
            $input['password_actual'],
            $input['password_nueva']
        );

        if (!$resultado['success']) {
            return $this->error($resultado['error'], 401);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]));
    }

    public function login()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->error('JSON inválido', 400);
        }

        if (empty($input['usuario']) || empty($input['password'])) {
            return $this->error('Datos incompletos', 400);
        }

        $resultado = $this->Auth_model->login(
            $input['usuario'],
            $input['password']
        );

        if (!$resultado['success']) {
            return $this->error($resultado['error'], 401);
        }

        $user = $resultado['user'];

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'usuario_id' => $user->id,
                'nombre_usuario' => $user->nombre_usuario,
                'password_temporal' => (bool)$user->password_temporal,
                'message' => $user->password_temporal 
                    ? 'Debe cambiar su contraseña temporal'
                    : 'Login exitoso'
            ]));
    }
} 