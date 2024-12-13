<?php
session_start();

include "../con.php";

if (!empty($_POST)) {
    $alert = '';
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<p class="msg_error">Todos los campos son obligatorios.</p>';
    } else {
        $nit = $_POST['nit'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $usuario_id = $_SESSION['idUser']; // Se consulta el id del master que lo registró

        // Validar si el nit ya existe
        $result = 0;

        if (is_numeric($nit) && $nit != 0) {
            $query = mysqli_query($connect, "SELECT * FROM cliente WHERE nit='$nit'");
            $result = mysqli_num_rows($query); // Verificar el número de filas
        }

        if ($result > 0) {
            $alert = '<p class="msg_error">El número de NIT ya existe.</p>';
        } else {
            $query_insert = mysqli_query($connect, "INSERT INTO cliente(nit, nombre, telefono, direccion, usuario_id)
                VALUES('$nit', '$nombre', '$telefono', '$direccion', '$usuario_id')");
            if ($query_insert) {
                $alert = '<p class="msg_save">Cliente guardado correctamente.</p>';
            } else {
                $alert = '<p class="msg_error">Error al guardar al cliente.</p>';
            }
        }
    }
    mysqli_close($connect); // Asegurarse de cerrar la conexión siempre
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <?php include "includes/scrips.php"; ?>
    <title>Registro de Clientes</title>
</head>
<body class="clientesbody">
    <?php include "includes/header.php"; ?>
    <section id="container">
        <div class="form_register">
            <h1><i class="fa-solid fa-user-plus"></i> Registro de clientes</h1>
            <hr>
            
            <div class="alert"><?php echo isset($alert) ? $alert : ''; ?></div>

            <form action="" method="POST">
                <label for="nit">NIT</label>
                <input type="text" name="nit" id="nit" placeholder="Número de NIT" maxlength="11">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre completo">
                <label for="telefono">Teléfono</label>
                    <input type="number" name="telefono" id="telefono" placeholder="Teléfono" min="0">
                    <script>
                    document.getElementById('telefono').addEventListener('input', function () {
       
                        if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                         }
                        });
                    </script>

                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
                
                <button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk"></i> Guardar cliente</button>
            </form>
        </div>
    </section>
    <?php include "includes/footer.php"; ?>
</body>
</html>
