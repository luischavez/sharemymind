<?php

	/**
	* Clase con las funciones necesarias para manejar la autentificacion de los usuarios.
	*/
	class Auth {

		/**
		* Funciones para manejar la base de datos.
		*/
		protected $database;

		function __construct() {
			$this->database = $GLOBALS['database'];
		}

		/**
		* Inicializa el sistema de sesiones.
		* Si existen las cookies y son validas crea la sesion del usuario,
		* si no son validas borra las cookies.
		*/
		function init() {
			if (isset($_SESSION['user'])) {
				$_SESSION['user'] = $this->database->getUserWithId($_SESSION['user']['user_id']);
				return;
			}

			if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_token'])) {
				$user = $this->database->getUserWithUserName($_COOKIE['user_id']);

				if (isset($user['token']) && $user['token'] === $_COOKIE['user_token']) {
					$_SESSION['user'] = $user;
				} else {
					$this->logout(TRUE);
				}
			}
		}

		/**
		* Verifica las credenciales del usuario y si son correctas realiza el login.
		* Si remember es TRUE almacena las cookies necesarias.
		* Si esta en modo de depuracion no guarda cookies.
		*/
		function login($userName, $password, $remember = FALSE) {
			if (isset($_SESSION['user'])) {
				return TRUE;
			}

			$user = $this->database->getUserWithUserName($userName);

			if (!$user) {
				return FALSE;
			}

			if ($GLOBALS['SMM']['debug'] || $user['password'] === md5($password)) {
				if ($remember) {
					$token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
					
					if ($this->database->setUserToken($user['user_id'], $token)) {
						if (!$GLOBALS['SMM']['debug']) {
							setcookie('user_id', $user['user_id'], time() + (60 * 60 * 24 * 365));
							setcookie('user_token', $token, time() + (60 * 60 * 24 * 365));
						}
					}
				}

				$_SESSION['user'] = $this->database->getUserWithUserName($userName);

				return TRUE;
			}

			return FALSE;
		}

		/**
		* Elimina la sesion del usuario y borra las cookies.
		* Si $onlyCookies es TRUE solo se eliminan las cookies.
		*/
		function logout($onlyCookies = FALSE) {
			if (isset($_SESSION['user']) && !$onlyCookies) {
				$user = $_SESSION['user'];
				unset($_SESSION['user']);
			
				$this->database->setUserToken($user['user_id'], NULL);
			}

			setcookie('user_id', NULL, time() - (60 * 60 * 24 * 365));
			setcookie('user_token', NULL, time() - (60 * 60 * 24 * 365));
		}

		/**
		* Regresa el usuario que esta autentificado actualmente.
		*/
		function getCurrentUser() {
			return isset($_SESSION['user']) ? $_SESSION['user'] : NULL;
		}

		/**
		* Verifica si el usuario esta autentificado.
		*/
		function isLogged() {
			return NULL != $this->getCurrentUser();
		}
	}

	/**
	* Objeto con las funciones de autentificacion.
	*/
	$auth = new Auth;