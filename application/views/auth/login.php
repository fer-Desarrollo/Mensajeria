<?php include APPPATH . 'views/templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="auth-card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-chat-dots-fill text-primary" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold mt-3">¡Bienvenido de vuelta!</h2>
                    <p class="text-muted">Inicia sesión para continuar</p>
                </div>

                <form id="loginForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control border-start-0" id="usuario" 
                                   placeholder="Ingresa tu usuario" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0" id="password" 
                                   placeholder="Ingresa tu contraseña" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember">
                            <label class="form-check-label" for="remember">
                                Recordarme
                            </label>
                        </div>
                        <a href="/mensajeria/auth/olvide-password" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" id="btnLogin">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="spinner"></span>
                        <span id="btnText">Iniciar Sesión</span>
                    </button>

                    <p class="text-center mb-0">
                        ¿No tienes cuenta? <a href="/mensajeria/auth/registro" class="text-decoration-none fw-semibold">Regístrate aquí</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#togglePassword').click(function() {
        const password = $('#password');
        const icon = $(this).find('i');

        if (password.attr('type') === 'password') {
            password.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            password.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    $('#loginForm').submit(function(e) {
        e.preventDefault();

        const usuario = $('#usuario').val().trim();
        const password = $('#password').val().trim();

        if (!usuario || !password) {
            Swal.fire('Error', 'Por favor completa todos los campos', 'error');
            return;
        }

        $('#spinner').removeClass('d-none');
        $('#btnText').text('Iniciando sesión...');
        $('#btnLogin').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/login',
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                usuario: usuario,
                password: password
            }),
            success: function(response) {
                if (response.success) {
                    localStorage.setItem('usuario_id', response.usuario_id);
                    localStorage.setItem('nombre_usuario', response.nombre_usuario);

                    if (response.password_temporal) {
                        window.location.href = '/mensajeria/auth/cambiar_password';
                    } else {
                        window.location.href = '/mensajeria/mensajeria';
                    }
                } else {
                    Swal.fire('Error', response.error || 'Credenciales incorrectas', 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al conectar con el servidor';

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }

                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#spinner').addClass('d-none');
                $('#btnText').text('Iniciar Sesión');
                $('#btnLogin').prop('disabled', false);
            }
        });
    });
});
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>