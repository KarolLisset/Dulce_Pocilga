<?php
session_start();
if ($_SESSION['rol']!=1 and $_SESSION['rol']!=2) {

	header("location: ./");
	// code...
}

include "../con.php";
if(!empty($_POST)){


	if(empty($_POST['idcliente']))
	{
		header("location: lista_clientes.php");
		mysqli_close($connect);
	}

	$idcliente=$_POST['idcliente'];
	//ELIMINAR UN REGISTRO $query_delete=mysqli_query($connect,"DELETE FROM usuario WHERE idusuario=$idusuario");//

	$query_delete=mysqli_query($connect, "UPDATE cliente SET estatus =0 WHERE idcliente=$idcliente");
	mysqli_close($connect);

	if($query_delete){

		header("location: lista_clientes.php");
	}else{

		echo "Error al eliminar";
	}

}
//Se añadio el segundo REQUEST para asegurarse que no puedan editarse usuarios con rol de administrador, ya que aunque no se muestre en la página principal si no se añade esta función se puede acceder por otros medios//
if(empty($_REQUEST['id'])){

	header("location: lista_clientes.php");
	mysqli_close($connect);

}else{

	//validar si el id a eliminar es existente

	$idcliente=$_REQUEST['id'];

	$query=mysqli_query($connect,"SELECT * FROM cliente WHERE idcliente=$idcliente");

	
mysqli_close($connect);
$result=mysqli_num_rows($query);
if($result>0){
	while ($data=mysqli_fetch_array($query)) {
		$nit=$data['nit'];
		$nombre=$data['nombre'];
		// code...
	}
}else{
	header("location: lista_clientes.php");
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
	<title>Eliminar Cliente</title>
</head>
<body>
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		<center>
			<br><br>
		<i class="fa-solid fa-user-xmark fa-6x" style="color:#c66262"></i></center>
		<div class="data_delete"><h2>¿Está seguro de eliminar el siguiente registro?</h2>
			<p>Nombre:<span><?php echo $nombre ?> </span></p>
			<p>Nit:<span><?php echo $nit ?> </span></p>
			
			<form method="POST" action="">
				<input type="hidden" name="idcliente" value="<?php echo $idcliente; ?>">
				<a href="lista_clientes.php" class="btn_cancel"><i class="fa-solid fa-ban"></i> Cancelar</a>
                <!--<input type="submit" name="Aceptar" class="btn_ok">-->
                <button type="submit" class="btn_ok"><i class="fa-solid fa-trash"></i> Eliminar</button>
			</form>

		</div>
	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>