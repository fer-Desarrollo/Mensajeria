<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conversaciones extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Conversaciones_model');
        $this->output->set_content_type('application/json');
    }

    public function crear()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input || empty($input['creador_id'])) {
            return $this->error('Datos inválidos', 400);
        }

        $resultado = $this->Conversaciones_model->crear_conversacion($input);

        if (!$resultado['success']) {
            return $this->error($resultado['error'], 500);
        }

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'success' => true,
                'conversacion_id' => $resultado['conversacion_id']
            ]));
    }

    private function error($mensaje, $code)
    {
        return $this->output
            ->set_status_header($code)
            ->set_output(json_encode(['error' => $mensaje]));
    }

    public function listar($usuario_id)
    {
        $resultado = $this->Conversaciones_model->listar_conversaciones($usuario_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'data' => $resultado
            ]));
    }
}