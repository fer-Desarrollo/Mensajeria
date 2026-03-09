<?php include APPPATH . 'views/templates/header.php'; ?>

<?php
if (!isset($_SESSION['usuario_id']) && !isset($_COOKIE['usuario_id'])) {
    header('Location: /mensajeria/auth/login');
    exit();
}

$usuarioIdActual = $_SESSION['usuario_id'] ?? $_COOKIE['usuario_id'] ?? '';
$nombreUsuarioActual = $_SESSION['nombre_usuario'] ?? $_COOKIE['nombre_usuario'] ?? 'Usuario';
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container-fluid">
        <a class="navbar-brand" href="/mensajeria/mensajeria">
            <i class="bi bi-chat-dots-fill me-2"></i>
            Mensajería
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <div class="user-avatar d-inline-flex me-2" style="width: 32px; height: 32px; font-size: 14px;">
                            <?php echo strtoupper(substr($nombreUsuarioActual, 0, 1)); ?>
                        </div>
                        <?php echo htmlspecialchars($nombreUsuarioActual, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#nuevaConversacionModal">
                                <i class="bi bi-plus-circle me-2"></i>Nueva conversación
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-person-circle me-2"></i>Mi perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" id="logoutBtn">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid chat-container">
    <div class="row h-100">
        <!-- Lista de conversaciones -->
        <div class="col-md-4 col-lg-3 conversations-list p-0">
            <div class="p-3 border-bottom bg-white sticky-top">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        class="form-control"
                        id="searchConversations"
                        placeholder="Buscar conversaciones..."
                    >
                </div>
            </div>

            <div id="conversationsList" class="list-group list-group-flush">
                <div class="text-center p-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 mb-0">Cargando conversaciones...</p>
                </div>
            </div>
        </div>

        <!-- Área de chat -->
        <div class="col-md-8 col-lg-9 p-0 d-flex flex-column">
            <div id="chatHeader" class="bg-white p-3 border-bottom d-none">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3" id="chatAvatar">C</div>
                    <div class="overflow-hidden">
                        <h5 class="mb-0 text-truncate" id="chatTitle">Selecciona una conversación</h5>
                        <small class="text-muted" id="chatStatus">Conversación activa</small>
                    </div>
                </div>
            </div>

            <div id="messagesContainer" class="messages-container flex-grow-1">
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-chat-dots" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Selecciona una conversación para empezar a chatear</h5>
                </div>
            </div>

            <div id="messageInput" class="bg-white p-3 border-top d-none">
                <form id="sendMessageForm" enctype="multipart/form-data">
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" id="attachFileBtn" title="Adjuntar imagen">
                            <i class="bi bi-paperclip"></i>
                        </button>

                        <input type="file" id="fileInput" class="d-none" accept="image/*">

                        <input
                            type="text"
                            class="form-control"
                            id="messageContent"
                            placeholder="Escribe un mensaje..."
                            autocomplete="off"
                        >

                        <button type="submit" class="btn btn-primary" title="Enviar mensaje">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>

                    <div id="filePreview" class="mt-2 d-none">
                        <div class="d-flex align-items-center bg-light p-2 rounded">
                            <i class="bi bi-file-image me-2"></i>
                            <span class="flex-grow-1 text-truncate" id="fileName"></span>
                            <button type="button" class="btn btn-sm" id="removeFile">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <img id="imagePreview" class="image-preview mt-2 d-none" alt="Vista previa">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Conversación -->
<div class="modal fade" id="nuevaConversacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva conversación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <form id="nuevaConversacionForm">
                    <div class="mb-3">
                        <label class="form-label">Buscar participantes</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                id="searchUsers"
                                placeholder="Nombre de usuario o email..."
                            >
                        </div>
                    </div>

                    <div id="searchResults" class="list-group mb-3" style="max-height: 200px; overflow-y: auto;">
                        <!-- Resultados -->
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Participantes seleccionados</label>
                        <div id="selectedParticipants" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="esGrupo">
                        <label class="form-check-label" for="esGrupo">¿Es un grupo?</label>
                    </div>

                    <div id="grupoFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Nombre del grupo</label>
                            <input type="text" class="form-control" id="nombreGrupo" placeholder="Ej. Equipo de proyecto">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="crearConversacion">Crear</button>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_URL = 'http://localhost/mensajeria/';
const API_URL = BASE_URL + 'api/';

let usuarioId = localStorage.getItem('usuario_id') || '<?php echo addslashes($usuarioIdActual); ?>';
let conversacionActual = null;
let fileToSend = null;
let searchTimeout = null;
let mensajesInterval = null;
let conversacionesInterval = null;

document.addEventListener("DOMContentLoaded", function () {
    cargarConversaciones();

    conversacionesInterval = setInterval(() => {
        cargarConversaciones(false);
    }, 5000);

    $('#searchConversations').on('keyup', function () {
        filtrarConversaciones($(this).val().toLowerCase().trim());
    });

    $('#sendMessageForm').on('submit', function (e) {
        e.preventDefault();
        enviarMensaje();
    });

    $('#attachFileBtn').on('click', function () {
        $('#fileInput').click();
    });

    $('#fileInput').on('change', function (e) {
        manejarArchivoSeleccionado(e);
    });

    $('#removeFile').on('click', function () {
        limpiarArchivoAdjunto();
    });

    $('#logoutBtn').on('click', function (e) {
        e.preventDefault();
        cerrarSesion();
    });

    $('#searchUsers').on('keyup', function () {
        clearTimeout(searchTimeout);

        const term = $(this).val().trim();

        if (term.length < 3) {
            $('#searchResults').html('');
            return;
        }

        searchTimeout = setTimeout(() => {
            buscarUsuarios(term);
        }, 400);
    });

    $('#crearConversacion').on('click', function () {
        crearConversacion();
    });

    $('#esGrupo').on('change', function () {
        $('#grupoFields').toggleClass('d-none', !$(this).is(':checked'));
    });
});

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return $('<div>').text(text).html();
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    if (isNaN(date.getTime())) return fecha;

    return date.toLocaleString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function cargarConversaciones(mostrarLoader = true) {
    if (mostrarLoader) {
        $('#conversationsList').html(`
            <div class="text-center p-4 text-muted">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 mb-0">Cargando conversaciones...</p>
            </div>
        `);
    }

    $.ajax({
        url: `${API_URL}conversaciones/listar/${usuarioId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                mostrarConversaciones(response.data || []);
            } else {
                mostrarEstadoConversaciones('No se pudieron cargar las conversaciones');
            }
        },
        error: function (xhr) {
            console.error('Error al cargar conversaciones:', xhr);
            mostrarEstadoConversaciones('Error al cargar conversaciones');
        }
    });
}

function mostrarEstadoConversaciones(mensaje) {
    $('#conversationsList').html(`
        <div class="text-center p-4 text-muted">
            <i class="bi bi-exclamation-circle" style="font-size: 2.5rem;"></i>
            <p class="mt-2 mb-0">${escapeHtml(mensaje)}</p>
        </div>
    `);
}

function mostrarConversaciones(conversaciones) {
    const container = $('#conversationsList');

    if (!conversaciones || conversaciones.length === 0) {
        container.html(`
            <div class="text-center p-4 text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2">No tienes conversaciones</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nuevaConversacionModal">
                    <i class="bi bi-plus-circle"></i> Nueva conversación
                </button>
            </div>
        `);
        return;
    }

    let html = '';

    conversaciones.forEach(conv => {
        const esGrupo = String(conv.es_grupo) === '1';
        const nombreConv = esGrupo
            ? (conv.nombre_grupo || 'Grupo sin nombre')
            : (conv.nombre_completo_participante || conv.nombre_participante || 'Chat privado');

        const ultimoMsg = conv.ultimo_mensaje || 'Sin mensajes';
        const fecha = formatearFecha(conv.fecha_envio);
        const activeClass = conversacionActual && String(conversacionActual.conversacion_id) === String(conv.conversacion_id)
            ? 'active'
            : '';

        const nombreEscapado = escapeHtml(nombreConv);
        const ultimoMsgEscapado = escapeHtml(ultimoMsg);
        const inicial = nombreConv.charAt(0).toUpperCase();

        html += `
            <div class="conversation-item ${activeClass}"
                 data-id="${escapeHtml(conv.conversacion_id)}"
                 data-name="${nombreEscapado}"
                 onclick="cargarMensajes('${String(conv.conversacion_id).replace(/'/g, "\\'")}', '${String(nombreConv).replace(/'/g, "\\'")}')">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                        ${escapeHtml(inicial)}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-truncate">${nombreEscapado}</h6>
                            <small class="text-muted ms-2">${escapeHtml(fecha)}</small>
                        </div>
                        <small class="text-muted d-block text-truncate">
                            ${ultimoMsgEscapado}
                        </small>
                    </div>
                </div>
            </div>
        `;
    });

    container.html(html);

    const searchTerm = $('#searchConversations').val().toLowerCase().trim();
    if (searchTerm) {
        filtrarConversaciones(searchTerm);
    }
}

function cargarMensajes(conversacionId, nombreConversacion = 'Conversación') {
    conversacionActual = {
        conversacion_id: conversacionId,
        nombre: nombreConversacion
    };

    $('.conversation-item').removeClass('active');
    $(`.conversation-item[data-id="${conversacionId}"]`).addClass('active');

    $('#chatHeader, #messageInput').removeClass('d-none');
    $('#chatTitle').text(nombreConversacion);
    $('#chatAvatar').text(nombreConversacion.charAt(0).toUpperCase());
    $('#chatStatus').text('Conversación activa');

    $('#messagesContainer').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: `${API_URL}mensajes/conversacion/${conversacionId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                mostrarMensajes(response.data || []);
            } else {
                mostrarEstadoMensajes('No se pudieron cargar los mensajes');
            }
        },
        error: function (xhr) {
            console.error('Error al cargar mensajes:', xhr);
            mostrarEstadoMensajes('Error al cargar los mensajes');
        }
    });

    if (mensajesInterval) {
        clearInterval(mensajesInterval);
    }

    mensajesInterval = setInterval(() => {
        if (conversacionActual) {
            cargarNuevosMensajes(conversacionActual.conversacion_id);
        }
    }, 3000);
}

