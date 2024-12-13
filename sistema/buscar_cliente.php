<?php
session_start();

include '../con_nuevo.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
	<title>Lista de Clientes</title>
</head>
<body class="listas">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		<?php

			$busqueda=strtolower($_REQUEST['busqueda']);
			if(empty($busqueda)){

				header('location:lista_clientes.php/');
				mysqli_close($connect);
			}


		?>
		
		<h1 class="Titulos"><i class="fa-solid fa-table-list"></i> Lista de clientes</h1>
		<a href="registro_cliente.php" class="btn_new"><i class="fa-solid fa-user-plus"></i> Crear cliente</a>
		<form action="buscar_cliente.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar" value="<?php echo $busqueda; ?>" required>
			<button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass"></i></button>
		</form>
		<table>
			<tr>
				<th>ID</th>
				<th>Nit</th>
				<th>Nombre</th>
				<th>Teléfono</th>
				<th>Dirección</th>
				<th>Acciones</th>
			</tr>
			<?php

			//Paginador
      //Agarra el valor del buscador y lo aplica a cada campo hasta que haya una similitud//
			$sql_registe=mysqli_query($connect, "SELECT count(*)as total_registro FROM cliente WHERE (idcliente LIKE '%$busqueda%' OR nit LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%' OR telefono LIKE '%$busqueda%' OR direccion LIKE '%$busqueda%')AND estatus=1");
			//no es necesario agregar OR al rol pues ya fue agregado en el bloque anterior//
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
				$query=mysqli_query($connect, "SELECT * FROM cliente
				 WHERE
				(idcliente LIKE '%$busqueda%' OR
				 nit LIKE '%$busqueda%' OR
				 nombre LIKE '%$busqueda%' OR
				 telefono LIKE '%$busqueda%' OR 
				 direccion LIKE '%$busqueda%')
				 AND
				 estatus=1 ORDER BY idcliente ASC LIMIT $desde,$por_pagina"); //Ordena el ID de manera ascendente c://

				mysqli_close($connect);

				$result=mysqli_num_rows($query);

				if($result>0){
					while($data=mysqli_fetch_array($query)){


						?>
						<tr>
							<td><?php echo $data['idcliente']?></td>
							<td><?php echo $data['nit']?></td>
							<td><?php echo $data['nombre']?></td>
							<td><?php echo $data['telefono']?></td>
							<td><?php echo $data['direccion']?></td>
							<td><a class="link_edit" href="editar_cliente.php?id=<?php echo $data['idcliente']?>"> <!--Te lleva al id que vas a editar--><i class="fa-solid fa-pen-to-square"></i> Editar</a>

							
							<?php if($_SESSION['rol']==1 || $_SESSION['rol']==2){ ?>
								
							 |
							<a class="link_delete" href="eliminar_confirmar_cliente.php?id=<?php echo $data['idcliente']?>"><i class="fa-solid fa-trash"></i> Eliminar</a>
						<?php } ?>
						</td>
						</tr>

							<?php
					}
				}

			?>

		</table>

		<?php
		//no aparece el paginador de no ser necesario//
		if($total_registro!=0){


		?>

		<div class="paginador">
			<ul>
				<?php 
				if($pagina !=1){


				?>
				<li><a href="?pagina=<?php echo 1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fas fa-backward"></i></a></li> <!--Llevarme a la página anterior-->
<?php
		}
//for ($i=1; $i < $total_paginas+1; $i++)
		for($i=1; $i <= $total_pagina; $i++){
			if($i==$pagina){

				echo '<li class="pageSelected">'.$i.'</li>';

			}else{

				echo '<li><a href="?pagina=' . $i . '&busqueda=' . $busqueda . '">' . $i . '</a></li>';


			}
}

	if($pagina!=$total_pagina){



?>


				<li><a href="?pagina=<?php echo $pagina+1; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fa-solid fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_pagina; ?>&busqueda=<?php echo $busqueda; ?>"><i class="fa-solid fa-forward-step"></i></a></li>
			<?php 
		}

			?>
			</ul>
		</div>
	<?php
	}
	?>

	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>