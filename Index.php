<?php
$alert='';
session_start();
if(!empty($_SESSION['active'])){
	header('location:sistema/');
}else{

	if(!empty($_POST)){ //si existe

		if(empty($_POST['usuario']||empty($_POST['clave']))){
		$alert="Ingrese su usuario y su clave";

		}else{
			require_once "con.php";
			$user=mysqli_real_escape_string($connect,$_POST['usuario']);
			$pass=md5(mysqli_real_escape_string($connect,$_POST['clave']));
			//echo $pass;exit;

			$query = mysqli_query($connect,"SELECT u.idusuario,u.nombre,u.correo,u.usuario,r.idrol,r.rol 
             FROM usuario u
             INNER JOIN rol r
             ON u.rol = r.idrol
             WHERE u.usuario= '$user' AND u.clave = '$pass' AND u.estatus = 1 ");
			mysqli_close($connect);
			$result=mysqli_num_rows($query);

			if($result>0)
			{

				$data=mysqli_fetch_array($query);

				
				$_SESSION['active']=true;
				$_SESSION['idUser']=$data['idusuario'];
				$_SESSION['nombre']=$data['nombre'];
				$_SESSION['email']=$data['correo'];
				$_SESSION['user']=$data['usuario'];
				$_SESSION['rol']=$data['idrol'];
				$_SESSION['rol_name']=$data['rol'];

				header('location:sistema/');


			}else{
			$alert="El usuario o la contraseña son incorrectos";
			session_destroy();
			}
		}	

	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login | Sistema de Facturación</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

	<section id="container">
		<form action="" method="POST">
			<h3>Iniciar Sesión</h3>
			<img src="img/login.png" alt="Login">
			<input type="text" name="usuario" placeholder="Usuario">
			<input type="password" name="clave" placeholder="Contraseña">
			<div class="alert"><?php echo isset($alert) ? $alert:''; ?></div>
			<input type="submit" value="INGRESAR">
			
		</form>
	</section>

</body>
</html>