function mostrarEstadoMensajes(mensaje) {
    $('#messagesContainer').html(`
        <div class="text-center p-4 text-danger">
            <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
            <p class="mt-2 mb-0">${escapeHtml(mensaje)}</p>
        </div>
    `);
}

function mostrarMensajes(mensajes) {
    if (!mensajes || mensajes.length === 0) {
        $('#messagesContainer').html(`
            <div class="text-center p-4 text-muted">
                <i class="bi bi-chat" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No hay mensajes. ¡Envía el primero!</p>
            </div>
        `);
        return;
    }

    let html = '';

    mensajes.forEach(msg => {
        const esMio = String(msg.remitente_id) === String(usuarioId);
        const clase = esMio ? 'sent' : 'received';
        const nombre = esMio ? 'Tú' : (msg.nombre_usuario || msg.nombre_completo || 'Usuario');
        const fecha = formatearFecha(msg.fecha_envio);

        if (msg.tipo === 'texto') {
            const contenido = msg.contenido_cifrado || '';

            html += `
                <div class="message ${clase}">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} mb-1">${escapeHtml(nombre)}</small>
                    <p class="mb-1">${escapeHtml(contenido)}</p>
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} text-end">
                        ${escapeHtml(fecha)}
                    </small>
                </div>
            `;
        } else if (msg.tipo === 'imagen') {
            const imageUrl = `${BASE_URL}uploads/${msg.storage_key}`;

            html += `
                <div class="message ${clase}">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} mb-1">${escapeHtml(nombre)}</small>
                    <img src="${escapeHtml(imageUrl)}" class="message-image img-fluid mt-1" alt="Imagen enviada" onclick="window.open('${imageUrl}', '_blank')">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} text-end mt-1">
                        ${escapeHtml(fecha)}
                    </small>
                </div>
            `;
        }
    });

    $('#messagesContainer').html(html);
    scrollAlFinal();
}

