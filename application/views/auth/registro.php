<?php include APPPATH . 'views/templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-md-6">
            <div class="auth-card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold mt-3">Crear cuenta</h2>
                    <p class="text-muted">Completa tus datos para registrarte</p>
                </div>

                <form id="registroForm">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Nombre completo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control" id="nombre_completo"
                                       placeholder="Ej: Juan Pérez García" required>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Nombre de usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-at"></i></span>
                                <input type="text" class="form-control" id="nombre_usuario"
                                       placeholder="Ej: juanperez" required>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email"
                                       placeholder="correo@ejemplo.com" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="telefono"
                                       placeholder="1234567890" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Fecha de nacimiento</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                                <input type="date" class="form-control" id="fecha_nacimiento" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Género</label>
                            <select class="form-select" id="genero" required>
                                <option value="">Selecciona...</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">País</label>
                            <input type="text" class="form-control" id="pais"
                                   placeholder="Ej: México" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Ciudad</label>
                            <input type="text" class="form-control" id="ciudad"
                                   placeholder="Ej: CDMX" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password"
                                       placeholder="********" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Confirmar contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password"
                                       placeholder="********" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" id="btnRegistro">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="spinner"></span>
                        <span id="btnText">Registrarse</span>
                    </button>

                    <p class="text-center mb-0">
                        ¿Ya tienes cuenta? <a href="/mensajeria/auth/login" class="text-decoration-none fw-semibold">Inicia sesión</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#registroForm').submit(function(e) {
        e.preventDefault();

        const formData = {
            nombre_completo: $('#nombre_completo').val().trim(),
            nombre_usuario: $('#nombre_usuario').val().trim(),
            email: $('#email').val().trim(),
            telefono: $('#telefono').val().trim(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            genero: $('#genero').val(),
            pais: $('#pais').val().trim(),
            ciudad: $('#ciudad').val().trim(),
            password: $('#password').val(),
            confirm_password: $('#confirm_password').val()
        };

        if (
            !formData.nombre_completo ||
            !formData.nombre_usuario ||
            !formData.email ||
            !formData.telefono ||
            !formData.fecha_nacimiento ||
            !formData.genero ||
            !formData.pais ||
            !formData.ciudad ||
            !formData.password ||
            !formData.confirm_password
        ) {
            Swal.fire('Error', 'Por favor completa todos los campos', 'error');
            return;
        }

        if (!isValidEmail(formData.email)) {
            Swal.fire('Error', 'Por favor ingresa un email válido', 'error');
            return;
        }

        if (formData.password.length < 6) {
            Swal.fire('Error', 'La contraseña debe tener al menos 6 caracteres', 'error');
            return;
        }

        if (formData.password !== formData.confirm_password) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }

        $('#spinner').removeClass('d-none');
        $('#btnText').text('Registrando...');
        $('#btnRegistro').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/registro',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro exitoso!',
                        html: `
                            <p>Tu cuenta fue creada correctamente.</p>
                            <p><strong>Usuario:</strong> ${response.usuario_generado}</p>
                            <p><strong>Contraseña temporal:</strong> ${response.password_temporal}</p>
                            <p>Guárdalos para iniciar sesión.</p>
                        `,
                        confirmButtonText: 'Ir a login'
                    }).then(() => {
                        window.location.href = '/mensajeria/auth/login';
                    });
                } else {
                    Swal.fire('Error', response.error || 'No se pudo registrar el usuario', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al registrar usuario';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }

                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#spinner').addClass('d-none');
                $('#btnText').text('Registrarse');
                $('#btnRegistro').prop('disabled', false);
            }
        });
    });

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>