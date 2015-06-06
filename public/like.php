<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';

	/**
	* Creamos la respuesta.
	* Por defecto es fallida.
	*/
	$liked = FALSE;

	/**
	* Si el usuario esta logeado,
	* los datos son validos y el id del usuario concuerda con el usuario actual,
	* se crea o elimina el like.
	*/
	if ($auth->isLogged()) {
		if (isset($_POST['user_id']) && isset($_POST['share_id'])) {
			if ($_POST['user_id'] == $arrays->value($auth->getCurrentUser(), 'user_id')) {
				if ($database->isLikedBy($_POST['share_id'], $_POST['user_id'])) {
					$liked = !$database->unlike($_POST['share_id'], $_POST['user_id']);
				} else {
					$liked = $database->like($_POST['share_id'], $_POST['user_id']);
				}
			}
		}
	}

	$database->close();

	/**
	* Enviamos la respuesta.
	*/
	echo $liked;
?>