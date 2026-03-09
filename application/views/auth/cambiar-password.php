<?php include APPPATH . 'views/templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="auth-card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill text-warning" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold mt-3">Cambiar contraseña temporal</h2>
                    <p class="text-muted">Por seguridad, debes cambiar tu contraseña temporal</p>
                </div>

                <form id="cambiarPasswordForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text" class="form-control" id="usuario" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña actual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password_actual" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_actual">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="password_nueva" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_nueva">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Mínimo 8 caracteres</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="confirmar_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#confirmar_password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnCambiar">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="spinnerCambiar"></span>
                        <span id="btnCambiarText">Cambiar contraseña</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const usuarioGuardado = localStorage.getItem('usuario_login') || localStorage.getItem('nombre_usuario') || '';
    $('#usuario').val(usuarioGuardado);

    $('.toggle-password').on('click', function() {
        const target = $($(this).data('target'));
        const icon = $(this).find('i');

        if (target.attr('type') === 'password') {
            target.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            target.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    $('#cambiarPasswordForm').on('submit', function(e) {
        e.preventDefault();

        const usuario = $('#usuario').val().trim();
        const passwordActual = $('#password_actual').val().trim();
        const passwordNueva = $('#password_nueva').val().trim();
        const confirmarPassword = $('#confirmar_password').val().trim();

        if (!usuario || !passwordActual || !passwordNueva || !confirmarPassword) {
            Swal.fire('Error', 'Completa todos los campos', 'error');
            return;
        }

        if (passwordNueva.length < 8) {
            Swal.fire('Error', 'La nueva contraseña debe tener al menos 8 caracteres', 'error');
            return;
        }

        if (passwordNueva !== confirmarPassword) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }

        $('#spinnerCambiar').removeClass('d-none');
        $('#btnCambiarText').text('Actualizando...');
        $('#btnCambiar').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/cambiar_password',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                usuario: usuario,
                password_actual: passwordActual,
                password_nueva: passwordNueva
            }),
            success: function(response) {
                if (response.success) {
                    localStorage.removeItem('usuario_id');
                    localStorage.removeItem('nombre_usuario');

                    Swal.fire({
                        icon: 'success',
                        title: 'Contraseña actualizada',
                        text: 'Ahora inicia sesión con tu nueva contraseña'
                    }).then(() => {
                        window.location.href = '/mensajeria/auth/login';
                    });
                } else {
                    Swal.fire('Error', response.error || 'No se pudo actualizar la contraseña', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al cambiar la contraseña';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }

                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#spinnerCambiar').addClass('d-none');
                $('#btnCambiarText').text('Cambiar contraseña');
                $('#btnCambiar').prop('disabled', false);
            }
        });
    });
});
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>