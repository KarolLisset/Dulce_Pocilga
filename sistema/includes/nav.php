<nav>
			<ul>
				<li><a href="index.php"><i class="fa-solid fa-house-chimney-user"></i> Inicio</a></li>
			<?php

		if ($_SESSION['rol']==1) {


					?>
				<li class="principal">
					<a href="#"><i class="fa-solid fa-user-large"></i> Usuarios</a>
					<ul>
						<li><a href="registro_usuario.php"><i class="fa-solid fa-user-plus"></i> Nuevo Usuario</a></li>
						<li><a href="lista_usuarios.php"><i class="fa-solid fa-user-group"></i> Lista de Usuarios</a></li>
					</ul>
				</li>
				<?php 
			}

				?>
				<li class="principal">
					<a href="#"><i class="fa-solid fa-user-large"></i> Clientes</a>
					<ul>
						<li><a href="registro_cliente.php"><i class="fa-solid fa-user-plus"></i> Nuevo Cliente</a></li>
						<li><a href="lista_clientes.php"><i class="fa-solid fa-user-group"></i> Lista de Clientes</a></li>
					</ul>
				</li>
		<?php

		if ($_SESSION['rol']==1 || $_SESSION['rol']==2) {


					?>

				<li class="principal">
					<a href="#"><i class="fa-solid fa-boxes-packing"></i> Proveedores</a>
					<ul>
						<li><a href="registro_proveedor.php"><i class="fa-solid fa-square-plus"></i> Nuevo Proveedor</a></li>
						<li><a href="lista_proveedor.php"><i class="fa-solid fa-table-list"></i> Lista de Proveedores</a></li>
					</ul>
				</li>
				<?php 
			}

				?>

				<li class="principal">
					<a href="#"><i class="fas fa-cubes"></i> Productos</a>
					<ul>
						<?php
						if ($_SESSION['rol']==1 || $_SESSION['rol']==2) {
					?>
						<li><a href="registro_producto.php"><i class="fas fa-plus"></i> Nuevo Producto</a></li>
					<?php 
						}
						?>

						<li><a href="lista_producto.php"><i class="fa-solid fa-table-list"></i> Lista de Productos</a></li>
					</ul>
				</li>
				<li class="principal">
					<a href="#"><i class="far fa-file-alt"></i> Ventas</a>
					<ul>
						<li><a href="nueva_venta.php"><i class="fas fa-plus"></i> Nueva Venta</a></li>
						<li><a href="ventas.php"><i class="fa-solid fa-table-list"></i> Ventas</a></li>
					</ul>
				</li>
			</ul>
		</nav>