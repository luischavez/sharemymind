<div class="media page-header">
	<h1><?php echo $SMM['title']; ?> <small><?php echo $SMM['slogan']; ?></small></h1>
	<?php if ($auth->isLogged()): ?>
		<div class="pull-left">
			<img class="media-object" width="100px" height="100px" src="<?php echo $database->getUserAvatar($arrays->value($auth->getCurrentUser(), 'user_id')); ?>">
		</div>
		<div class="media-body">
			<h2 class="media-heading">
				<?php echo $arrays->value($auth->getCurrentUser(), 'user_name'); ?>
			</h2>
			<a class="btn btn-warning btn-xs" href="logout.php">Salir</a>
	</div>
	<?php else: ?>
		<p class="pull-right">
			<a class="btn btn-primary btn-sm" href="login.php">Ingresar</a>
			<a class="btn btn-default btn-sm" href="register.php">Registrarse</a>
		</p>
	<?php endif; ?>
</div>