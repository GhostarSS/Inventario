<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "configuracion";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty ($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
$message = null; // Inicializar la variable de mensaje

// Revisa si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Asegúrate de que los campos necesarios estén seteados y no estén vacíos
    if (isset($_POST['alert_interval']) && isset($_POST['alert_time_unit'])) {
        // Guarda la configuración en la base de datos o en variables de sesión
        $_SESSION['alert_interval'] = $_POST['alert_interval'];
        $_SESSION['alert_time_unit'] = $_POST['alert_time_unit'];
        $message = "Configuración guardada correctamente. Intervalo: {$_POST['alert_interval']} {$_POST['alert_time_unit']}.";
    }
}

// Valores existentes de la configuración de alertas
$alert_interval = $_SESSION['alert_interval'] ?? '10';  // 10 es un valor predeterminado
$alert_time_unit = $_SESSION['alert_time_unit'] ?? 'minutos';  // 'minutos' es un valor predeterminado

include_once "includes/header.php";
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title">Configuración de Alertas</h4>
            </div>
            <div class="card-body">
                <form id="configForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Intervalo de Alerta (en la unidad de tiempo seleccionada):</label>
                        <input type="number" id="alert_interval" name="alert_interval" class="form-control" value="<?php echo $alert_interval; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Unidad de Tiempo:</label>
                        <select id="alert_time_unit" name="alert_time_unit" class="form-control" required>
                            <option value="minutos" <?php echo $alert_time_unit == 'minutos' ? 'selected' : ''; ?>>Minutos</option>
                            <option value="horas" <?php echo $alert_time_unit == 'horas' ? 'selected' : ''; ?>>Horas</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    <?php if ($message): ?>
        Swal.fire({
            icon: 'success',
            title: 'Configuración Guardada',
            text: '<?php echo $message; ?>',
            confirmButtonText: 'Aceptar'
        });
        <?php endif; ?>
});
</script>

<?php include_once "includes/footer.php"; ?>
