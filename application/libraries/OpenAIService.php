<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpenAIService {

    private $api_key;

    public function __construct()
    {
        // La API key se toma de una variable de entorno
        $this->api_key = getenv('OPENAI_API_KEY');

        if (!$this->api_key) {
            log_message('error', 'OpenAI API key no configurada.');
        }
    }

    public function preguntar($mensaje)
    {
        if (!$this->api_key) {
            return "El asistente no está configurado correctamente.";
        }

        $data = [
            "model" => "gpt-4.1-mini",
            "input" => $mensaje
        ];

        $ch = curl_init("https://api.openai.com/v1/responses");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api_key
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if ($response === false) {
            log_message('error', 'OpenAI CURL error: ' . curl_error($ch));
            return "No pude responder en este momento.";
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            log_message('error', 'OpenAI API error: ' . $result['error']['message']);
            return "El asistente no pudo responder.";
        }

        return $result['output'][0]['content'][0]['text'] ?? "No pude generar respuesta.";
    }
}