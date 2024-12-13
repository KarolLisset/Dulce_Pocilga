<?php
session_start();
if ($_SESSION['rol']!=1) {

	header("location: ./");
	// code...
}
include "../con.php";

if(!empty($_POST)){
	$alert='';
	if (empty($_POST['nombre'])|| empty($_POST['correo'])||empty($_POST['usuario'])||empty($_POST['clave'])||empty($_POST['rol'])) {
		$alert='<p class="msg_error">Todos los campos son obligatorios.</p>';
	}else{
		

		$nombre=$_POST['nombre'];
		$email=$_POST['correo'];
		$user=$_POST['usuario'];
		$clave=md5($_POST['clave']);
		$rol=$_POST['rol'];

		$query=mysqli_query($connect,"SELECT * FROM usuario WHERE usuario='$user' OR correo='$email'");

		$result=mysqli_fetch_array($query);

		if($result>0){
			$alert='<p class="msg_error">El correo o el usuario ya existe.</p>';
		}else{
			$query_insert=mysqli_query($connect,"INSERT INTO usuario(nombre,correo,usuario,clave,rol)
				VALUES('$nombre','$email','$user','$clave','$rol')");
			if($query_insert){
				$alert='<p class="msg_save">Usuario creado correctamente.</p>';
			}else{
				$alert='<p class="msg_error">Error al crear al usuario</p>';
			}
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
	<title>Registro de Usuarios</title>
</head>
<body class="fondant">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		<div class="form_register">
			<h1><i class="fa-solid fa-user-plus"></i> Registro usuario</h1>
			<hr>
			
			<div class="alert"><?php echo isset($alert) ? $alert:''; ?></div>

			<form action="" method="POST">
				<label for="nombre">Nombre</label>
				<input type="text" name="nombre" placeholder="Nombre completo">
				<label for="correo">Correo electrónico</label>
				<input type="email" name="correo" id="correo" placeholder="Correo electrónico">
				<label for="usuario">Usuario</label>
				<input type="text" name="usuario" id="usuario" placeholder="Usuario">
				<label for="clave">Clave</label>
				<input type="password" name="clave" id="clave" placeholder="Clave de acceso">
				<label for="rol">Tipo Usuario</label>
				<!---Esto lo que hace es jalar los roles desde la bdd--->
				<?php

				$query_rol=mysqli_query($connect, "SELECT * FROM rol");
				mysqli_close($connect);

				$result_rol=mysqli_num_rows($query_rol);
				

			
				?>
				<select name="rol" id="rol">
					<?php

				if ($result_rol>0) {
					while ($rol=mysqli_fetch_array($query_rol)) {
						?>
						<option value="<?php echo $rol["idrol"];?>"><?php echo $rol["rol"]; ?></option>

						<?php
						 
					
					}

				}
				?>


					
				
			</select>
			<button type="submit"class="btn_save"><i class="fa-solid fa-floppy-disk"></i> Crear usuario</button>
			</form>
	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>