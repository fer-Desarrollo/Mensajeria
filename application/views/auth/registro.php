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
$(document).ready(function() {
    $('#registroForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            nombre_completo: $('#nombre_completo').val(),
            email: $('#email').val(),
            telefono: $('#telefono').val(),
            fecha_nacimiento: $('#fecha_nacimiento').val(),
            genero: $('#genero').val(),
            pais: $('#pais').val(),
            ciudad: $('#ciudad').val()
        };

        // Validaciones básicas
        if (!formData.nombre_completo || !formData.email || !formData.telefono) {
            Swal.fire('Error', 'Por favor completa todos los campos requeridos', 'error');
            return;
        }

        if (!isValidEmail(formData.email)) {
            Swal.fire('Error', 'Por favor ingresa un email válido', 'error');
            return;
        }

        // Show loading
        $('#spinner').removeClass('d-none');
        $('#btnText').text('Registrando...');
        $('#btnRegistro').prop('disabled', true);

        $.ajax({
            url: 'http://localhost/mensajeria/api/auth/registro',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro exitoso!',
                    text: 'Tu cuenta ha sido creada correctamente. Por favor inicia sesión.',
                    confirmButtonText: 'Ir a login'
                }).then((result) => {
                    window.location.href = '/mensajeria/auth/login';
                });
            },
            error: function(xhr) {
                let errorMsg = 'Error al registrar usuario';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
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