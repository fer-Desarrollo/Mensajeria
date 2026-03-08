<?php include APPPATH . 'views/templates/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="auth-card p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-envelope-paper-fill text-info" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold mt-3">¿Olvidaste tu contraseña?</h2>
                    <p class="text-muted">Te enviaremos instrucciones para recuperarla</p>
                </div>

                <form id="olvidePasswordForm">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" 
                                   placeholder="correo@ejemplo.com" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3" id="btnEnviar">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="spinner"></span>
                        <span id="btnText">Enviar instrucciones</span>
                    </button>

                    <p class="text-center mb-0">
                        <a href="/mensajeria/auth/login" class="text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Volver al login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#olvidePasswordForm').submit(function(e) {
        e.preventDefault();
        
        const email = $('#email').val();

        if (!email || !isValidEmail(email)) {
            Swal.fire('Error', 'Por favor ingresa un email válido', 'error');
            return;
        }

        $('#spinner').removeClass('d-none');
        $('#btnText').text('Enviando...');
        $('#btnEnviar').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/olvide-password',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ email: email }),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Email enviado!',
                    text: 'Revisa tu correo para las instrucciones de recuperación',
                    confirmButtonText: 'Entendido'
                });
            },
            error: function(xhr) {
                let errorMsg = 'Error al procesar la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $('#spinner').addClass('d-none');
                $('#btnText').text('Enviar instrucciones');
                $('#btnEnviar').prop('disabled', false);
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