<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mensajes_model extends CI_Model {

    public function enviar_mensaje($conversacion_id,$remitente_id,$tipo,$contenido,$iv,$archivo)
    {

        $this->db->trans_begin();

        try {

            $mensaje_id = $this->generar_uuid();

            $this->db->insert('mensajes',[
                'id'=>$mensaje_id,
                'conversacion_id'=>$conversacion_id,
                'remitente_id'=>$remitente_id,
                'tipo'=>$tipo,
                'contenido_cifrado'=>$contenido,
                'iv'=>$iv
            ]);

            if($archivo){

                $archivo_id = $this->generar_uuid();

                $this->db->insert('archivos',[
                    'id'=>$archivo_id,
                    'mensaje_id'=>$mensaje_id,
                    'nombre_original'=>$archivo['orig_name'],
                    'tipo_mime'=>$archivo['file_type'],
                    'tamano_bytes'=>$archivo['file_size'],
                    'storage_key'=>$archivo['file_name'],
                    'clave_cifrado'=>bin2hex(random_bytes(32)),
                    'iv_archivo'=>bin2hex(random_bytes(16))
                ]);

            }

            if ($this->db->trans_status() === FALSE){
                throw new Exception();
            }

            $this->db->trans_commit();

            return [
                'success'=>true,
                'mensaje_id'=>$mensaje_id
            ];

        } catch(Exception $e){

            $this->db->trans_rollback();

            return [
                'success'=>false,
                'error'=>'Error enviando mensaje'
            ];
        }

    }

    private function generar_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0,0xffff),mt_rand(0,0xffff),
            mt_rand(0,0xffff),
            mt_rand(0,0x0fff)|0x4000,
            mt_rand(0,0x3fff)|0x8000,
            mt_rand(0,0xffff),mt_rand(0,0xffff),mt_rand(0,0xffff)
        );
    }

}