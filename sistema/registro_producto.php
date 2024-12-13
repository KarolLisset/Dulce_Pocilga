<?php
session_start();
if ($_SESSION['rol']!=1 AND $_SESSION['rol']!=2) {

	header("location: ./");
	// code...
}

include "../con_nuevo.php";

if(!empty($_POST)){

	//print_r($_FILES); o $_POST para comprobar que lleva post.
	$alert='';
	if (empty($_POST['proveedor'])||empty($_POST['producto'])|| empty($_POST['precio'])||empty($_POST['cantidad'])){
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		
		$proveedor=$_POST['proveedor'];
		$producto=$_POST['producto'];
		$precio=$_POST['precio'];
		$cantidad=$_POST['cantidad'];
		$usuario_id=$_SESSION['idUser'];//se consulta el id del master que lo registró//
		$foto = $_FILES['foto'];
		$nombre_foto=$foto['name'];
		$type=$foto['type'];
		$url_temp=$foto['tmp_name'];

		$imgProducto='img_producto.png'; //lo que se almacena cuando no lleva foto

		if($nombre_foto!=''){

		$destino='img/uploads/';
		$img_nombre='img_'.md5(date('d-m-Y H:m:s'));
		$imgProducto=$img_nombre.'.jpg';
		$src=$destino.$imgProducto;		
		}

			$query_insert=mysqli_query($connect,"INSERT INTO producto(proveedor, descripcion,precio,existencia,usuario_id,foto)
				VALUES('$proveedor','$producto','$precio','$cantidad','$usuario_id','$imgProducto')");

			if($query_insert){
				if($nombre_foto!=''){
					move_uploaded_file($url_temp,$src);
				}
				$alert='<p class="msg_save">Producto guardado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al guardar Producto</p>';
			}
	}
	}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
	<title>Registro de Productos</title>
</head>
<body class="prodbody">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		<div class="form_register">
			<h1><i class="fas fa-cubes"></i> Registro de Productos</h1>
			<hr>
		<!--no se podia arreglar la relacion tuve que cambiar id_usuario por uno UPDATE `proveedor` SET `usuario_id` = '1' WHERE `proveedor`.`usuario_id` = 0-->
			<div class="alert"><?php echo isset($alert) ? $alert:''; ?></div>

			<form action="" method="POST" enctype="multipart/form-data"><!--adjuntar archivo-->
				<label for="proveedor">Nombre del proveedor</label>

				<?php
	$query_proveedor = mysqli_query($connect, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus=1 ORDER BY proveedor ASC");
	$result_proveedor = mysqli_num_rows($query_proveedor);
	mysqli_close($connect);
?>
<select name="proveedor" id="proveedor">
	<?php
		if ($result_proveedor > 0) {
			while ($proveedor = mysqli_fetch_array($query_proveedor)) {
				?>
				<option value="<?php echo $proveedor['codproveedor']; ?>">
					<?php echo $proveedor['proveedor']; ?>
				</option>
				<?php
			}
		}
	?>
</select>

				</select>
				<label for="producto">Producto</label>
				<input type="text" name="producto" placeholder="Nombre del producto">
				<label for="precio">Precio</label>
				<input type="number" name="precio" id="precio" step="0.01" min="0" placeholder="Precio del producto">
				<label for="cantidad">Cantidad</label>
				<input type="number" min="1" name="cantidad" id="cantidad" placeholder="Cantidad del producto">
				<div class="photo">
				<label for="foto">Foto</label>
        		<div class="prevPhoto">
        		<span class="delPhoto notBlock">X</span>
       		 	<label for="foto"></label>
        		</div>
        		<div class="upimg">
        		<input type="file" name="foto" id="foto">
        		</div>
        		<div id="form_alert"></div>
</div>

				
			<button type="submit" class="btn_save"><i class="fa-solid fa-floppy-disk"></i> Guardar Producto</button>
			</form>
	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>