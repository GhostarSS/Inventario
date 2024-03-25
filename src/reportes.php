<?php
session_start();
include "../conexion.php";
$id_user = $_SESSION['idUser'];
$permiso = "reportes";
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql);
if (empty ($existe) && $id_user != 1) {
    header('Location: permisos.php');
}
include_once "includes/header.php";
?>
<style>
    :root {
        --bg-color: #f0f0f0;
        --text-color: #333;
        --accent-color: #007bff;
        --form-bg-color: #ffffff;
        --border-color: #ccd0d5;
        --border-radius: 8px;
    }


    form {
        background-color: var(--form-bg-color);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
    }

    .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        background-color: #fff;
        font-size: 16px;
        color: var(--text-color);
    }

    .form-select:focus {
        outline: none;
        border-color: var(--accent-color);
    }
</style>
<div class="container text-center mt-4">
    <h2 class="my-3">Generación de Reportes</h2> <!-- Título actualizado para incluir margen -->
    <div class="card">
        <div class="card-body">
            <!-- Descripción general de la sección de reportes -->
            <p class="card-text mb-4">Seleccione el tipo de reporte que desea generar. Puede obtener un reporte de los
                productos que han vencido o un reporte de los productos que aún no han vencido.</p>
            <!-- Botones de reporte con descripciones -->
            <div class="my-4">
                <a href="pdf/reporte_vencidos.php" class="btn btn-warning" target="_blank">
                    <i class="fas fa-exclamation-triangle"></i> Reporte Productos Vencidos
                </a>
                <p class="mt-2">Este reporte incluye todos los productos cuya fecha de vencimiento ha pasado.</p>
                <a href="pdf/reporte_no_vencidos.php" class="btn btn-info" target="_blank">
                    <i class="fas fa-check-circle"></i> Reporte Productos No Vencidos
                </a>
                <p class="mt-2">Este reporte muestra los productos que aún tienen tiempo antes de vencerse.</p>


                <br>
                <br>
                <form action="pdf/insumos_instrum_reporte.php" method="post" id="typeForm">
                    <div class="form-group">
                        <label for="typeSelect">Descarga el reporte de inventario caducado segun si es (Insumo o
                            Instrumento Odontologico):</label>
                        <select class="form-select" name="type" id="typeSelect">
                            <option value="">- - - Selecciona - - -</option>
                            <?php
                            // Assuming you have a connection to the database
                            include "../conexion.php";
                            $typesQuery = mysqli_query($conexion, "SELECT DISTINCT tipo FROM tipos");
                            while ($type = mysqli_fetch_assoc($typesQuery)) {
                                echo "<option value=\"{$type['tipo']}\">{$type['tipo']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', (event) => {
                        document.getElementById('typeSelect').addEventListener('change', function () {
                            document.getElementById('typeForm').submit();
                        });
                    });
                </script>
                <br>
                <form action="pdf/insumos_instrum_reporte_no_vencidos.php" method="post" id="typeFormNotExpired">
                    <div class="form-group">
                        <label for="typeSelectNotExpired">Descarga el reporte de inventario no caducado segun si es
                            (Insumo o Instrumento Odontologico):</label>
                        <select class="form-select" name="type" id="typeSelectNotExpired"
                            onchange="submitForm('typeFormNotExpired')">
                            <option value="">- - - Selecciona - - -</option>
                            <?php
                            $typesQuery = mysqli_query($conexion, "SELECT DISTINCT tipo FROM tipos");
                            while ($type = mysqli_fetch_assoc($typesQuery)) {
                                echo "<option value=\"{$type['tipo']}\">{$type['tipo']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <br>
                <form action="pdf/inventario_reporte_lugar.php" method="post" id="placeForm">
                    <div class="form-group">
                        <label for="placeSelect">Descarga el reporte de inventario según el lugar:</label>
                        <select class="form-select" name="place" id="placeSelect" onchange="submitForm('placeForm')">
                            <option value="">- - - Selecciona - - -</option>
                            <?php
                            $placesQuery = mysqli_query($conexion, "SELECT * FROM lugar");
                            while ($place = mysqli_fetch_assoc($placesQuery)) {
                                echo "<option value=\"{$place['id']}\">{$place['laboratorio']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <br>
                <form action="pdf/inventario_reporte_lugar_vencidos.php" method="post" id="placeFormExpired">
                    <div class="form-group">
                        <label for="placeSelectExpired">Descarga el reporte de inventario caducado según el
                            lugar:</label>
                        <select class="form-select" name="place" id="placeSelectExpired"
                            onchange="submitForm('placeFormExpired')">
                            <option value="">- - - Selecciona - - -</option>
                            <?php
                            // Suponiendo que $conexion es tu conexión activa a la base de datos
                            $placesQuery = mysqli_query($conexion, "SELECT * FROM lugar");
                            while ($place = mysqli_fetch_assoc($placesQuery)) {
                                echo "<option value=\"{$place['id']}\">{$place['laboratorio']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </form>
                <script>

                    
                        function submitForm(formId) {
                            document.getElementById(formId).submit();
                    }
                </script>



            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>