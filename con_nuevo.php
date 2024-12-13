<?php

$host='localhost';
$user='root';
$password= '';
$db='facturacion';

$connect=mysqli_connect($host,$user,$password,$db);

//mysqli_close($connect);

if (!$connect) {
		echo "<script> alert('Conexion no establecida');</script>";
	}
?>