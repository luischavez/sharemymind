<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';

	/**
	* Si no esta autentificado.
	*/
	if (!$auth->isLogged() && isset($_POST['register'])) {
		/**
		* Bandera para verificar si existen errores.
		*/
		$errors = FALSE;

		/**
		* Validaciones para el formulario.
		*/
		$validations = array(
			'user_name' => '/^[a-zA-Z]+$/', // Solo caracteres.
			'first_name' => '/^[a-zA-Zá-úÁ-Ú]+$/', // Solo caracteres.
			'last_name' => '/^[a-zA-Zá-úÁ-Ú]+$/', // Solo caracteres.
			'birthdate' => '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', // Solo Fechas.
			'email' => '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', // Correo valido.
		);

		$values = array();

		/**
		* Validamos el nombre del usuario.
		*/
		if (isset($_POST['first_name'])) {
			$firstName = $_POST['first_name'];

			if (preg_match($validations['first_name'], $firstName)) {
				if (20 < strlen($firstName)) {
					$errors = TRUE;
					$_SESSION['form_errors']['first_name'] = 'Maximo 20 caracteres.';
				} else {
					$values['first_name'] = $firstName;
				}
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['first_name'] = 'Solo caracteres [a-z], no puede estar vacia.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['first_name'] = 'Ingresa tu nombre.';
		}

		/**
		* Validamos el apellido del usuario.
		*/
		if (isset($_POST['last_name'])) {
			$lastName = $_POST['last_name'];

			if (preg_match($validations['last_name'], $lastName)) {
				if (20 < strlen($lastName)) {
					$errors = TRUE;
					$_SESSION['form_errors']['last_name'] = 'Maximo 20 caracteres.';
				} else {
					$values['last_name'] = $lastName;
				}
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['last_name'] = 'Solo caracteres [a-z], no puede estar vacia.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['last_name'] = 'Ingresa tu apellido.';
		}

		/**
		* Validamos la fecha de nacimiento.
		*/
		if (isset($_POST['birthdate'])) {
			$birthdate = $_POST['birthdate'];

			if (preg_match($validations['birthdate'], $birthdate)) {
				$values['birthdate'] = $birthdate;
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['birthdate'] = 'La fecha no es valida.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['birthdate'] = 'Ingresa tu fecha de nacimiento.';
		}

		/**
		* Validamos el correo.
		*/
		if (isset($_POST['email'])) {
			$email = $_POST['email'];

			if (preg_match($validations['email'], $email)) {
				if ($database->getUserWithEmail($email)) {
					$errors = TRUE;
					$_SESSION['form_errors']['email'] = 'El correo ya esta en uso.';
				} else {
					$values['email'] = $email;
				}
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['email'] = 'El correo no es valido.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['email'] = 'Ingresa tu correo.';
		}

		/**
		* Validamos el nombre del usuario.
		*/
		if (isset($_POST['user_name'])) {
			$userName = $_POST['user_name'];

			if (preg_match($validations['user_name'], $userName)) {
				if (10 < strlen($userName)) {
					$errors = TRUE;
					$_SESSION['form_errors']['user_name'] = 'Maximo 10 caracteres.';
				} else {
					if ($database->getUserWithUserName($userName)) {
						$errors = TRUE;
						$_SESSION['form_errors']['user_name'] = 'El nombre de usuario ya esta en uso.';
					} else {
						$values['user_name'] = $userName;
					}
				}
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['user_name'] = 'Solo caracteres [a-z], no puede estar vacia.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['user_name'] = 'Ingresa tu nombre de usuario.';
		}

		/**
		* Validamos la contraseña.
		*/
		if (isset($_POST['password']) && 0 != strlen($_POST['password'])) {
			if (isset($_POST['confirm_password'])) {
				$password = $_POST['password'];
				$confirmPassword = $_POST['confirm_password'];

				if (5 > strlen($password) || 20 < strlen($password)) {
					$errors = TRUE;
					$_SESSION['form_errors']['password'] = 'La contraseña tiene que tener entre 5 y 20 caracteres.';
				} else {
					if ($password == $confirmPassword) {
						$values['password'] = md5($password);
					} else {
						$errors = TRUE;
						$_SESSION['form_errors']['password'] = 'Las contraseñas no coinciden.';
					}
				}
			} else {
				$errors = TRUE;
				$_SESSION['form_errors']['confirm_password'] = 'Confirma la contraseña.';
			}
		} else {
			$errors = TRUE;
			$_SESSION['form_errors']['password'] = 'Ingresa tu contraseña.';
		}

		/**
		* Si no existen errores registramos al usuario y redireccionamos a la pagina de login.
		*/
		if (!$errors) {
			$database->registerUser($values);
			$redirect->to('login.php');
		} 
	}
?>
<!doctype html>
<!-- Establecemos el lenguaje de la aplicacion -->
<html lang="<?php echo $SMM['lang']; ?>">
<head>
	<!-- Establecemos la codificacion de caracteres -->
	<meta charset="<?php echo $SMM['charset']; ?>">
	<!-- No escalable -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<!-- Establecemos el titulo de la aplicacion -->
	<title><?php echo $SMM['title']; ?></title>
	<!-- Incluimos los estilos css -->
	<?php foreach ($SMM['styles'] as $style): ?>
		<link rel="stylesheet" href="<?php echo $style; ?>">
	<?php endforeach; ?>
</head>
<body>
	<!-- Contenedor principal -->
	<div class="container">
		<!-- Cabecera de la aplicacion -->
		<div class="row">
			<div class="col-xs-12">
				<?php include 'header.php'; ?>
			</div>
		</div>
		<!-- Cuerpo de la aplicacion -->
		<div class="row">
			<!-- Menu de navegacion -->
			<div class="col-xs-3">
				<?php include 'sidebar.php'; ?>
			</div>
			<!-- Contenido -->
			<div class="col-xs-9">
				<!-- Shares -->
				<form class="form-horizontal" action="register.php" method="POST">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h1 class="panel-title">Registro</h1>
						</div>
						<div class="panel-body">
							<!-- Indica que se envio el formulario de registro -->
							<input type="hidden" name="register">
							<!-- Nombre -->
							<div class="form-group">
								<label for="first_name" class="col-sm-2 control-label">Nombre</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="first_name" value="<?php echo !isset($_POST['first_name']) ? '' : $_POST['first_name']; ?>">
									<?php if (isset($_SESSION['form_errors']['first_name'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['first_name']; ?></span>
										<?php unset($_SESSION['form_errors']['first_name']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Apellido -->
							<div class="form-group">
								<label for="last_name" class="col-sm-2 control-label">Apellido</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="last_name" value="<?php echo !isset($_POST['last_name']) ? '' : $_POST['last_name']; ?>">
									<?php if (isset($_SESSION['form_errors']['last_name'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['last_name']; ?></span>
										<?php unset($_SESSION['form_errors']['last_name']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Fecha de nacimiento -->
							<div class="form-group">
								<label for="birthdate" class="col-sm-2 control-label">Fecha de nacimiento</label>
								<div class="col-sm-10">
									<input type="date" class="form-control" name="birthdate" value="<?php echo !isset($_POST['birthdate']) ? '' : $_POST['birthdate']; ?>">
									<?php if (isset($_SESSION['form_errors']['birthdate'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['birthdate']; ?></span>
										<?php unset($_SESSION['form_errors']['birthdate']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Correo -->
							<div class="form-group">
								<label for="email" class="col-sm-2 control-label">Correo</label>
								<div class="col-sm-10">
									<input type="email" class="form-control" name="email" value="<?php echo !isset($_POST['email']) ? '' : $_POST['email']; ?>">
									<?php if (isset($_SESSION['form_errors']['email'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['email']; ?></span>
										<?php unset($_SESSION['form_errors']['email']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Nombre de usuario -->
							<div class="form-group">
								<label for="user_name" class="col-sm-2 control-label">Nombre de usuario</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="user_name" value="<?php echo !isset($_POST['user_name']) ? '' : $_POST['user_name']; ?>">
									<?php if (isset($_SESSION['form_errors']['user_name'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['user_name']; ?></span>
										<?php unset($_SESSION['form_errors']['user_name']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Contraseña -->
							<div class="form-group">
								<label for="password" class="col-sm-2 control-label">Contraseña</label>
								<div class="col-sm-10">
									<input type="password" class="form-control" name="password">
									<?php if (isset($_SESSION['form_errors']['password'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['password']; ?></span>
										<?php unset($_SESSION['form_errors']['password']); ?>
									<?php endif; ?>
								</div>
							</div>
							<!-- Confirmar contraseña -->
							<div class="form-group">
								<label for="confirm_password" class="col-sm-2 control-label">Confirmar contraseña</label>
								<div class="col-sm-10">
									<input type="password" class="form-control" name="confirm_password">
									<?php if (isset($_SESSION['form_errors']['confirm_password'])): ?>
										<span class="help-block alert alert-danger"><?php echo $_SESSION['form_errors']['confirm_password']; ?></span>
										<?php unset($_SESSION['form_errors']['confirm_password']); ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<button type="submit" class="btn btn-primary">Registrar</button>
							<a type="button" class="btn btn-default" href="index.php">Cancelar</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<!-- Incluimos el codigo javascript -->
	<?php foreach ($SMM['scripts'] as $script): ?>
		<script src="<?php echo $script; ?>"></script>
	<?php endforeach; ?>

	<?php
		/**
		* Incluimos el archivo para finalizar la ejecucion de la aplicacion.
		* Es muy importante para manejar la aplicacion en modo de depuracion.
		*/
		require_once '../stop.php';	
	?>
</body>
</html>