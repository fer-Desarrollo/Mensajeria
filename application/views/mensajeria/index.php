<?php include APPPATH . 'views/templates/header.php'; ?>

<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id']) && !isset($_COOKIE['usuario_id'])) {
    header('Location: /mensajeria/auth/login');
    exit();
}
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
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown">
                        <div class="user-avatar d-inline-block me-2">
                            <?php echo strtoupper(substr($_SESSION['nombre_usuario'] ?? $_COOKIE['nombre_usuario'], 0, 1)); ?>
                        </div>
                        <?php echo $_SESSION['nombre_usuario'] ?? $_COOKIE['nombre_usuario']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#nuevaConversacionModal">
                            <i class="bi bi-plus-circle me-2"></i>Nueva conversación
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="bi bi-person-circle me-2"></i>Mi perfil
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" id="logoutBtn">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                        </a></li>
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
            <div class="p-3 border-bottom">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchConversations" 
                           placeholder="Buscar conversaciones...">
                </div>
            </div>
            <div id="conversationsList" class="list-group list-group-flush">
                <!-- Las conversaciones se cargarán aquí dinámicamente -->
                <div class="text-center p-4 text-muted">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando conversaciones...</p>
                </div>
            </div>
        </div>

        <!-- Área de chat -->
        <div class="col-md-8 col-lg-9 p-0 d-flex flex-column">
            <div id="chatHeader" class="bg-white p-3 border-bottom d-none">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3" id="chatAvatar"></div>
                    <div>
                        <h5 class="mb-0" id="chatTitle">Selecciona una conversación</h5>
                        <small class="text-muted" id="chatStatus"></small>
                    </div>
                </div>
            </div>

            <div id="messagesContainer" class="messages-container flex-grow-1">
                <!-- Los mensajes se cargarán aquí -->
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-chat-dots" style="font-size: 4rem;"></i>
                    <h5>Selecciona una conversación para empezar a chatear</h5>
                </div>
            </div>

            <div id="messageInput" class="bg-white p-3 border-top d-none">
                <form id="sendMessageForm" enctype="multipart/form-data">
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" id="attachFileBtn">
                            <i class="bi bi-paperclip"></i>
                        </button>
                        <input type="file" id="fileInput" class="d-none" accept="image/*">
                        <input type="text" class="form-control" id="messageContent" 
                               placeholder="Escribe un mensaje..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    <div id="filePreview" class="mt-2 d-none">
                        <div class="d-flex align-items-center bg-light p-2 rounded">
                            <i class="bi bi-file-image me-2"></i>
                            <span class="flex-grow-1" id="fileName"></span>
                            <button type="button" class="btn btn-sm" id="removeFile">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                        <img id="imagePreview" class="image-preview mt-2 d-none">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Conversación -->
<div class="modal fade" id="nuevaConversacionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva conversación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaConversacionForm">
                    <div class="mb-3">
                        <label class="form-label">Buscar participantes</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchUsers" 
                                   placeholder="Nombre de usuario o email...">
                        </div>
                    </div>
                    <div id="searchResults" class="list-group mb-3" style="max-height: 200px; overflow-y: auto;">
                        <!-- Resultados de búsqueda -->
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Participantes seleccionados</label>
                        <div id="selectedParticipants" class="d-flex flex-wrap gap-2">
                            <!-- Participantes seleccionados -->
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="esGrupo">
                        <label class="form-check-label">¿Es un grupo?</label>
                    </div>
                    <div id="grupoFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Nombre del grupo</label>
                            <input type="text" class="form-control" id="nombreGrupo">
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
let usuarioId = localStorage.getItem('usuario_id') || '<?php echo $_SESSION['usuario_id'] ?? $_COOKIE['usuario_id']; ?>';
let conversacionActual = null;
let typingTimeout;
let fileToSend = null;

