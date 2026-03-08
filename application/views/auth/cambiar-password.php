<?php include APPPATH . 'views/templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="auth-card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill text-warning" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold mt-3">Cambiar contraseña temporal</h2>
                    <p class="text-muted">Por seguridad, debes cambiar tu contraseña temporal</p>
                </div>

                <form id="cambiarPasswordForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text" class="form-control" id="usuario" 
                               value="<?php echo isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : ''; ?>" 
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña actual</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="password_actual" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_actual')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="password_nueva" 
                                   onkeyup="checkPasswordStrength()" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_nueva')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <small class="text-muted" id="passwordHelp">
                            Mínimo 8 caracteres, 1 mayúscula, 1 minúscula y 1 número
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-key-fill"></i></span>
                            <input type="password" class="form-control" id="confirmar_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmar_password')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnCambiar">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="spinner"></span>
                        <span id="btnText">Cambiar contraseña</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#cambiarPasswordForm').submit(function(e) {
        e.preventDefault();
        
        const usuario = $('#usuario').val();
        const password_actual = $('#password_actual').val();
        const password_nueva = $('#password_nueva').val();
        const confirmar = $('#confirmar_password').val();

        if (!usuario || !password_actual || !password_nueva || !confirmar) {
            Swal.fire('Error', 'Por favor completa todos los campos', 'error');
            return;
        }

        if (password_nueva !== confirmar) {
            Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
            return;
        }

        if (!isValidPassword(password_nueva)) {
            Swal.fire('Error', 'La contraseña no cumple con los requisitos de seguridad', 'error');
            return;
        }

        $('#spinner').removeClass('d-none');
        $('#btnText').text('Cambiando contraseña...');
        $('#btnCambiar').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/cambiar-password',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                usuario: usuario,
                password_actual: password_actual,
                password_nueva: password_nueva
            }),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Contraseña cambiada!',
                    text: 'Tu contraseña ha sido actualizada correctamente',
                    confirmButtonText: 'Iniciar sesión'
                }).then((result) => {
                    window.location.href = '/mensajeria/auth/login';
                });
            },
            error: function(xhr) {
                let errorMsg = 'Error al cambiar la contraseña';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#spinner').addClass('d-none');
                $('#btnText').text('Cambiar contraseña');
                $('#btnCambiar').prop('disabled', false);
            }
        });
    });
});

function togglePassword(fieldId) {
    const field = $('#' + fieldId);
    const icon = field.next().next().find('i');
    
    if (field.attr('type') === 'password') {
        field.attr('type', 'text');
        icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
        field.attr('type', 'password');
        icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
}

function checkPasswordStrength() {
    const password = $('#password_nueva').val();
    const strengthBar = $('#passwordStrength');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;

    strengthBar.removeClass('weak medium strong');
    
    if (strength <= 2) {
        strengthBar.addClass('weak');
    } else if (strength <= 4) {
        strengthBar.addClass('medium');
    } else {
        strengthBar.addClass('strong');
    }
}

function isValidPassword(password) {
    return password.length >= 8 && 
           /[a-z]/.test(password) && 
           /[A-Z]/.test(password) && 
           /[0-9]/.test(password);
}
</script>

<?php include APPPATH . 'views/templates/footer.php'; ?>