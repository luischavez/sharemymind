<?php
	
	/**
	* Clase necesaria para manejar redirecciones,
	* cualquier cambio en esta clase cambia el comportamiento general de la aplicacion.
	*/
	class Redirect {

		function to($script) {
			header("location: $script");
			exit();
		}
	}

	/**
	* Objeto con las funciones necesarias para hacer redirecciones.
	*/
	$redirect = new Redirect;