function cargarNuevosMensajes(conversacionId) {
    $.ajax({
        url: `${API_URL}mensajes/conversacion/${conversacionId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data) {
                const mensajesActuales = $('#messagesContainer .message').length;
                if (response.data.length !== mensajesActuales) {
                    mostrarMensajes(response.data);
                }
            }
        },
        error: function (xhr) {
            console.error('Error al actualizar mensajes:', xhr);
        }
    });
}

function enviarMensaje() {
    if (!conversacionActual || !conversacionActual.conversacion_id) {
        Swal.fire('Aviso', 'Selecciona una conversación primero', 'warning');
        return;
    }

    const contenido = $('#messageContent').val().trim();

    if (!contenido && !fileToSend) {
        Swal.fire('Aviso', 'Escribe un mensaje o selecciona una imagen', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('conversacion_id', conversacionActual.conversacion_id);
    formData.append('remitente_id', usuarioId);

    if (fileToSend) {
        formData.append('tipo', 'imagen');
        formData.append('contenido', contenido || 'Imagen');
        formData.append('iv', generateRandomIV());
        formData.append('archivo', fileToSend);
    } else {
        formData.append('tipo', 'texto');
        formData.append('contenido', contenido);
        formData.append('iv', generateRandomIV());
    }

    $.ajax({
        url: `${API_URL}mensajes/enviar`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $('#messageContent').val('');
                limpiarArchivoAdjunto();
                cargarMensajes(conversacionActual.conversacion_id, conversacionActual.nombre);
                cargarConversaciones(false);
            } else {
                Swal.fire('Error', 'No se pudo enviar el mensaje', 'error');
            }
        },
        error: function (xhr) {
            console.error('Error al enviar mensaje:', xhr);

            let errorMsg = 'No se pudo enviar el mensaje';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }

            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

function manejarArchivoSeleccionado(e) {
    const file = e.target.files[0];

    if (!file) return;

    if (!file.type.startsWith('image/')) {
        Swal.fire('Archivo no válido', 'Solo puedes seleccionar imágenes', 'warning');
        $('#fileInput').val('');
        return;
    }

    fileToSend = file;
    $('#fileName').text(file.name);
    $('#filePreview').removeClass('d-none');

    const reader = new FileReader();
    reader.onload = function (event) {
        $('#imagePreview')
            .attr('src', event.target.result)
            .removeClass('d-none');
    };
    reader.readAsDataURL(file);
}

function limpiarArchivoAdjunto() {
    fileToSend = null;
    $('#fileInput').val('');
    $('#fileName').text('');
    $('#filePreview').addClass('d-none');
    $('#imagePreview').attr('src', '').addClass('d-none');
}

function generateRandomIV() {
    let result = '';
    const characters = '0123456789abcdef';

    for (let i = 0; i < 16; i++) {
        result += characters.charAt(Math.floor(Math.random() * characters.length));
    }

    return result;
}

function filtrarConversaciones(term) {
    $('.conversation-item').each(function () {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.includes(term));
    });
}

function buscarUsuarios(term) {
    $.ajax({
        url: `${API_URL}usuarios/buscar?q=${encodeURIComponent(term)}&actual=${usuarioId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                mostrarResultadosBusqueda(response.data || []);
            } else {
                $('#searchResults').html('<div class="p-2 text-muted">No se encontraron usuarios</div>');
            }
        },
        error: function (xhr) {
            console.error('Error al buscar usuarios:', xhr);
            $('#searchResults').html('<div class="p-2 text-danger">Error al buscar usuarios</div>');
        }
    });
}

