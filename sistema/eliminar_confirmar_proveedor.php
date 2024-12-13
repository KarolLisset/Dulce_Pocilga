<?php
session_start();
if ($_SESSION['rol']!=1 and $_SESSION['rol']!=2) {

    header("location: ./");
    // code...
}

include "../con.php";

if (!empty($_POST)) {

    // Se usa para tratar vulnerabilidades del sistema en inspeccionar elemento (value == 1)
    if (empty($_POST['idproveedor'])) {
        header("location: lista_proveedor.php");
        mysqli_close($connect);
    }

    $idproveedor = $_POST['idproveedor'];
    // ELIMINAR UN REGISTRO $query_delete=mysqli_query($connect,"DELETE FROM usuario WHERE idusuario=$idusuario");// 

    $query_delete = mysqli_query($connect, "UPDATE proveedor SET estatus = 0 WHERE codproveedor = $idproveedor");
    mysqli_close($connect);

    if ($query_delete) {
        header("location: lista_proveedor.php");
    } else {
        echo "Error al eliminar";
    }
}

// Se añadió el segundo REQUEST para asegurarse que no puedan editarse usuarios con rol de administrador
if (empty($_REQUEST['id'])) {
    header("location: lista_proveedor.php");
    mysqli_close($connect);

} else {

    // Validar si el id a eliminar es existente
    $idproveedor = $_REQUEST['id'];
    $query = mysqli_query($connect, "SELECT *FROM proveedor WHERE codproveedor = $idproveedor");

    mysqli_close($connect);
    $result = mysqli_num_rows($query);

    if ($result > 0) {
        while ($data = mysqli_fetch_array($query)) {
            $proveedor = $data['proveedor'];
        }
    } else {
        header("location: lista_proveedor.php");
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
    <title>Eliminar Proveedor</title>
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
            <p>Nombre del Proveedor: <span><?php echo $proveedor; ?></span></p>
            <form method="POST" action="">
                <input type="hidden" name="idproveedor" value="<?php echo $idproveedor; ?>">
                <a href="lista_proveedor.php" class="btn_cancel"><i class="fa-solid fa-ban"></i> Cancelar</a>
                <!--<input type="submit" name="Aceptar" class="btn_ok">-->
                <button type="submit" class="btn_ok"><i class="fa-solid fa-trash"></i> Eliminar</button>
            </form> 
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
