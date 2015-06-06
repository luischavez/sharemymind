<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';	

	/**
	* Si el Usuario no esta autentificado.
	* Verificamos sus credenciales,
	* si son validas se redirecciona a la pagina principal.
	*/
	if (!$auth->isLogged()) {
		if (isset($_POST['user_name']) && isset($_POST['password'])) {
			$remember = isset($_POST['remember']);

			if (!$auth->login($_POST['user_name'], $_POST['password'], $remember)) {
				$errors = TRUE;
			} else {
				$redirect->to('index.php');
			}
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
				<form class="form-horizontal" action="login.php" method="POST">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="panel-title">Login</h1>
						</div>
						<div class="panel-body">
							<?php if (isset($errors)): ?>
								<span class="help-block alert alert-danger">Usuario y/o contraseña invalidos.</span>
							<?php endif; ?>
							<!-- Usuario -->
							<div class="form-group">
								<label for="user_name" class="col-sm-2 control-label">Usuario</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="user_name">
								</div>
							</div>
							<!-- Contraseña -->
							<div class="form-group">
								<label for="password" class="col-sm-2 control-label">Contraseña</label>
								<div class="col-sm-10">
									<input type="password" class="form-control" name="password">
								</div>
							</div>
							<!-- Recordar usuario -->
							<div class="form-group">
								<div class="col-sm-8 col-sm-offset-2">
									<input type="checkbox" name="remember"> Recordar
								</div>
							</div>
							
						</div>
						<div class="panel-footer">
							<button type="submit" class="btn btn-primary">Ingresar</button>
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