function mostrarResultadosBusqueda(usuarios) {
    const container = $('#searchResults');

    if (!usuarios || usuarios.length === 0) {
        container.html('<div class="p-2 text-muted">No se encontraron usuarios</div>');
        return;
    }

    let html = '';

    usuarios.forEach(user => {
        const nombreUsuario = user.nombre_usuario || 'Usuario';
        const email = user.email || '';
        const usuarioIdBusqueda = user.usuario_id || user.id || '';

        html += `
            <div class="list-group-item list-group-item-action"
                 onclick="seleccionarUsuario('${String(usuarioIdBusqueda).replace(/'/g, "\\'")}', '${String(nombreUsuario).replace(/'/g, "\\'")}')">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 14px;">
                        ${escapeHtml(nombreUsuario.charAt(0).toUpperCase())}
                    </div>
                    <div class="overflow-hidden">
                        <strong class="d-block text-truncate">${escapeHtml(nombreUsuario)}</strong>
                        <small class="d-block text-muted text-truncate">${escapeHtml(email)}</small>
                    </div>
                </div>
            </div>
        `;
    });

    container.html(html);
}

function seleccionarUsuario(usuarioSeleccionadoId, nombreUsuario) {
    if (String(usuarioSeleccionadoId) === String(usuarioId)) {
        Swal.fire('Error', 'No puedes agregarte a ti mismo', 'warning');
        return;
    }

    if ($(`#selectedParticipant-${usuarioSeleccionadoId}`).length > 0) {
        return;
    }

    const participantHtml = `
        <span class="badge bg-primary p-2" id="selectedParticipant-${usuarioSeleccionadoId}">
            ${escapeHtml(nombreUsuario)}
            <i class="bi bi-x ms-1" style="cursor: pointer;" onclick="eliminarParticipante('${String(usuarioSeleccionadoId).replace(/'/g, "\\'")}')"></i>
            <input type="hidden" name="participantes[]" value="${escapeHtml(usuarioSeleccionadoId)}">
        </span>
    `;

    $('#selectedParticipants').append(participantHtml);
    $('#searchUsers').val('');
    $('#searchResults').html('');
}

