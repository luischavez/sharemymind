<?php
	/**
	* Iniciamos la sesion.
	*/
	session_start();

	/**
	* Incluimos el archivo de configuracion.
	*/
	require_once '../config.php';

	/**
	* Cargamos todos los modulos de la aplicacion.
	*/
	foreach ($SMM['auto_load'] as $module) {
		require_once $SMM['app_path'] . $module;
	}

	/**
	* Conectamos la base de datos.
	*/
	$database->connect();

	/**
	* Verifica si existe la conexion con la base de datos.
	* Si no existe termina la ejecucion.
	*/
	if (!$database->isConnected()) {
		die('No se puede realizar la conexion con la base de datos');
	}

	/**
	* Inicia el sistema de sesion.
	*/
	$auth->init();

	/**
	* Si esta en modo debug creamos las variables necesarias.
	* Ademas iniciamos sesion en modo debug,
	* en modo debug no es necesario establecer el password del usuario.
	*/
	if ($SMM['debug']) {
		$GLOBALS['query_log'] = array();

		$auth->login('luis', '');
	}

	/**
	* Contamos el numero de visita.
	*/
	$_SESSION['visit'] = isset($_SESSION['visit']) ? $_SESSION['visit'] + 1 : 1;

	/**
	* Si es la primera visita se redirecciona a la pagina de bienvenida.
	*/
	if (1 == $_SESSION['visit']) {
		$redirect->to('welcome.php');
	}

	/**
	* Nombre del script que solicito el usuario.
	*/
	$script = basename($_SERVER['SCRIPT_FILENAME']);

	/**
	* Si solicita el perfil, la pizarra o el logout y no esta autentificado,
	* se redirecciona a la pagina de bienvenida.
	*/
	if ('profile.php' === $script || 'board.php' === $script  || 'logout.php' === $script) {
		if (!$auth->isLogged()) {
			$redirect->to('welcome.php');
		}
	}

	/**
	* Si solicita la pagina de bienvenida o login
	* y esta autentificado, se redirecciona a la pagina principal.
	*/
	if ('welcome.php' === $script || 'login.php' === $script) {
		if ($auth->isLogged()) {
			$redirect->to('index.php');
		}
	}