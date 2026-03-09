<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuarios_model');
        $this->output->set_content_type('application/json');
    }

    public function buscar()
    {
        $q = trim($this->input->get('q'));

        if (strlen($q) < 3) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'success' => false,
                    'error' => 'Debes escribir al menos 3 caracteres'
                ]));
        }
        
        $usuario_actual = $this->input->get('actual');
        $usuarios = $this->Usuarios_model->buscar_usuarios($q, $usuario_actual);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'data' => $usuarios
            ]));
    }
}