function eliminarParticipante(usuarioIdEliminar) {
    $(`#selectedParticipant-${usuarioIdEliminar}`).remove();
}

function crearConversacion() {
    const participantes = [];

    $('input[name="participantes[]"]').each(function () {
        participantes.push($(this).val());
    });

    if (participantes.length === 0) {
        Swal.fire('Error', 'Debes seleccionar al menos un participante', 'warning');
        return;
    }

    const esGrupo = $('#esGrupo').is(':checked');
    const nombreGrupo = $('#nombreGrupo').val().trim();

    if (esGrupo && !nombreGrupo) {
        Swal.fire('Error', 'Debes escribir un nombre para el grupo', 'warning');
        return;
    }

    const data = {
        creador_id: usuarioId,
        participantes: participantes
    };

    if (esGrupo) {
        data.nombre_grupo = nombreGrupo;
        data.es_grupo = true;
    }

    $.ajax({
        url: `${API_URL}conversaciones/crear`,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                const modalElement = document.getElementById('nuevaConversacionModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }

                Swal.fire('Éxito', 'Conversación creada correctamente', 'success');
                resetModalNuevaConversacion();
                cargarConversaciones();
            } else {
                Swal.fire('Error', 'No se pudo crear la conversación', 'error');
            }
        },
        error: function (xhr) {
            let errorMsg = 'Error al crear la conversación';

            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }

            Swal.fire('Error', errorMsg, 'error');
        }
    });
}

function resetModalNuevaConversacion() {
    $('#nuevaConversacionForm')[0].reset();
    $('#selectedParticipants').html('');
    $('#searchResults').html('');
    $('#grupoFields').addClass('d-none');
}

function cerrarSesion() {
    localStorage.clear();

    document.cookie.split(';').forEach(function (c) {
        document.cookie = c
            .replace(/^ +/, '')
            .replace(/=.*/, '=;expires=' + new Date().toUTCString() + ';path=/');
    });

    window.location.href = '/mensajeria/auth/login';
}

function scrollAlFinal() {
    const container = $('#messagesContainer')[0];
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>