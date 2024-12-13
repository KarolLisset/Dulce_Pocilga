	<?php
	if(empty($_SESSION['active'])){
	header('location:../');
	} //privada
	?>

	<header>
		<div class="header">
			
			<h1>Sistema de Administración</h1>
			<div class="optionsBar">
				<p>Estado de México, <?php echo fechaC();?></p>
				<span>|</span>
				<span class="user"><?php echo $_SESSION['user'].'-'.$_SESSION['rol']; ?></span></a>
				<a href="Configuracion.php"><img class="photouser" src="img/user.png" alt="Usuario"></a>
				<a href="salir.php"><img class="close" src="img/salir.png" alt="Salir del sistema" title="Salir"></a>
			</div>
		</div>
		<?php include "nav.php"; ?>
	</header>
	<div class="modal">
    <div class="bodyModal">
 
    </div>
</div>
