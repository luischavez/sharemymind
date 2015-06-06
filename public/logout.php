<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';

	if ($auth->isLogged()) {
		$auth->logout();
	}

	$redirect->to('welcome.php');
?>