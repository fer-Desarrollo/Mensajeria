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
                c.id AS conversacion_id,
                c.es_grupo,
                c.nombre_grupo,
                c.foto_grupo_url,
                m.contenido_cifrado AS ultimo_mensaje,
                m.fecha_envio,
                u_otro.id AS participante_id,
                u_otro.nombre_usuario AS nombre_participante,
                per_otro.nombre_completo AS nombre_completo_participante
            FROM participantes p_actual
            JOIN conversaciones c 
                ON c.id = p_actual.conversacion_id

            LEFT JOIN participantes part_otro
                ON part_otro.conversacion_id = c.id
                AND part_otro.usuario_id != p_actual.usuario_id
                AND c.es_grupo = 0

            LEFT JOIN usuario u_otro
                ON u_otro.id = part_otro.usuario_id

            LEFT JOIN personas per_otro
                ON per_otro.id = u_otro.persona_id

            LEFT JOIN mensajes m
                ON m.id = (
                    SELECT m2.id
                    FROM mensajes m2
                    WHERE m2.conversacion_id = c.id
                    ORDER BY m2.fecha_envio DESC
                    LIMIT 1
                )

            WHERE p_actual.usuario_id = ?
            ORDER BY 
                CASE WHEN m.fecha_envio IS NULL THEN 1 ELSE 0 END,
                m.fecha_envio DESC,
                c.fecha_creacion DESC
        ";

        return $this->db->query($sql, [$usuario_id])->result();
    }
}