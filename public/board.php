<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';	
?>
<?php
	/**
	* Si se pasa el nombre del usuario por $_GET
	* se selecciona al usuario al que pertenece,
	* en caso contrario se selecciona el usuario autentificado.
	*/
	if (isset($_GET['user'])) {
		$user = $database->getUserWithUserName($_GET['user']);
	} else {
		$user = $auth->getCurrentUser();
	}

	/**
	* Si el usuario no existe redirecciona a la pagina principal.
	*/
	if (NULL == $user) {
		$redirect->to('index.php');
	}

	/**
	* Nombre de usuario.
	*/
	$userName = $user['user_name'];

	/**
	* Si el numero de pagina se pasa por $_GET
	* se utiliza para paginar los resultados,
	* en caso contrario la pagina es igual a 1.
	*/
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	/**
	* Si el numero de pagina no es un numero
	* se redirecciona a la pagina 1.
	*/
	if (!is_numeric($page)) {
		$redirect->to("board.php?user=$userName&page=1");
	}

	/**
	* Si el numero de pagina es menor o igual a 0
	* se redirecciona a la pagina 1.
	*/
	if (0 >= $page) {
		$redirect->to("board.php?user=$userName&page=1");
	}

	/**
	* Numero de resultados por pagina.
	*/
	$perPage = 10;

	/**
	* Numero de items a paginar.
	*/
	$shareCount = $database->getShareCount($user['user_id']);
	$shareCount = $shareCount['share_count'];

	/**
	* Total de paginas.
	*/
	$pages = ceil($shareCount / $perPage);

	/**
	* Si la pagina no existe y existen registros.
	* redirecciona a la ultima pagina.
	*/
	if (0 < $shareCount && $pages < $page) {
		$redirect->to("board.php?user=$userName&page=$pages");
	}

	/**
	* Si se envia el color por POST,
	* se actualiza el color del usuario.
	*/
	if (isset($_POST['share_color'])) {
		if ($database->setShareColor($arrays->value($auth->getCurrentUser(), 'user_id'), $_POST['share_color'])) {
			$user['share_color'] = $_POST['share_color'];
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
				<?php if ($auth->isLogged() && $user['user_id'] == $arrays->value($auth->getCurrentUser(), 'user_id')): ?>
					<!-- Formulario share color -->
					<form class="form-inline" action="board.php" method="POST">
						<div class="form-group">
							<label class="" for="share_color">Color</label>
							<input type="color" name="share_color" value="<?php echo isset($user['share_color']) ? $user['share_color'] : ''; ?>">
						</div>
						<input class="btn btn-xs btn-info" type="submit" value="Actualizar">	
					</form>
				<?php endif; ?>
				<!-- Shares -->
				<ul class="media-list">
					<?php foreach ($database->getUserShares($user['user_id'], $perPage, $page) as $share): ?>
					<li class="media" <?php echo isset($user['share_color']) ? 'style="background-color: ' . $user['share_color'] . ';"' : ''; ?>>
						<a class="pull-left" href="#">
							<img class="media-object" width="100px" height="100px" src="<?php echo $database->getUserAvatar($user['user_id']); ?>">
						</a>
						<div class="media-body">
							<h4 class="media-heading">
								<?php echo $user['user_name']; ?>
								<small>Fecha: <?php echo $share['created_at']; ?></small>
							</h4>
							<p><?php echo $share['text']; ?></p>
							<p>
								<?php if ($auth->isLogged()): ?>
									<?php if ($database->isLikedBy($share['share_id'], $arrays->value($auth->getCurrentUser(), 'user_id'))): ?>
										<a class="like glyphicon glyphicon-heart" data-user="<?php echo $arrays->value($auth->getCurrentUser(), 'user_id'); ?>" data-share="<?php echo $share['share_id']; ?>" href="#"></a>
									<?php else: ?>
										<a class="like glyphicon glyphicon-heart-empty" data-user="<?php echo $arrays->value($auth->getCurrentUser(), 'user_id'); ?>" data-share="<?php echo $share['share_id']; ?>" href="#"></a>
									<?php endif; ?>
								<?php endif; ?>
								<span class="badge"><?php echo $database->getLikeCount($share['share_id']); ?></span>
							</p>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
				<!-- Paginacion -->
				<ul class="pagination">
					<?php if (0 < $pages && $page != 1): ?>
						<li><a href="?user=<?php echo $userName; ?>&page=1">&laquo;</a></li>
					<?php endif; ?>
					<?php if (1 < $pages): ?>
						<?php for ($i = 1; $i < $pages + 1; $i++): ?>
							<?php if ($i == $page): ?>
								<li class="active">
									<a href="?user=<?php echo $userName; ?>&page=<?php echo $i; ?>">
										<?php echo $i; ?>
									</a>
								</li>
							<?php else: ?>
								<li>
									<a href="?user=<?php echo $userName; ?>&page=<?php echo $i; ?>">
										<?php echo $i; ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endfor; ?>
					<?php endif; ?>
					<?php if (0 < $pages && $page != $pages): ?>
						<li><a href="?user=<?php echo $userName; ?>&page=<?php echo $pages; ?>">&raquo;</a></li>
					<?php endif; ?>
				</ul>
				<!-- Formulario para agregar shares -->
				<?php if ($auth->isLogged()): ?>
					<?php if ($user['user_id'] == $arrays->value($auth->getCurrentUser(), 'user_id')): ?>
						<div class="row">
							<div class="col-xs-8 col-xs-offset-2">
								<form class="form-inline" role="form" action="share.php" method="POST">
									<div class="form-group">
										<textarea class="form-control" rows="3" placeholder="De 20 a 120 caracteres" name="text"></textarea>
									</div>
									<button type="submit" class="btn btn-primary">Enviar</button>
								</form>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
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