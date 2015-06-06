<?php

	/**
	* Clase que contiene las funciones para manejar la base de datos.
	*/
	class Database {

		/**
		* Conexion a la base de datos.
		*/
		protected $connection = NULL;

		/**
		* Funcion para crear una nueva conexion.
		*/
		function connect() {
			$host = $GLOBALS['SMM']['database']['host'];
			$user = $GLOBALS['SMM']['database']['user'];
			$password = $GLOBALS['SMM']['database']['password'];
			$database = $GLOBALS['SMM']['database']['database'];
			$charset = $GLOBALS['SMM']['charset'];

			$this->connection = mysql_connect($host, $user, $password);
			
			mysql_set_charset($charset);

			if (!mysql_select_db($database, $this->connection)) {
				$this->createDatabase();
			}
		}

		/**
		* Funcion para liberar la conexion.
		*/
		function close() {
			if (NULL != $this->connection) {
				mysql_close($this->connection);
			}
		}

		/**
		* Verifica si existe una conexion valida.
		*/
		function isConnected() {
			return NULL != $this->connection;
		}

		/**
		* Crea la estructura de la aplicacion en la base de datos.
		*/
		function createDatabase() {
			if ($this->isConnected()) {
				$script = $GLOBALS['SMM']['app_path'] . '/../sql/ShareMyMind.sql';
				
				$queries = explode(';', file_get_contents($script));

				foreach ($queries as $query) {
					mysql_query($query);
				}
			}
		}

		/**
		* Funcion para limpiar los argumentos de la consulta.
		* Esto impide crear consultas inseguras.
		*/
		function clean($arguments) {
			for ($i = 0; $i < count($arguments); $i++) { 
				if (is_string($arguments[$i])) {
					$arguments[$i] = mysql_escape_string($arguments[$i]);
				}
			}

			return $arguments;
		}

		/**
		* Funcion para ejecutar una sentencia sql.
		*/
		function executeQuery($sql, $arguments) {
			$arguments = $this->clean($arguments);

			if ($GLOBALS['SMM']['debug']) {
				array_push($GLOBALS['query_log'], array('sql' => $sql, 'arguments' => $arguments));
			}

			foreach ($arguments as $argument) {
				if (NULL === $argument) {
					$sql = preg_replace('/\?/', 'NULL', $sql, 1);
				} else if (is_numeric($argument)) {
					$sql = preg_replace('/\?/', "$argument", $sql, 1);
				} else {
					$sql = preg_replace('/\?/', "'$argument'", $sql, 1);
				}
			}

			$results = mysql_query($sql, $this->connection);

			return $results;
		}

		/**
		* Funcion para guardar los resultados en un arreglo y liberar los recursos.
		*/
		function getArray($results) {
			$rows = array();

			while ($row = mysql_fetch_assoc($results)) {
				foreach ($row as $key => $value) {
					$rows[$key] = $value;
				}
			}

			mysql_free_result($results);

			return $rows;
		}

		/**
		* Funcion para guardar los resultados en un arreglo bidimensional y liberar los recursos.
		*/
		function getArrays($results) {
			$rows = array();

			while ($row = mysql_fetch_assoc($results)) {
				array_push($rows, $row);
			}

			mysql_free_result($results);

			return $rows;
		}

		/**
		* Ejecuta una consulta y devuelve un arreglo con la fila obtenida.
		*/
		function executeQueryAndGetArray($sql, $arguments) {
			$results = $this->executeQuery($sql, $arguments);

			if ($results) {
				return $this->getArray($results);
			}

			return NULL;
		}

		/**
		* Ejecuta una consulta y devuelve un arreglo bidimensional con las filas obtenidas.
		*/
		function executeQueryAndGetArrays($sql, $arguments) {
			$results = $this->executeQuery($sql, $arguments);

			if ($results) {
				return $this->getArrays($results);
			}

			return array();
		}

		/**
		* Retorna un arreglo con los usuarios,
		* delimitado por $take y $page.
		*/
		function getUsers($take = 10, $page = 1) {
			$sql = "SELECT * FROM users LIMIT ?,?";

			return $this->executeQueryAndGetArrays($sql, array($take * ($page - 1), $take));
		}

		/**
		* Retorna el usuario con el id = $user_id
		*/
		function getUserWithId($userId) {
			$sql = "SELECT * FROM users WHERE user_id = ?";

			return $this->executeQueryAndGetArray($sql, array($userId));
		}

		/**
		* Retorna el usuario con el user_name = $userName
		*/
		function getUserWithUserName($userName) {
			$sql = "SELECT * FROM users WHERE user_name = ?";

			return $this->executeQueryAndGetArray($sql, array($userName));;
		}

		/**
		* Retorna el usuario con el user_name = $userName
		*/
		function getUserWithEmail($email) {
			$sql = "SELECT * FROM users WHERE email = ?";

			return $this->executeQueryAndGetArray($sql, array($email));;
		}

		/**
		* Establece el token de sesion del usuario.
		*/
		function setUserToken($userId, $token) {
			$sql = "UPDATE users SET token = ? WHERE user_id = ?";

			return $this->executeQuery($sql, array($token, $userId));
		}

		/**
		* Obtiene un share.
		*/
		function getShare($shareId) {
			$sql = "SELECT * FROM shares WHERE share_id = ?";

			return $this->executeQueryAndGetArray($sql, array($shareId));
		}

		/**
		* Obtiene el top de shares mas votados.
		* Esta delimitado por $take.
		*/
		function getTopShares($take = 20) {
			$sql = "SELECT share_id FROM likes GROUP BY share_id ORDER BY COUNT(share_id) DESC LIMIT ?";

			$results = $this->executeQueryAndGetArrays($sql, array($take));

			if (0 === count($results)) {
				$sql = "SELECT * FROM shares ORDER BY created_at DESC LIMIT ?";

				$results = $this->executeQueryAndGetArrays($sql, array($take));
			} else {
				foreach ($results as &$share) {
					$share = $this->getShare($share['share_id']);
				}
			}

			return $results;
		}

		/**
		* Obtiene los shares del usuario paginados.
		*/
		function getUserShares($userId, $take = 10, $page = 1) {
			$sql = "SELECT * FROM shares WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?";

			return $this->executeQueryAndGetArrays($sql, array($userId, $take * ($page - 1), $take));
		}

		/**
		* Obtiene el numero de shares del usuario.
		*/
		function getShareCount($userId) {
			$sql = "SELECT COUNT(*) AS share_count FROM shares WHERE user_id = ?";

			return $this->executeQueryAndGetArray($sql, array($userId));
		}

		/**
		* Obtiene el numero de likes de un share.
		*/
		function getLikeCount($shareId) {
			$sql = "SELECT COUNT(share_id) AS likes FROM likes WHERE share_id = ? GROUP BY share_id";

			$result = $this->executeQueryAndGetArray($sql, array($shareId));

			return $result ? $result['likes'] : 0;
		}

		/**
		* Verifica si un usuario le dio like a un share.
		*/
		function isLikedBy($shareId, $userId) {
			$sql = "SELECT 1 AS liked FROM likes WHERE share_id = ? AND user_id = ?";

			$result = $this->executeQueryAndGetArray($sql, array($shareId, $userId));

			return $result ? TRUE : FALSE;
		}

		/**
		* Crea un like para el share de parte del usuario.
		*/
		function like($shareId, $userId) {
			$sql = "INSERT INTO likes (share_id, user_id) VALUES (?, ?)";

			return $this->executeQuery($sql, array($shareId, $userId));
		}

		/**
		* Elimina el like de un share de parte del usuario.
		*/
		function unlike($shareId, $userId) {
			$sql = "DELETE FROM likes WHERE share_id = ? AND user_id = ?";

			return $this->executeQuery($sql, array($shareId, $userId));
		}

		/**
		* Obtiene el avatar del usuario a partir del email.
		* Utiliza el servicio de gravatar.
		*/
		function getUserAvatar($userId) {
			$user = $this->getUserWithId($userId);

			if ($user) {
				return "http://www.gravatar.com/avatar/" . md5(strtolower(trim($user['email']))) . "?d=mm";
			}

			return "http://www.gravatar.com/avatar?d=mm";
		}

		/**
		* Crea un nuevo share.
		*/
		function share($userId, $text) {
			$sql = "INSERT INTO shares (user_id, text, created_at) VALUES (?, ?, ?)";
			
			return $this->executeQuery($sql, array($userId, htmlentities($text), date('Y-m-d H:i:s')));
		}

		/**
		* Actualiza la informacion del usuario.
		*/
		function updateUser($user) {
			$sql = "UPDATE users SET first_name = ?, last_name = ?, birthdate = ?, email = ?, password = ? WHERE user_id = ?";

			$arguments = array(
				$user['first_name'], 
				$user['last_name'],
				$user['birthdate'],
				$user['email'],
				$user['password'],
				$user['user_id'],
			);

			return $this->executeQuery($sql, $arguments);
		}

		/**
		* Registra un nuevo usuario.
		*/
		function registerUser($user) {
			$sql = "INSERT INTO users (first_name, last_name, birthdate, email, user_name, password) VALUES (?, ?, ?, ?, ?, ?)";

			$arguments = array(
				$user['first_name'], 
				$user['last_name'],
				$user['birthdate'],
				$user['email'],
				$user['user_name'],
				$user['password'],
			);

			return $this->executeQuery($sql, $arguments);
		}

		/**
		* Actualiza el color de los shares del usuario.
		*/
		function setShareColor($userId, $color = NULL) {
			$sql = "UPDATE users SET share_color = ? WHERE user_id = ?";

			return $this->executeQuery($sql, array($color, $userId));
		}
	}  
	
	/**
	* Objeto con las funciones necesarias para interactuar con la base de datos.
	*/
	$database = new Database;