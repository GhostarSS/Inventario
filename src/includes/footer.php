<?php
// Obtén la fecha actual
$hoy = date('Y-m-d');
// Define un intervalo, por ejemplo, productos que vencerán en los próximos 30 días
$intervalo = date('Y-m-d', strtotime("+30 days", strtotime($hoy)));

// Ejecuta la consulta SQL
$query = mysqli_query($conexion, "SELECT codproducto, descripcion, vencimiento FROM producto WHERE vencimiento != 'No vence' AND vencimiento != '' AND vencimiento BETWEEN '$hoy' AND '$intervalo'");

$productosProximosAVencer = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Si las configuraciones están en variables de sesión, recupéralas
$alert_interval = $_SESSION['alert_interval'] ?? 10; // 10 es un valor predeterminado
$alert_time_unit = $_SESSION['alert_time_unit'] ?? 'minutos'; // 'minutos' es un valor predeterminado

// Convertir el intervalo a segundos según la unidad de tiempo
switch ($alert_time_unit) {
    case 'segundos':
        $alert_interval_seconds = $alert_interval;
        break;
    case 'minutos':
        $alert_interval_seconds = $alert_interval * 60; // Convierte minutos a segundos
        break;
    case 'horas':
        $alert_interval_seconds = $alert_interval * 3600; // Convierte horas a segundos
        break;
    default:
        $alert_interval_seconds = $alert_interval * 60; // Usa minutos como valor predeterminado
        break;
}

// Asegúrate de que el script espera la cantidad correcta de tiempo
$alert_interval_milliseconds = $alert_interval_seconds * 1000; // Convierte segundos a milisegundos para JavaScript
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="../assets/js/jquery-3.6.0.min.js"></script> <!-- Asegúrate de incluir jQuery -->

<script type="text/javascript">
var productosProximosAVencer = <?php echo json_encode($productosProximosAVencer); ?>;

function mostrarAlerta() {
    if (productosProximosAVencer.length > 0) {
        var tablaHtml = '<table class="table table-bordered">' +
                        '<thead>' +
                        '<tr>' +
                        '<th>Seleccionar</th>' +
                        '<th>Producto</th>' +
                        '<th>Fecha de Vencimiento</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody>';
        productosProximosAVencer.forEach(function(producto) {
            tablaHtml += `<tr>
                             <td><input type="checkbox" class="producto-check" value="${producto.codproducto}"></td>
                             <td>${producto.descripcion}</td>
                             <td>${producto.vencimiento}</td>
                          </tr>`;
        });
        tablaHtml += '</tbody></table>';

        Swal.fire({
            title: 'Alerta de vencimiento',
            html: tablaHtml,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Agregar nuevo producto',
            cancelButtonText: 'Eliminar productos seleccionados'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'productos.php';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                var productosSeleccionados = $('.producto-check:checked').map(function(){
                    return this.value;
                }).get();
                if(productosSeleccionados.length > 0) {
                    eliminarProductosSeleccionados(productosSeleccionados);
                } else {
                    Swal.fire('Atención', 'No has seleccionado ningún producto.', 'info');
                }
            }
        });
    }
}

function eliminarProductosSeleccionados(idsProductos) {
    $.ajax({
        url: 'eliminar_producto_vencido.php',
        type: 'POST',
        data: { ids: idsProductos },
        success: function(response) {
            Swal.fire('Eliminado!', 'Los productos seleccionados han sido eliminados.', 'success')
            .then((result) => {
                // Recargar la página para reflejar los cambios o actualizar la lista
                location.reload();
            });
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'No se pudo eliminar los productos.', 'error');
        }
    });
}

setInterval(mostrarAlerta, <?php echo $alert_interval_milliseconds; ?>);
// Descomentar para que muestre la alerta sin cooldown
//mostrarAlerta();

</script>



</div>
</div>
<footer class="footer">
    <div class="container-fluid">
        <div class="copyright float-right">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
            <a href="#" target="_blank">Frank Sevilla</a>.
        </div>
    </div>
</footer>
</div>
</div>
<div id="nuevo_pass" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Cambiar contraseña</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="frmPass">
                    <div class="form-group">
                        <label for="actual"><i class="fas fa-key"></i> Contraseña Actual</label>
                        <input id="actual" class="form-control" type="password" name="actual" placeholder="Contraseña actual" required>
                    </div>
                    <div class="form-group">
                        <label for="nueva"><i class="fas fa-key"></i> Contraseña Nueva</label>
                        <input id="nueva" class="form-control" type="password" name="nueva" placeholder="Contraseña nueva" required>
                    </div>
                    <button class="btn btn-primary btn-block" type="button" onclick="btnCambiar(event)">Cambiar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="../assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/material-dashboard.js" type="text/javascript"></script>
<script src="../assets/js/bootstrap-notify.js"></script>
<script src="../assets/js/arrive.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="../assets/js/sweetalert2.all.min.js"></script>
<script src="../assets/js/jquery-ui/jquery-ui.min.js"></script>
<script src="../assets/js/chart.min.js"></script>
<script src="../assets/js/funciones.js"></script>
</body>

</html>