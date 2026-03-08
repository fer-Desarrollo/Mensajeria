<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conversaciones_model extends CI_Model {

    public function crear_conversacion($input)
    {
        $this->db->trans_begin();

        try {

            $conversacion_id = $this->generar_uuid();

            $es_grupo = !empty($input['nombre_grupo']) ? 1 : 0;

            // Crear conversación
            $this->db->insert('conversaciones', [
                'id' => $conversacion_id,
                'es_grupo' => $es_grupo,
                'nombre_grupo' => $input['nombre_grupo'] ?? null,
                'creador_id' => $input['creador_id']
            ]);

            // Agregar creador como admin
            $this->db->insert('participantes', [
                'conversacion_id' => $conversacion_id,
                'usuario_id' => $input['creador_id'],
                'es_admin' => 1
            ]);

            // Agregar participantes
            if (!empty($input['participantes'])) {

                foreach ($input['participantes'] as $usuario_id) {

                    $this->db->insert('participantes', [
                        'conversacion_id' => $conversacion_id,
                        'usuario_id' => $usuario_id,
                        'es_admin' => 0
                    ]);

                }

            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception();
            }

            $this->db->trans_commit();

            return [
                'success' => true,
                'conversacion_id' => $conversacion_id
            ];

        } catch (Exception $e) {

            $this->db->trans_rollback();

            return [
                'success' => false,
                'error' => 'Error creando conversación'
            ];
        }
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
    public function listar_conversaciones($usuario_id)
    {
        $sql = "
            SELECT 
                c.id as conversacion_id,
                c.es_grupo,
                c.nombre_grupo,
                c.foto_grupo_url,
                m.contenido_cifrado as ultimo_mensaje,
                m.fecha_envio
            FROM participantes p
            JOIN conversaciones c ON c.id = p.conversacion_id
            LEFT JOIN mensajes m ON m.id = (
                SELECT id
                FROM mensajes
                WHERE conversacion_id = c.id
                ORDER BY fecha_envio DESC
                LIMIT 1
            )
            WHERE p.usuario_id = ?
            ORDER BY m.fecha_envio DESC
        ";

        return $this->db->query($sql, [$usuario_id])->result();
    }

    public function obtener_mensajes($conversacion_id)
{
    $sql = "
        SELECT 
            m.id as mensaje_id,
            m.conversacion_id,
            m.remitente_id,
            u.nombre_usuario,
            p.nombre_completo,
            m.tipo,
            m.contenido_cifrado,
            m.iv,
            m.fecha_envio,

            a.id as archivo_id,
            a.nombre_original,
            a.tipo_mime,
            a.tamano_bytes,
            a.storage_key,
            a.miniatura_url

        FROM mensajes m

        JOIN usuario u ON u.id = m.remitente_id
        JOIN personas p ON p.id = u.persona_id

        LEFT JOIN archivos a ON a.mensaje_id = m.id

        WHERE m.conversacion_id = ?

        ORDER BY m.fecha_envio ASC
    ";

    return $this->db->query($sql, [$conversacion_id])->result();
}
}