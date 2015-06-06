<?php

	/**
	* Configuracion de la aplicacion.
	*/
	$SMM = array(
		// Modo de depuracion.
		'debug' => FALSE,
		// Titulo de la aplicacion.
		'title' => 'ShareMyMind',
		// Slogan de la aplicacion.
		'slogan' => '¡Comparte tus ideas!',
		// Lenguaje.
		'lang' => 'es',
		// Codificacion de caracteres.
		'charset' => 'UTF8',
		// Estilos css.
		'styles' => array(
			'css/bootstrap.min.css',
		),
		// Javascript
		'scripts' => array(
			'js/jquery.min.js',
			'js/bootstrap.min.js',
			'js/holder.js',
			'js/smm.js',
		),
		// Codificacion de la base de datos.
		'database' => array(
			// Host
			'host' => 'localhost',
			// Usuario
			'user' => 'root',
			// Password
			'password' => '',
			// Base de datos
			'database' => 'smm',
		),
		// Ruta completa a la carpeta raiz de la aplicacion.
		'app_path' => realpath(dirname(__FILE__)),
		// Modulos necesarios para el funcionamiento de la aplicacion.
		// Se deben de agregar en orden de dependencia.
		'auto_load' => array(
			'/libs/database.php',
			'/libs/auth.php',
			'/libs/redirect.php',
			'/libs/arrays.php',
		),
	);