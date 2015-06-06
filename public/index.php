<?php
	/**
	* Incluimos el archivo de arranque de la aplicacion.
	* Es muy importante por que contiene las funciones
	* necesarias para que trabaje correctamente la aplicacion.
	*/
	require_once '../boot.php';	
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
				<ul class="media-list">
					<?php foreach ($database->getTopShares() as $share): ?>
					<?php $user = $database->getUserWithId($share['user_id']); ?>
					<li class="media" <?php echo isset($user['share_color']) ? 'style="background-color: ' . $user['share_color'] . ';"' : ''; ?>>
						<a class="pull-left" href="#">
							<img class="media-object" width="100px" height="100px" src="<?php echo $database->getUserAvatar($user['user_id']); ?>">
						</a>
						<div class="media-body">
							<h4 class="media-heading">
								<a href="board.php?user=<?php echo $user['user_name']; ?>">
									<?php echo $user['user_name']; ?>
								</a>
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