$(document).ready(function() {
    cargarConversaciones();
    
    // Cargar conversaciones cada 5 segundos
    setInterval(cargarConversaciones, 5000);
    
    // Buscar conversaciones
    $('#searchConversations').on('keyup', function() {
        const term = $(this).val().toLowerCase();
        filtrarConversaciones(term);
    });
    
    // Enviar mensaje
    $('#sendMessageForm').submit(function(e) {
        e.preventDefault();
        enviarMensaje();
    });
    
    // Adjuntar archivo
    $('#attachFileBtn').click(function() {
        $('#fileInput').click();
    });
    
    $('#fileInput').change(function(e) {
        const file = e.target.files[0];
        if (file) {
            fileToSend = file;
            $('#fileName').text(file.name);
            $('#filePreview').removeClass('d-none');
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).removeClass('d-none');
                };
                reader.readAsDataURL(file);
            }
        }
    });
    
    $('#removeFile').click(function() {
        fileToSend = null;
        $('#fileInput').val('');
        $('#filePreview').addClass('d-none');
        $('#imagePreview').addClass('d-none');
    });
    
    // Logout
    $('#logoutBtn').click(function() {
        localStorage.clear();
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });
        window.location.href = '/mensajeria/auth/login';
    });
    
    // Búsqueda de usuarios
    let searchTimeout;
    $('#searchUsers').on('keyup', function() {
        clearTimeout(searchTimeout);
        const term = $(this).val();
        
        if (term.length < 3) return;
        
        searchTimeout = setTimeout(() => {
            buscarUsuarios(term);
        }, 500);
    });
    
    // Crear conversación
    $('#crearConversacion').click(function() {
        crearConversacion();
    });
    
    $('#esGrupo').change(function() {
        if ($(this).is(':checked')) {
            $('#grupoFields').removeClass('d-none');
        } else {
            $('#grupoFields').addClass('d-none');
        }
    });
});

function cargarConversaciones() {
    $.ajax({
        url: `http://localhost/mensajeria/api/conversaciones/listar/${usuarioId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarConversaciones(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error al cargar conversaciones:', xhr);
        }
    });
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
        const activeClass = conversacionActual && conversacionActual.conversacion_id === conv.conversacion_id ? 'active' : '';
        const nombreConv = conv.es_grupo == '1' ? conv.nombre_grupo : 'Usuario';
        const ultimoMsg = conv.ultimo_mensaje || 'Sin mensajes';
        
        html += `
            <div class="conversation-item ${activeClass}" data-id="${conv.conversacion_id}" onclick="cargarMensajes('${conv.conversacion_id}')">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                        ${nombreConv.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${nombreConv}</h6>
                        <small class="text-muted">${ultimoMsg.substring(0, 30)}${ultimoMsg.length > 30 ? '...' : ''}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function cargarMensajes(conversacionId) {
    conversacionActual = { conversacion_id: conversacionId };
    
    // Actualizar UI
    $('.conversation-item').removeClass('active');
    $(`.conversation-item[data-id="${conversacionId}"]`).addClass('active');
    
    $('#chatHeader, #messageInput').removeClass('d-none');
    $('#messagesContainer').html(`
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: `http://localhost/mensajeria/api/mensajes/conversacion/${conversacionId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarMensajes(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error al cargar mensajes:', xhr);
            $('#messagesContainer').html(`
                <div class="text-center p-4 text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
                    <p>Error al cargar los mensajes</p>
                </div>
            `);
        }
    });
    
    // Cargar nuevos mensajes cada 3 segundos
    if (window.mensajesInterval) {
        clearInterval(window.mensajesInterval);
    }
    window.mensajesInterval = setInterval(() => {
        if (conversacionActual) {
            cargarNuevosMensajes(conversacionActual.conversacion_id);
        }
    }, 3000);
}

function mostrarMensajes(mensajes) {
    if (!mensajes || mensajes.length === 0) {
        $('#messagesContainer').html(`
            <div class="text-center p-4 text-muted">
                <i class="bi bi-chat" style="font-size: 3rem;"></i>
                <p>No hay mensajes. ¡Envía el primero!</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    mensajes.forEach(msg => {
        const esMio = msg.remitente_id === usuarioId;
        const clase = esMio ? 'sent' : 'received';
        const nombre = esMio ? 'Tú' : (msg.nombre_usuario || 'Usuario');
        
        if (msg.tipo === 'texto') {
            // Descifrar mensaje (ejemplo simple, deberías usar el IV correcto)
            const contenido = msg.contenido_cifrado; // Aquí deberías descifrar
            
            html += `
                <div class="message ${clase}">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'}">${nombre}</small>
                    <p class="mb-0">${contenido}</p>
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} text-end">
                        ${msg.fecha_envio}
                    </small>
                </div>
            `;
        } else if (msg.tipo === 'imagen') {
            const imageUrl = `http://localhost/mensajeria/uploads/${msg.storage_key}`;
            
            html += `
                <div class="message ${clase}">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'}">${nombre}</small>
                    <img src="${imageUrl}" class="message-image" onclick="window.open('${imageUrl}')">
                    <small class="d-block text-${esMio ? 'white-50' : 'muted'} text-end">
                        ${msg.fecha_envio}
                    </small>
                </div>
            `;
        }
    });
    
    $('#messagesContainer').html(html);
    
    // Scroll al último mensaje
    $('#messagesContainer').scrollTop($('#messagesContainer')[0].scrollHeight);
}

