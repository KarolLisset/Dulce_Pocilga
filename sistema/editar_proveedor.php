<?php
session_start();
if ($_SESSION['rol']!=1 AND $_SESSION['rol']!=2) {

    header("location: ./");
    // code...
}

include "../con.php";

$alert = '';

// Validación del formulario
if (!empty($_POST)) {
    if (empty($_POST['proveedor']) ||empty($_POST['contacto']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    } else {
        $idproveedor = mysqli_real_escape_string($connect, $_POST['id']);
        $proveedor = mysqli_real_escape_string($connect, $_POST['proveedor']);
        $contacto = mysqli_real_escape_string($connect, $_POST['contacto']);
        $telefono = mysqli_real_escape_string($connect, $_POST['telefono']);
        $direccion = $_POST['direccion'];


    
                $sql_update = mysqli_query($connect,"UPDATE proveedor SET proveedor = '$proveedor', contacto = '$contacto', telefono = '$telefono', direccion = '$direccion' WHERE codproveedor = $idproveedor");

            if ($sql_update) {
                $alert = '<p class="msg_save"> Proveedor actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar al proveedor.</p>';
            }
        }
}

// Mostrar datos para actualizar
if (empty($_REQUEST['id'])) {
    header('location: lista_proveedor.php');
    exit();
}

$idproveedor = mysqli_real_escape_string($connect, $_REQUEST['id']);
$sql = mysqli_query($connect, "SELECT *
                               FROM proveedor
                               WHERE codproveedor = $idproveedor and estatus=1");

$result_sql = mysqli_num_rows($sql);

if ($result_sql == 0) {
    header('location: lista_proveedor.php');
    exit();
} else {
    $data = mysqli_fetch_array($sql);
    $idproveedor=$data['codproveedor'];
    $proveedor = $data['proveedor'];
    $contacto = $data['contacto'];
    $telefono = $data['telefono'];
    $direccion = $data['direccion'];
}
mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scrips.php"; ?>
    <title>Actualizar Proveedor</title>
</head>
<body class="provbody">
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-solid fa-pen-to-square"></i> Actualizar Proveedor</h1>
            <hr>
            
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idproveedor?>">
                <label for="proveedor">Nombre del proveedor</label>
                <input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor" value="<?php echo $proveedor ?>">
                <label for="contacto">Contacto</label>
                <input type="text" name="contacto" placeholder="Nombre completo del contacto" value="<?php echo $contacto ?>">
                <label for="telefono">Télefono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion ?>">
                
            <button type="submit" class="btn_save"><i class="fa-solid fa-pen-to-square"></i> Actualizar proveedor</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
