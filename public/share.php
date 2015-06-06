<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';

	if ($auth->isLogged()) {
		if (isset($_POST['text'])) {
			$text = $_POST['text'];

			if (1 <= strlen($text)) {
				if (120 < strlen($text)) {
					$text = substr($text, 0, 120);
				}

				$database->share($arrays->value($auth->getCurrentUser(), 'user_id'), $text);
			}
		}
	}

	$redirect->to("board.php");
?>