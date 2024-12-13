<?php
session_start();
if ($_SESSION['rol']!=1 AND $_SESSION['rol']!=2) {

	header("location: ./");
	// code...
}

include "../con_nuevo.php";

if(!empty($_POST)){
	$alert='';
	if (empty($_POST['proveedor'])||empty($_POST['contacto'])|| empty($_POST['telefono'])||empty($_POST['direccion'])){
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		
		$proveedor=$_POST['proveedor'];
		$contacto=$_POST['contacto'];
		$telefono=$_POST['telefono'];
		$direccion=$_POST['direccion'];
		$usuario_id=$_SESSION['idUser'];//se consulta el id del master que lo registró//

			$query_insert=mysqli_query($connect,"INSERT INTO proveedor(proveedor, contacto,telefono,direccion,usuario_id)
				VALUES('$proveedor','$contacto','$telefono','$direccion','$usuario_id')");
			if($query_insert){
				$alert='<p class="msg_save">Proveedor guardado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al guardar al Proveedor</p>';
			}
	}
		mysqli_close($connect);
	}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
	<title>Registro de Proveedores</title>
</head>
<body class="provbody">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		<div class="form_register">
			<h1><i class="fa-solid fa-boxes-packing"></i> Registro de Proveedores</h1>
			<hr>
		<!--no se podia arreglar la relacion tuve que cambiar id_usuario por uno UPDATE `proveedor` SET `usuario_id` = '1' WHERE `proveedor`.`usuario_id` = 0-->
			<div class="alert"><?php echo isset($alert) ? $alert:''; ?></div>

			<form action="" method="POST">
				<label for="proveedor">Nombre del proveedor</label>
				<input type="text" name="proveedor" id="proveedor" placeholder="Nombre del proveedor">
				<label for="contacto">Contacto</label>
				<input type="text" name="contacto" placeholder="Nombre completo del contacto">
				<label for="telefono">Télefono</label>
				<input type="number" name="telefono" id="telefono" placeholder="Teléfono">
				<label for="direccion">Dirección</label>
				<input type="text" name="direccion" id="direccion" placeholder="Dirección completa">
				
			<button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk"></i> Guardar Proveedor</button>
			</form>
	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>