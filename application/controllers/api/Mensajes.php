<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mensajes extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mensajes_model');
        $this->load->library('upload');
        $this->output->set_content_type('application/json');
    }

    public function enviar()
    {

        $conversacion_id = $this->input->post('conversacion_id');
        $remitente_id = $this->input->post('remitente_id');
        $tipo = $this->input->post('tipo');
        $contenido = $this->input->post('contenido');
        $iv = $this->input->post('iv');

        if (!$conversacion_id || !$remitente_id) {
            return $this->error('Datos incompletos', 400);
        }

        $archivo_data = null;

        if (!empty($_FILES['archivo']['name'])) {
            
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
            $config['encrypt_name'] = TRUE;

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('archivo')) {
                return $this->error($this->upload->display_errors(), 400);
            }

            $archivo_data = $this->upload->data();
        }

        // 1. Guardar mensaje del usuario
        $resultado = $this->Mensajes_model->enviar_mensaje(
            $conversacion_id,
            $remitente_id,
            $tipo,
            $contenido,
            $iv,
            $archivo_data
        );

        if (!$resultado['success']) {
            return $this->error($resultado['error'], 500);
        }

        // 2. Detectar si es conversación con el bot
        $esBot = $this->Mensajes_model->es_conversacion_con_bot($conversacion_id);

        // 3. Si es conversación con el bot y el mensaje es texto, generar respuesta
        if ($esBot && $tipo === 'texto' && !empty(trim($contenido))) {
            $this->load->library('OpenAIService');

            $respuestaBot = $this->openaiservice->preguntar($contenido);

            $bot_id = '88888888-8888-8888-8888-888888888888';

            $this->Mensajes_model->enviar_mensaje(
                $conversacion_id,
                $bot_id,
                'texto',
                $respuestaBot,
                bin2hex(random_bytes(16)),
                null
            );
        }

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode($resultado));
    }

    private function error($msg,$code)
    {
        return $this->output
            ->set_status_header($code)
            ->set_output(json_encode(['error'=>$msg]));
    }

    public function conversacion($conversacion_id)
    {
        $mensajes = $this->Mensajes_model->obtener_mensajes($conversacion_id);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'data' => $mensajes
            ]));
    }
}