function cargarNuevosMensajes(conversacionId) {
    $.ajax({
        url: `http://localhost/mensajeria/api/mensajes/conversacion/${conversacionId}`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data && response.data.length > 0) {
                const mensajesActuales = $('#messagesContainer .message').length;
                if (response.data.length > mensajesActuales) {
                    mostrarMensajes(response.data);
                }
            }
        }
    });
}

function enviarMensaje() {
    const contenido = $('#messageContent').val();
    
    if (!contenido && !fileToSend) {
        return;
    }
    
    const formData = new FormData();
    formData.append('conversacion_id', conversacionActual.conversacion_id);
    formData.append('remitente_id', usuarioId);
    
    if (fileToSend) {
        formData.append('tipo', 'imagen');
        formData.append('contenido', 'foto');
        formData.append('iv', generateRandomIV());
        formData.append('archivo', fileToSend);
    } else {
        formData.append('tipo', 'texto');
        formData.append('contenido', contenido);
        formData.append('iv', generateRandomIV());
    }
    
    $.ajax({
        url: 'http://localhost/mensajeria/api/mensajes/enviar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#messageContent').val('');
            $('#removeFile').click();
            
            // Recargar mensajes
            cargarMensajes(conversacionActual.conversacion_id);
            
            // Actualizar lista de conversaciones
            cargarConversaciones();
        },
        error: function(xhr) {
            console.error('Error al enviar mensaje:', xhr);
            Swal.fire('Error', 'No se pudo enviar el mensaje', 'error');
        }
    });
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
    $('.conversation-item').each(function() {
        const text = $(this).text().toLowerCase();
        if (text.includes(term)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function buscarUsuarios(term) {
    $.ajax({
        url: `http://localhost/mensajeria/api/usuarios/buscar?q=${term}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarResultadosBusqueda(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error al buscar usuarios:', xhr);
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
        html += `
            <div class="list-group-item list-group-item-action" onclick="seleccionarUsuario('${user.usuario_id}', '${user.nombre_usuario}')">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 14px;">
                        ${user.nombre_usuario.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <strong>${user.nombre_usuario}</strong>
                        <small class="d-block text-muted">${user.email}</small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function seleccionarUsuario(usuarioId, nombreUsuario) {
    // Evitar seleccionarse a sí mismo
    if (usuarioId === usuarioId) {
        Swal.fire('Error', 'No puedes agregarte a ti mismo', 'warning');
        return;
    }
    
    // Verificar si ya está seleccionado
    if ($(`#selectedParticipant-${usuarioId}`).length > 0) {
        return;
    }
    
    const participantHtml = `
        <span class="badge bg-primary p-2" id="selectedParticipant-${usuarioId}">
            ${nombreUsuario}
            <i class="bi bi-x ms-1" style="cursor: pointer;" onclick="eliminarParticipante('${usuarioId}')"></i>
            <input type="hidden" name="participantes[]" value="${usuarioId}">
        </span>
    `;
    
    $('#selectedParticipants').append(participantHtml);
    $('#searchUsers').val('');
    $('#searchResults').html('');
}

function eliminarParticipante(usuarioId) {
    $(`#selectedParticipant-${usuarioId}`).remove();
}

function crearConversacion() {
    const participantes = [];
    $('input[name="participantes[]"]').each(function() {
        participantes.push($(this).val());
    });
    
    if (participantes.length === 0) {
        Swal.fire('Error', 'Debes seleccionar al menos un participante', 'warning');
        return;
    }
    
    const esGrupo = $('#esGrupo').is(':checked');
    const nombreGrupo = $('#nombreGrupo').val();
    
    const data = {
        creador_id: usuarioId,
        participantes: participantes
    };
    
    if (esGrupo && nombreGrupo) {
        data.nombre_grupo = nombreGrupo;
        data.es_grupo = true;
    }
    
    $.ajax({
        url: 'http://localhost/mensajeria/api/conversaciones/crear',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.success) {
                $('#nuevaConversacionModal').modal('hide');
                Swal.fire('Éxito', 'Conversación creada correctamente', 'success');
                cargarConversaciones();
                
                // Reiniciar modal
                $('#selectedParticipants').html('');
                $('#nombreGrupo').val('');
                $('#esGrupo').prop('checked', false);
                $('#grupoFields').addClass('d-none');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al crear la conversación';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error', errorMsg, 'error');
        }
    });
}
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>