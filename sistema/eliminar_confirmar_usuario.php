<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header("location: ./");
    // code...
}

include "../con_nuevo.php";
if (!empty($_POST)) {

    // Se usa para tratar vulnerabilidades del sistema en inspeccionar elemento (value == 1)
    if ($_POST['idusuario'] == 1) {
        header("location: lista_usuarios.php");
        mysqli_close($connect);
        exit;
    }

    $idusuario = $_POST['idusuario'];
    // ELIMINAR UN REGISTRO $query_delete=mysqli_query($connect,"DELETE FROM usuario WHERE idusuario=$idusuario");// 

    $query_delete = mysqli_query($connect, "UPDATE usuario SET estatus = 0 WHERE idusuario = $idusuario");
    mysqli_close($connect);

    if ($query_delete) {
        header("location: lista_usuarios.php");
    } else {
        echo "Error al eliminar";
    }
}

// Se añadió el segundo REQUEST para asegurarse que no puedan editarse usuarios con rol de administrador
if (empty($_REQUEST['id']) || $_REQUEST['id'] == 1) {
    header("location: lista_usuarios.php");
    mysqli_close($connect);

} else {

    // Validar si el id a eliminar es existente
    $idusuario = $_REQUEST['id'];
    $query = mysqli_query($connect, "SELECT u.nombre, u.usuario, r.rol FROM usuario u INNER JOIN rol r ON u.rol = r.idrol WHERE u.idusuario = $idusuario");

    mysqli_close($connect);
    $result = mysqli_num_rows($query);

    if ($result > 0) {
        while ($data = mysqli_fetch_array($query)) {
            $nombre = $data['nombre'];
            $usuario = $data['usuario'];
            $rol = $data['rol'];
            // code...
        }
    } else {
        header("location: lista_usuarios.php");
    }
} // <--- Esta llave de cierre es la que faltaba
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php
    include "includes/scrips.php";
    ?>
    <title>Eliminar usuario</title>
</head>
<body>
    <?php
        include "includes/header.php";
    ?>
    <section id="container">
        <div class="data_delete">
            <i class="fa-solid fa-user-xmark fa-6x" style="color:#c66262"></i>
            <br><br>
            <h2>¿Está seguro de eliminar el siguiente registro?</h2>
            <p>Nombre: <span><?php echo $nombre; ?></span></p>
            <p>Usuario: <span><?php echo $usuario; ?></span></p>
            <p>Rol: <span><?php echo $rol; ?></span></p>
            <form method="POST" action="">
                <input type="hidden" name="idusuario" value="<?php echo $idusuario; ?>">
                <a href="lista_usuarios.php" class="btn_cancel"><i class="fa-solid fa-ban"></i> Cancelar</a>
                <!--<input type="submit" name="Aceptar" class="btn_ok">-->
                <button type="submit" class="btn_ok"><i class="fa-solid fa-trash"></i> Eliminar</button>
            </form> 
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
