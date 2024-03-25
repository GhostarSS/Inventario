<?php
require "../conexion.php";
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario");
$total['usuarios'] = mysqli_num_rows($usuarios);

$productos = mysqli_query($conexion, "SELECT * FROM producto");
$total['productos'] = mysqli_num_rows($productos);

session_start();
include_once "includes/header.php";
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Row -->
<div class="row">
    <!-- Card for Users -->
    <div class="col-md-4"> <!-- Change this to col-md-6 for half-width on medium screens and up -->
        <div class="card card-stats">
            <div class="card-header card-header-warning card-header-icon text-center">
                <div class="card-icon">
                    <i class="fas fa-user fa-2x"></i>
                </div>
                <a href="usuarios.php" class="card-category text-warning font-weight-bold">
                    Usuarios
                </a>
                <h3 class="card-title">
                    <?php echo $total['usuarios']; ?>
                </h3>
            </div>
            <div class="card-footer bg-warning text-white">
            </div>
        </div>
    </div>

    <!-- Card for Products -->
    <div class="col-md-4"> <!-- Change this to col-md-6 for half-width on medium screens and up -->
        <div class="card card-stats">
            <div class="card-header card-header-danger card-header-icon text-center">
                <div class="card-icon">
                    <i class="fa fa-archive fa-2x"></i>
                </div>
                <a href="productos.php" class="card-category text-danger font-weight-bold">
                    Inventario
                </a>
                <h3 class="card-title">
                    <?php echo $total['productos']; ?>
                </h3>
            </div>
            <div class="card-footer bg-primary">
            </div>
        </div>
    </div>

    <div class="col-md-4">
    <div class="card card-stats">
        <div class="card-header card-header-primary card-header-icon text-center">
            <div class="card-icon">
                <i class="fa fa-ban fa-2x"></i>
            </div>
            <a href="productos.php" class="card-category text-danger font-weight-bold">
                Inventario Caducado
            </a>
            <h3 class="card-title">
                <?php
                include "../conexion.php";
                $hoy = date('Y-m-d');
                $query = mysqli_query($conexion, "SELECT COUNT(*) AS total_caducados FROM producto WHERE vencimiento < '$hoy' AND vencimiento != ''");
                $result = mysqli_fetch_assoc($query);
                echo $result['total_caducados'];
                ?>
            </h3>
        </div>
        <div class="card-footer bg-secondary">
            <!-- Opcional: Información adicional o acciones del footer de la tarjeta -->
        </div>
    </div>


</div>

</div>

<div class="card">
    <div class="card-header">
        Productos Vencidos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Presentacion</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Fecha de Vencimiento</th> <!-- Nueva columna para la fecha de vencimiento -->
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "../conexion.php";
                    $hoy = date('Y-m-d');
                    // Consulta para seleccionar solo productos vencidos
                    $query = mysqli_query($conexion, "SELECT p.*, t.tipo, pr.nombre FROM producto p 
                                                  INNER JOIN tipos t ON p.id_tipo = t.id 
                                                  INNER JOIN presentacion pr ON p.id_presentacion = pr.id 
                                                  WHERE p.vencimiento != '' AND p.vencimiento < '$hoy'");
                    $result = mysqli_num_rows($query);
                    if ($result > 0) {
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td>
                                    <?php echo $data['codproducto']; ?>
                                </td>
                                <td>
                                    <?php echo $data['codigo']; ?>
                                </td>
                                <td>
                                    <?php echo $data['descripcion']; ?>
                                </td>
                                <td>
                                    <?php echo $data['tipo']; ?>
                                </td>
                                <td>
                                    <?php echo $data['nombre']; ?>
                                </td>
                                <td>
                                    <?php echo $data['precio']; ?>
                                </td>
                                <td>
                                    <?php echo $data['existencia']; ?>
                                </td>
                                <td>
                                    <?php echo $data['vencimiento']; ?>
                                </td>
                                <td>
                                    <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post"
                                        class="confirmar d-inline">
                                        <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Productos No Vencidos
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="tbl">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Presentacion</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Fecha de Vencimiento</th> <!-- Nueva columna para la fecha de vencimiento -->
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "../conexion.php";
                    $hoy = date('Y-m-d');
                    // Consulta para seleccionar productos no vencidos
                    $query = mysqli_query($conexion, "SELECT p.*, t.tipo, pr.nombre FROM producto p 
                                                  INNER JOIN tipos t ON p.id_tipo = t.id 
                                                  INNER JOIN presentacion pr ON p.id_presentacion = pr.id 
                                                  WHERE p.vencimiento = '' OR p.vencimiento >= '$hoy'");
                    $result = mysqli_num_rows($query);
                    if ($result > 0) {
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td><?php echo $data['codproducto']; ?></td>
                                <td><?php echo $data['codigo']; ?></td>
                                <td><?php echo $data['descripcion']; ?></td>
                                <td><?php echo $data['tipo']; ?></td>
                                <td><?php echo $data['nombre']; ?></td>
                                <td><?php echo $data['precio']; ?></td>
                                <td><?php echo $data['existencia']; ?></td>
                                <td><?php echo ($data['vencimiento'] != '') ? $data['vencimiento'] : 'No vence'; ?></td>
                                <td>
                                    <!-- Las acciones que puedas realizar -->
                                    
                                    <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
                                        <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    } else { ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay productos no vencidos para mostrar.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="row justify-content-center">
    <div class="col-3">
        <div class="card">
            <div class="card-header card-header-primary">
                <h3 class="title-2 m-b-40">Stock de Productos</h3>
            </div>
            <div class="card-body">
                <canvas id="stockMinimo"></canvas>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-header card-header-danger">
                <h3 class="title-2 m-b-40">Productos Caducados</h3>
            </div>
            <div class="card-body">
            <canvas id="productosCaducados"></canvas>

            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    
</div>

<?php
include "../conexion.php";
$hoy = date('Y-m-d');
$query = mysqli_query($conexion, "SELECT COUNT(*) as total, t.tipo FROM producto p 
                                  INNER JOIN tipos t ON p.id_tipo = t.id
                                  WHERE p.vencimiento < '$hoy' AND p.vencimiento <> '' 
                                  GROUP BY t.tipo");
$productosCaducados = [];
while ($resultado = mysqli_fetch_assoc($query)) {
    $productosCaducados[] = [
        'tipo' => $resultado['tipo'],
        'total' => $resultado['total']
    ];
}
?>

<script>
// Convertir los datos de PHP a JSON para JavaScript
const datosCaducados = <?php echo json_encode($productosCaducados); ?>;

const configProductosCaducados = {
    type: 'pie', // o 'bar', dependiendo del tipo de gráfica que desees
    data: {
        labels: datosCaducados.map(item => item.tipo), // Usa los tipos de producto como etiquetas
        datasets: [{
            label: 'Productos Caducados',
            data: datosCaducados.map(item => item.total), // Usa los totales de productos caducados
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    },
};

const ctxProductosCaducados = document.getElementById('productosCaducados').getContext('2d');
const chartProductosCaducados = new Chart(ctxProductosCaducados, configProductosCaducados);
</script>

</div>

<?php include_once "includes/footer.php"; ?>