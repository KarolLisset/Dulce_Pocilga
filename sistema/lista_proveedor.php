<?php
session_start();
if ($_SESSION['rol']!=1 AND $_SESSION['rol']!=2) {

	header("location: ./");
	// code...
}

include '../con_nuevo.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
	<title>Lista de proveedores</title>
</head>
<body class="listas">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		
		<h1 class="Titulos"><i class="fa-solid fa-table-list"></i> Lista de provedorees</h1>
		<a href="registro_proveedor.php" class="btn_new"><i class="fa-solid fa-square-plus"></i> Crear proveedor</a>
		<form action="buscar_proveedor.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass"></i></button>
		</form>
		<table>
			<tr>
				<th>ID</th>
				<th>Proveedor</th>
				<th>Contacto</th>
				<th>Teléfono</th>
				<th>Dirección</th>
				<th>Fecha</th>
				<th>Acciones</th>
			</tr>
			<?php

			//Paginador

			$sql_registe=mysqli_query($connect, "SELECT count(*)as total_registro FROM proveedor WHERE estatus=1");

			$result_register=mysqli_fetch_array($sql_registe);
			$total_registro=$result_register['total_registro'];

			$por_pagina=5; //cuantos registros por página

			if(empty($_GET['pagina'])){

				$pagina=1;

			}else{

				$pagina=$_GET['pagina'];
			}

			$desde=($pagina-1)*$por_pagina;//inicia conteo
			$total_pagina=ceil($total_registro/$por_pagina);//limite del conteo

			//Se añadio el WHERE estatus=1 para que aparezcan solo los usuarios con estatus activo, si no se incluye el estatus en las tablas, no es necesario incluirlo
				$query=mysqli_query($connect, "SELECT * FROM proveedor WHERE estatus=1 ORDER BY codproveedor ASC LIMIT $desde,$por_pagina"); //Ordena el ID de manera ascendente c://
				mysqli_close($connect);

				$result=mysqli_num_rows($query);

				if($result>0){
					while($data=mysqli_fetch_array($query)){

						$formato='Y-m-d H:i:s';
						$fecha=DateTime::createFromFormat($formato,$data["date_add"]);

						?>
						<tr>
							<td><?php echo $data['codproveedor']?></td>
							<td><?php echo $data['proveedor']?></td>
							<td><?php echo $data['contacto']?></td>
							<td><?php echo $data['telefono']?></td>
							<td><?php echo $data['direccion']?>
							</td>
							<td><?php echo $fecha->format('d-m-Y');?>
							</td>
							<td><a class="link_edit" href="editar_proveedor.php?id=<?php echo $data['codproveedor']?>"> <!--Te lleva al id que vas a editar--><i class="fa-solid fa-pen-to-square"></i> Editar</a>
								|
							
							<a class="link_delete" href="eliminar_confirmar_proveedor.php?id=<?php echo $data['codproveedor']?>"><i class="fa-solid fa-trash"></i> Eliminar</a>
						</td>
						</tr>

							<?php

						}
					}

			?>

		</table>

		<div class="paginador">
			<ul>
				<?php 
				if($pagina !=1){


				?>
				<li><a href="?pagina=<?php echo 1; ?>"><i class="fa-solid fa-backward-step"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fa-solid fa-backward"></i></a></li> <!--Llevarme a la página anterior-->
<?php
		}
//for ($i=1; $i < $total_paginas+1; $i++)
		for($i=1; $i <= $total_pagina; $i++){
			if($i==$pagina){

				echo '<li class="pageSelected">'.$i.'</li>';

			}else{

				echo '<li><a href="?pagina='.$i.'">'.$i.'</a></li>';

			}
}

	if($pagina!=$total_pagina){



?>


				<li><a href="?pagina=<?php echo $pagina+1; ?>"><i class="fa-solid fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_pagina; ?>"><i class="fa-solid fa-forward-step"></i></a></li>
			<?php 
		}

			?>
			</ul>
		</div>	

	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>