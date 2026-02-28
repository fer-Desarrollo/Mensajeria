create database messenger;
use messenger;

CREATE TABLE personas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombres VARCHAR(120) NOT NULL,
  apellidos VARCHAR(120) NOT NULL,
  telefono VARCHAR(30) NULL,
  fecha_nacimiento DATE NULL,
  foto_perfil_url TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_personas_telefono (telefono)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  persona_id BIGINT UNSIGNED NOT NULL,
  username VARCHAR(60) NOT NULL,
  email VARCHAR(160) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  estado ENUM('activo','bloqueado') NOT NULL DEFAULT 'activo',
  ultimo_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_persona_id (persona_id),
  UNIQUE KEY uk_usuarios_username (username),
  UNIQUE KEY uk_usuarios_email (email),
  CONSTRAINT fk_usuarios_personas
    FOREIGN KEY (persona_id) REFERENCES personas(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE conversaciones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  tipo ENUM('directa','grupo') NOT NULL,
  nombre VARCHAR(120) NULL,
  created_by_usuario_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_conversaciones_tipo (tipo),
  KEY idx_conversaciones_creador (created_by_usuario_id),
  CONSTRAINT fk_conversaciones_creador
    FOREIGN KEY (created_by_usuario_id) REFERENCES usuarios(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE conversacion_participantes (
  conversacion_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  rol ENUM('admin','miembro') NOT NULL DEFAULT 'miembro',
  joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (conversacion_id, usuario_id),
  KEY idx_cp_usuario (usuario_id),
  KEY idx_cp_conversacion (conversacion_id),
  CONSTRAINT fk_cp_conversacion
    FOREIGN KEY (conversacion_id) REFERENCES conversaciones(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_cp_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mensajes (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  conversacion_id BIGINT UNSIGNED NOT NULL,
  remitente_usuario_id BIGINT UNSIGNED NOT NULL,
  texto TEXT NULL,
  reply_to_mensaje_id BIGINT UNSIGNED NULL,
  estado ENUM('enviado','entregado','leido','eliminado') NOT NULL DEFAULT 'enviado',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_mensajes_conversacion_fecha (conversacion_id, created_at),
  KEY idx_mensajes_remitente_fecha (remitente_usuario_id, created_at),
  KEY idx_mensajes_reply (reply_to_mensaje_id),
  CONSTRAINT fk_mensajes_conversacion
    FOREIGN KEY (conversacion_id) REFERENCES conversaciones(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_mensajes_remitente
    FOREIGN KEY (remitente_usuario_id) REFERENCES usuarios(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT fk_mensajes_reply
    FOREIGN KEY (reply_to_mensaje_id) REFERENCES mensajes(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mensaje_adjuntos (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  mensaje_id BIGINT UNSIGNED NOT NULL,
  tipo ENUM('imagen','audio','video') NOT NULL,
  url TEXT NOT NULL,
  mime_type VARCHAR(100) NULL,
  size_bytes BIGINT UNSIGNED NULL,
  -- solo para audio/video
  duracion_segundos INT UNSIGNED NULL,
  -- solo para imagen/video
  ancho INT UNSIGNED NULL,
  alto  INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_adjuntos_mensaje (mensaje_id),
  KEY idx_adjuntos_tipo (tipo),
  CONSTRAINT fk_adjuntos_mensaje
    FOREIGN KEY (mensaje_id) REFERENCES mensajes(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mensaje_estados (
  mensaje_id BIGINT UNSIGNED NOT NULL,
  usuario_id BIGINT UNSIGNED NOT NULL,
  entregado_at DATETIME NULL,
  leido_at DATETIME NULL,
  PRIMARY KEY (mensaje_id, usuario_id),
  KEY idx_me_usuario (usuario_id),
  KEY idx_me_leido (usuario_id, leido_at),
  CONSTRAINT fk_me_mensaje
    FOREIGN KEY (mensaje_id) REFERENCES mensajes(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT fk_me_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE OR REPLACE VIEW vw_conversaciones_ultimo_mensaje AS
SELECT
  c.id AS conversacion_id,
  c.tipo,
  c.nombre,
  c.created_at AS conversacion_creada,
  m.id AS ultimo_mensaje_id,
  m.created_at AS ultimo_mensaje_fecha,
  m.texto AS ultimo_mensaje_texto,
  m.remitente_usuario_id
FROM conversaciones c
LEFT JOIN mensajes m
  ON m.id = (
    SELECT m2.id
    FROM mensajes m2
    WHERE m2.conversacion_id = c.id
    ORDER BY m2.created_at DESC, m2.id DESC
    LIMIT 1
  );
