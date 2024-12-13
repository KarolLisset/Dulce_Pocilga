<?php
session_start();

include '../con.php';

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<?php
	include "includes/scrips.php";
	?>
	<title>Lista de productos</title>
</head>
<body class="listas">
	<?php
		include "includes/header.php";
	?>
	<section id="container">
		
		<h1 class="Titulos"><i class="fas fa-cubes"></i> Lista de productos</h1>
		<a href="registro_producto.php" class="btn_new"><i class="fas fa-plus"></i> Registrar producto</a>
		<form action="buscar_productos.php" method="get" class="form_search">
			<input type="text" name="busqueda" id="busqueda" placeholder="Buscar">
			<button type="submit" class="btn_search"><i class="fa-solid fa-magnifying-glass"></i></button>
		</form>
		<table>
			<tr>
				<th>Código</th>
				<th>Descripción</th>
				<th>Precio</th>
				<th>Existencia</th>

				<th>
					<!--Muestra los proveedores-->
					<?php
	$query_proveedor = mysqli_query($connect, "SELECT codproveedor, proveedor FROM proveedor WHERE estatus=1 ORDER BY proveedor ASC");
	$result_proveedor = mysqli_num_rows($query_proveedor);
?>
<select name="proveedor" id="search_proveedor">
	<option value="" selected>PROVEEDOR</option>
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
				</th>
				
				<th>Foto</th>
				<th>Acciones</th>
			</tr>
			<?php

			//Paginador

			$sql_registe=mysqli_query($connect, "SELECT count(*)as total_registro FROM producto WHERE estatus=1");

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
				$query=mysqli_query($connect, "SELECT p.codproducto, p.descripcion, p.precio, p.existencia, pr.proveedor, p.foto FROM producto p INNER JOIN proveedor pr ON p.proveedor=pr.codproveedor WHERE p.estatus=1 ORDER BY p.codproducto DESC LIMIT $desde,$por_pagina"); //Ordena el ID de manera ascendente c://
				mysqli_close($connect);

				$result=mysqli_num_rows($query);

				if($result>0){
					while($data=mysqli_fetch_array($query)){

						if($data['foto']!='img_producto'){//img_producto.png

							$foto='img/uploads/'.$data['foto'];
						}else{
							$foto='img/'.$data['foto'];
						}

						?>
						 <tr class="row<?php echo$data["codproducto"]; ?>"><!--todas las filas tienen una clase diferente-->
							<td><?php echo $data['codproducto']?></td>
							<td><?php echo $data['descripcion']?></td>
							<td class="celPrecio"><?php echo $data['precio']?></td>
							<td class="celExistencia"><?php echo $data['existencia']?></td>
							<td><?php echo $data['proveedor']?></td>
							<td class="img_producto"><img src="<?php echo $foto; ?>" alt="<?php echo $data['descripcion']?>"></td>
							<?php if($_SESSION['rol']==1 || $_SESSION['rol']==2){ ?>
								|
							<td>
								<a class="link_add add_product" href="#" product="<?php echo $data['codproducto'] ?>"> 
    <i class="fas fa-plus"></i> Agregar
</a>

								|
								<a class="link_edit" href="editar_producto.php?id=<?php echo $data['codproducto']?>"> <!--Te lleva al id que vas a editar--><i class="fa-solid fa-pen-to-square"></i> Editar</a>

								|
							
							<a class="link_delete del_product" href="#" product="<?php echo $data['codproducto'] ?>"><i class="fa-solid fa-trash"></i> Eliminar</a>
						</td>
					<?php } ?>
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
				<li><a href="?pagina=<?php echo 1; ?>"><i class="fas fa-step-backward"></i></a></li>
				<li><a href="?pagina=<?php echo $pagina-1; ?>"><i class="fas fa-backward"></i></a></li> <!--Llevarme a la página anterior-->
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


				<li><a href="?pagina=<?php echo $pagina+1; ?>"><i class="fas fa-forward"></i></a></li>
				<li><a href="?pagina=<?php echo $total_pagina; ?>"><i class="fas fa-step-forward"></i></a></li>
			<?php 
		}

			?>
			</ul>
		</div>	

	</section>
	<?php include "includes/footer.php"; ?>

</body>
</html>