<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

    public function buscar_usuarios($termino, $usuario_actual = null)
    {
        $sql = "
            SELECT
                u.id AS usuario_id,
                u.nombre_usuario,
                u.email,
                p.nombre_completo,
                p.foto_url
            FROM usuario u
            JOIN personas p ON p.id = u.persona_id
            WHERE (
                u.nombre_usuario LIKE ?
                OR u.email LIKE ?
                OR p.nombre_completo LIKE ?
            )
            AND u.activo = 1
        ";

        $params = ["%$termino%", "%$termino%", "%$termino%"];

        if ($usuario_actual) {
            $sql .= " AND u.id != ?";
            $params[] = $usuario_actual;
        }

        $sql .= " ORDER BY u.nombre_usuario ASC LIMIT 20";

        return $this->db->query($sql, $params)->result();
    }
}