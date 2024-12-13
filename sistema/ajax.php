<?php

include'../con_nuevo.php';

session_start();

//print_r($_POST);
//exit;

if(!empty($_POST)){

	//Extraer datos del producto

	if($_POST['action']=='infoProducto'){

		$producto_id=$_POST['producto'];
		$query=mysqli_query($connect,"SELECT codproducto,descripcion,existencia,precio FROM producto WHERE
         codproducto=$producto_id AND estatus=1");

		mysqli_close($connect);

		$result=mysqli_num_rows($query);
		if($result>0){
			$data=mysqli_fetch_assoc($query);
			echo json_encode($data,JSON_UNESCAPED_UNICODE);
			exit;
		}
		echo 'error';
		exit;
	}
	//Agregar productos a entrada
	if($_POST['action']=='addProduct')
	{


		if(!empty($_POST['cantidad'])||!empty($_POST['precio'])||!empty($_POST['producto_id']))
		{
			$cantidad=$_POST['cantidad'];
			$precio=$_POST['precio'];
			$producto_id=$_POST['producto_id'];
			$usuario_id=$_SESSION['idUser'];

			$query_insert=mysqli_query($connect,"INSERT INTO entradas(codproducto, cantidad, precio,usuario_id) VALUES($producto_id,$cantidad,$precio,$usuario_id)");
			if($query_insert){

				//ejecutar procedimiento almacenado

				$query_upd=mysqli_query($connect, "CALL actualizar_precio_producto($cantidad,$precio,$producto_id)");
				$result_pro=mysqli_num_rows($query_upd);
				if($result_pro>0){

					$data=mysqli_fetch_assoc($query_upd);
					$data['producto_id']=$producto_id;
					echo json_encode($data,JSON_UNESCAPED_UNICODE);
					exit;
				}else{
					echo 'error';
				}
				mysqli_close($connect);
			}else{

				echo 'error';
			}
			exit;
		}

	}	//eliminar producto//

	if($_POST['action']=='delProduct'){

		if(empty($_POST['producto_id'])||!is_numeric($_POST['producto_id'])){

			echo 'error';
		}else{

		$idproducto = $_POST['producto_id'];
    // ELIMINAR UN REGISTRO $query_delete=mysqli_query($connect,"DELETE FROM usuario WHERE idusuario=$idusuario");// 

    $query_delete = mysqli_query($connect, "UPDATE producto SET estatus = 0 WHERE codproducto = $idproducto");
    mysqli_close($connect);

    if ($query_delete) {
        header("location: lista_proveedor.php");
        echo 'ok';
    } else {
        echo "Error al eliminar";
    }
    exit;
	}
	echo "error";
	exit;
}

//Buscar Cliente
if($_POST['action']=='searchCliente')
{
	if(!empty($_POST['cliente'])){
		$nit=$_POST['cliente'];
		$query=mysqli_query($connect, "SELECT * FROM cliente WHERE nit LIKE '$nit' and estatus=1");
		mysqli_close($connect);
		$result=mysqli_num_rows($query);

		$data='';

		if($result>0){

			$data=mysqli_fetch_assoc($query);
		}else{
			$data=0;
		}
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	exit;
}

//Registro Cliente-Ventas

if($_POST['action']=='addCliente')
{
	$nit=$_POST['nit_cliente'];
	$nombre=$_POST['nom_cliente'];
	$telefono=$_POST['tel_cliente'];
	$direccion=$_POST['dir_cliente'];
	$usuario_id=$_SESSION['idUser'];

	$query_insert=mysqli_query($connect,"INSERT INTO cliente(nit, nombre,telefono,direccion,usuario_id)
				VALUES('$nit','$nombre','$telefono','$direccion','$usuario_id')");

	if($query_insert){

		$codCliente=mysqli_insert_id($connect);
		$msg=$codCliente;

	}else{
		$msg='error';
	
    }
    mysqli_close($connect);
	echo $msg;
	exit;

}

    //Agregar producto al detalle temporal
if ($_POST['action'] == 'addProductoDetalle') {

    if (empty($_POST['producto']) || empty($_POST['cantidad'])) {

        echo 'error';
    } else {

        $codproducto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $token = md5($_SESSION['idUser']);

        $query_iva = mysqli_query($connect, "SELECT iva FROM configuracion");
        $result_iva = mysqli_num_rows($query_iva);

        $query_detalle_temp = mysqli_query($connect, "CALL add_detalle_temp($codproducto, $cantidad, '$token')"); // Token es cadena
        $result = mysqli_num_rows($query_detalle_temp);

        $detalleTabla = '';
        $sub_total = 0;
        $iva = 0;
        $total = 0;
        $arrayData = array();

        if ($result > 0) {

            if ($result_iva > 0) {
                $info_iva = mysqli_fetch_assoc($query_iva);
                $iva = $info_iva['iva'];
            }

            // Redondear
            while ($data = mysqli_fetch_assoc($query_detalle_temp)) {

                $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                $sub_total = round($sub_total + $precioTotal, 2);
                $total = round($total + $precioTotal, 2);

                $detalleTabla .= '<tr>
                    <td>' . $data['codproducto'] . '</td>
                    <td colspan="2">' . $data['descripcion'] . '</td>
                    <td class="textcenter">' . $data['cantidad'] . '</td>
                    <td class="textcenter">' . $data['precio_venta'] . '</td>
                    <td class="textright">' . $precioTotal . '</td>
                    <td class="">
                        <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle(' . $data['correlativo'] . ');"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>';
            }

            $impuesto = round($sub_total * ($iva / 100), 2);
            $tl_sniva = round($sub_total - $impuesto, 2);
            $total = round($tl_sniva + $impuesto, 2);

            $detalleTotales = '<tr>
                    <td colspan="5" class="textright">SUBTOTAL MXN.</td>
                    <td class="textright">' . $tl_sniva . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright1">IVA (' . $iva . '%)</td>
                    <td class="textright1">' . $impuesto . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright">TOTAL MXN.</td>
                    <td class="textright">' . $total . '</td>
                </tr>';

            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);

        } else {
            echo 'error';
        }
        mysqli_close($connect);
    }
    exit;
}

//Extraer datos del detalle temp
if ($_POST['action'] == 'searchForDetalle') {

    if (empty($_POST['user'])){

        echo 'error';
    } else {
        $token = md5($_SESSION['idUser']);

        $query=mysqli_query($connect,"SELECT tmp.correlativo, tmp.token_user, tmp.cantidad, tmp.precio_venta, p.codproducto, p.descripcion FROM detalle_temp tmp INNER JOIN producto p ON tmp.codproducto=p.codproducto WHERE token_user='$token'");


     $result = mysqli_num_rows($query);


        $query_iva = mysqli_query($connect, "SELECT iva FROM configuracion");
        $result_iva = mysqli_num_rows($query_iva);


        $detalleTabla = '';
        $sub_total = 0;
        $iva = 0;
        $total = 0;
        $arrayData = array();

        if ($result > 0) {

            if ($result_iva > 0) {
                $info_iva = mysqli_fetch_assoc($query_iva);
                $iva = $info_iva['iva'];
            }

            // Redondear
            while ($data = mysqli_fetch_assoc($query)) {

                $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                $sub_total = round($sub_total + $precioTotal, 2);
                $total = round($total + $precioTotal, 2);

                $detalleTabla .= '<tr>
                    <td>' . $data['codproducto'] . '</td>
                    <td colspan="2">' . $data['descripcion'] . '</td>
                    <td class="textcenter">' . $data['cantidad'] . '</td>
                    <td class="textcenter">' . $data['precio_venta'] . '</td>
                    <td class="textright">' . $precioTotal . '</td>
                    <td class="">
                        <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle(' . $data['correlativo'] . ');"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>';

                //Correlativo es la cantidad de piezas a adquirir
            }

            $impuesto = round($sub_total * ($iva / 100), 2);
            $tl_sniva = round($sub_total - $impuesto, 2);
            $total = round($tl_sniva + $impuesto, 2);

            $detalleTotales = '<tr>
                    <td colspan="5" class="textright">SUBTOTAL MXN.</td>
                    <td class="textright">' . $tl_sniva . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright1">IVA (' . $iva . '%)</td>
                    <td class="textright1">' . $impuesto . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright">TOTAL MXN.</td>
                    <td class="textright">' . $total . '</td>
                </tr>';

            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);

        } else {
            echo 'error';
        }
        mysqli_close($connect);
    }
    exit;
}

if($_POST['action']=='delProductoDetalle'){

    /*print_r($_POST);
    exit;*/
if (empty($_POST['id_detalle'])){

        echo 'error';
    } else {

        $id_detalle=$_POST['id_detalle'];
        $token = md5($_SESSION['idUser']);



        $query_iva = mysqli_query($connect, "SELECT iva FROM configuracion");
        $result_iva = mysqli_num_rows($query_iva);


        $query_detalle_temp=mysqli_query($connect, "CALL del_detalle_temp($id_detalle,'$token')");


        $result = mysqli_num_rows($query_detalle_temp);



        $detalleTabla = '';
        $sub_total = 0;
        $iva = 0;
        $total = 0;
        $arrayData = array();

        if ($result > 0) {

            if ($result_iva > 0) {
                $info_iva = mysqli_fetch_assoc($query_iva);
                $iva = $info_iva['iva'];
            }

            // Redondear
            while ($data = mysqli_fetch_assoc($query_detalle_temp)) {

                $precioTotal = round($data['cantidad'] * $data['precio_venta'], 2);
                $sub_total = round($sub_total + $precioTotal, 2);
                $total = round($total + $precioTotal, 2);

                $detalleTabla .= '<tr>
                    <td>' . $data['codproducto'] . '</td>
                    <td colspan="2">' . $data['descripcion'] . '</td>
                    <td class="textcenter">' . $data['cantidad'] . '</td>
                    <td class="textcenter">' . $data['precio_venta'] . '</td>
                    <td class="textright">' . $precioTotal . '</td>
                    <td class="">
                        <a href="#" class="link_delete" onclick="event.preventDefault(); del_product_detalle(' . $data['correlativo'] . ');"><i class="far fa-trash-alt"></i></a>
                    </td>
                </tr>';

                //Correlativo es la cantidad de piezas a adquirir
            }

            $impuesto = round($sub_total * ($iva / 100), 2);
            $tl_sniva = round($sub_total - $impuesto, 2);
            $total = round($tl_sniva + $impuesto, 2);

            $detalleTotales = '<tr>
                    <td colspan="5" class="textright">SUBTOTAL MXN.</td>
                    <td class="textright">' . $tl_sniva . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright1">IVA (' . $iva . '%)</td>
                    <td class="textright1">' . $impuesto . '</td>
                </tr>
                <tr>
                    <td colspan="5" class="textright">TOTAL MXN.</td>
                    <td class="textright">' . $total . '</td>
                </tr>';

            $arrayData['detalle'] = $detalleTabla;
            $arrayData['totales'] = $detalleTotales;

            echo json_encode($arrayData, JSON_UNESCAPED_UNICODE);

        } else {
            echo 'error';
        }
        mysqli_close($connect);
    }
    exit;

}


    //Anular Venta
    if($_POST['action']=='anularVenta'){

    $token=md5($_SESSION['idUser']);
    $query_del=mysqli_query($connect,"DELETE FROM detalle_temp WHERE token_user='$token'");
    mysqli_close($connect);
    if($query_del){

        echo 'Éxito en la operación';
    }else{
        echo 'Error en la operación';
    }
    exit;
}

//Procesar Venta
if($_POST['action']=='procesarVenta'){


    if(empty($_POST['codcliente'])){
        $codcliente=1;
    }else{
        $codcliente=$_POST['codcliente'];
    }

    $token=md5($_SESSION['idUser']);
    $usuario=$_SESSION['idUser'];

    $query=mysqli_query($connect,"SELECT * FROM detalle_temp WHERE token_user='$token'");
    $result=mysqli_num_rows($query);

    if($result>0){

        $query_procesar=mysqli_query($connect,"CALL procesar_venta($usuario,$codcliente,'$token')");
        $result_detalle=mysqli_num_rows($query_procesar);

        if($result_detalle>0){

            $data=mysqli_fetch_assoc($query_procesar);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{

            echo "errorsin";
        }
        }else{

            echo "error dos";
        }

        mysqli_close($connect);
        exit;
    
}
//Info Factura
if($_POST['action']=='infoFactura'){

    if(!empty($_POST['nofactura'])){

        $nofactura=$_POST['nofactura'];
        $query=mysqli_query($connect,"SELECT * FROM factura WHERE nofactura='$nofactura' AND estatus=1");
        mysqli_close($connect);

        $result=mysqli_num_rows($query);
        if($result>0){

            $data=mysqli_fetch_assoc($query);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    echo "error";
    exit;
}

//Anular Factura
if($_POST['action']=='anularFactura'){

    //print_r($_POST);
    //exit;
    if(!empty($_POST['noFactura'])){

        $noFactura=$_POST['noFactura'];

        $query_anular=mysqli_query($connect,"CALL anular_factura($noFactura)");
        mysqli_close($connect);
        $result=mysqli_num_rows($query_anular);

        if($result>0){
            $data=mysqli_fetch_assoc($query_anular);
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    echo "error";
    exit;
}
//Cambiar contraseña
if ($_POST['action'] == 'changePassword') {

    if (!empty($_POST['passActual']) && !empty($_POST['passNuevo'])) {

        $password = md5($_POST['passActual']);
        $newPass = md5($_POST['passNuevo']);
        $idUser = $_SESSION['idUser'];

        $code = '';
        $msg = '';
        $arrData = array();

        $query_user = mysqli_query($connect, "SELECT * FROM usuario WHERE clave='$password' AND idusuario=$idUser");

        $result = mysqli_num_rows($query_user);

        if ($result > 0) {

            $query_update = mysqli_query($connect, "UPDATE usuario SET clave='$newPass' WHERE idusuario=$idUser");
            mysqli_close($connect);

            if ($query_update) {
                $code = '00';
                $msg = "Su contraseña se ha actualizado con éxito";
            } else {
                $code = '2';
                $msg = "No es posible cambiar su contraseña.";
            }
        } else {
            $code = '1';
            $msg = "La contraseña actual es incorrecta";
        }

        $arrData = array('cod' => $code, 'msg' => $msg);
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);

    } else {
        echo "error";
    }

    exit;

}

}
exit;

//Primero actualizas la lista y llenas el precio y cantidad del producto, luego abres la consola; regresas a ajax.php y habilitas el print_r, le das en enviar al formulario e imprime el array yaaaaaaaay//

?>