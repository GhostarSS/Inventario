<?php
session_start();
require("../conexion.php");

if(isset($_POST['ids'])) {
    $idsParaEliminar = $_POST['ids'];
    $errores = [];
    foreach($idsParaEliminar as $id) {
        if(!mysqli_query($conexion, "DELETE FROM producto WHERE codproducto = '".mysqli_real_escape_string($conexion, $id)."'")) {
            $errores[] = "No se pudo eliminar el producto con ID: $id";
        }
    }
    mysqli_close($conexion);
    
    if(count($errores) === 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'errores' => $errores]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se recibieron IDs de productos.']);
}
?>
