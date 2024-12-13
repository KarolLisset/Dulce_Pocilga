<?php
session_start();
if ($_SESSION['rol']!=1) {

    header("location: ./");
    // code...
}

include "../con.php";

$alert = '';

// Validación del formulario
if (!empty($_POST)) {
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['rol'])) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    } else {
        $iduser = mysqli_real_escape_string($connect, $_POST['id']);
        $nombre = mysqli_real_escape_string($connect, $_POST['nombre']);
        $email = mysqli_real_escape_string($connect, $_POST['correo']);
        $user = mysqli_real_escape_string($connect, $_POST['usuario']);
        $clave = !empty($_POST['clave']) ? md5($_POST['clave']) : null;
        $rol = $_POST['rol'];

        // Comprobar si el correo o usuario ya existen para otro usuario
        $query = mysqli_query($connect, "SELECT * FROM usuario WHERE (usuario = '$user' OR correo = '$email') AND idusuario != $iduser");
        $result = mysqli_num_rows($query);

        if ($result > 0) {
            $alert = '<p class="msg_error">El correo o el usuario ya existe.</p>';
        } else {
            // Construir la consulta de actualización
            if ($clave) {
                $sql_update = "UPDATE usuario SET nombre = '$nombre', correo = '$email', usuario = '$user', clave = '$clave', rol = '$rol' WHERE idusuario = $iduser";
            } else {
                $sql_update = "UPDATE usuario SET nombre = '$nombre', correo = '$email', usuario = '$user', rol = '$rol' WHERE idusuario = $iduser";
            }

            $query_update = mysqli_query($connect, $sql_update);

            if ($query_update) {
                $alert = '<p class="msg_save">Usuario actualizado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al actualizar el usuario.</p>';
            }
        }
    }
}

// Mostrar datos para actualizar
if (empty($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
    header('location: lista_usuarios.php');
    exit();
}

$iduser = mysqli_real_escape_string($connect, $_REQUEST['id']);
$sql = mysqli_query($connect, "SELECT u.idusuario, u.nombre, u.correo, u.usuario, u.rol as idrol, r.rol as rol 
                               FROM usuario u 
                               INNER JOIN rol r ON u.rol = r.idrol 
                               WHERE u.idusuario = $iduser and estatus=1"); //se agrega el estatus para que no se puedan editar usuarios con estatus 0//

$result_sql = mysqli_num_rows($sql);

if ($result_sql == 0) {
    header('location: lista_usuarios.php');
    exit();
} else {
    $data = mysqli_fetch_array($sql);
    $nombre = $data['nombre'];
    $correo = $data['correo'];
    $usuario = $data['usuario'];
    $idrol = $data['idrol'];
    $rol = $data['rol'];

    // Opción seleccionada de rol
    $option = '<option value="' . $idrol . '" selected>' . $rol . '</option>';
}
mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scrips.php"; ?>
    <title>Actualizar Usuarios</title>
</head>
<body class="fondant">
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            
            <h1><i class="fa-solid fa-pen-to-square"></i>Actualizar usuarios</h1>
            <hr>
            
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="POST">
                <input type="hidden" name="id" value="<?php echo $iduser; ?>">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre completo" value="<?php echo htmlspecialchars($nombre); ?>">
                
                <label for="correo">Correo electrónico</label>
                <input type="email" name="correo" id="correo" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($correo); ?>">
                
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($usuario); ?>">
                
                <label for="clave">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Clave de acceso">
                
                <label for="rol">Tipo Usuario</label>
                <select name="rol" id="rol" class="notItemOne">
                    <?php

                    include "../con.php";
                        echo $option;
                        $query_rol = mysqli_query($connect, "SELECT * FROM rol");
                        mysqli_close($connect);
                        $result_rol = mysqli_num_rows($query_rol);

                        if ($result_rol > 0) {
                            while ($rol_data = mysqli_fetch_array($query_rol)) {
                                if ($rol_data['idrol'] != $idrol) {
                                    echo '<option value="' . $rol_data['idrol'] . '">' . $rol_data['rol'] . '</option>';
                                }
                            }
                        }
                    ?>
                </select>
                <!--<input type="submit" value="Actualizar usuario" class="btn_save">-->
                <button type="submit" class="btn_save"><i class="fa-solid fa-pen-to-square"></i> Actualizar usuario</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
