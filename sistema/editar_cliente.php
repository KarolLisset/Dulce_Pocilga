<?php
session_start();

include "../con.php";

$alert = '';

// Validación del formulario
if (!empty($_POST)) {
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    } else {
        $idcliente = mysqli_real_escape_string($connect, $_POST['id']);
        $nit = mysqli_real_escape_string($connect, $_POST['nit']);
        $nombre = mysqli_real_escape_string($connect, $_POST['nombre']);
        $telefono = mysqli_real_escape_string($connect, $_POST['telefono']);
        $direccion = $_POST['direccion'];


        $result=0;

        if(is_numeric($nit)and $nit!=0){
            // Comprobar si el correo o usuario ya existen para otro usuario
        $query = mysqli_query($connect, "SELECT * FROM cliente WHERE (nit = '$nit' AND idcliente != '$idcliente')");

        $result = mysqli_num_rows($query);

        }

        if ($result > 0) {
            $alert = '<p class="msg_error">El NIT ya existe, ingrese otro</p>';
        } else {

            if ($nit=='') {

                $nit=0;
               
            }
    
                $sql_update = mysqli_query($connect,"UPDATE cliente SET nit = '$nit', nombre = '$nombre', telefono = '$telefono', direccion = '$direccion' WHERE idcliente = $idcliente");

            if ($sql_update) {
                $alert = '<p class="msg_save"> Cliente actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar al cliente.</p>';
            }
        }
    }
}

// Mostrar datos para actualizar
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    header('location: lista_clientes.php');
    exit();
}

$idcliente = mysqli_real_escape_string($connect, $_REQUEST['id']);
$sql = mysqli_query($connect, "SELECT *
                               FROM cliente
                               WHERE idcliente = $idcliente and estatus=1");

$result_sql = mysqli_num_rows($sql);

if ($result_sql == 0) {
    header('location: lista_clientes.php');
    exit();
} else {
    $data = mysqli_fetch_array($sql);
    $idcliente=$data['idcliente'];
    $nit = $data['nit'];
    $nombre = $data['nombre'];
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
    <title>Actualizar Clientes</title>
</head>
<body class="clientesbody">
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-solid fa-pen-to-square"></i>Actualizar Clientes</h1>
            <hr>
            
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
                <label for="nit">NIT</label>
                <input type="number" name="nit" id="nit" placeholder="Número de NIT" value="<?php echo $nit; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo $nombre; ?>">
                <label for="telefono">Télefono</label>
                <input type="number" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $telefono; ?>">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa" value="<?php echo $direccion; ?>">
                
            <button type="submit" class="btn_save"><i class="fa-solid fa-pen-to-square"></i> Actualizar cliente</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
