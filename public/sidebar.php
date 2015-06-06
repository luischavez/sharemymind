<ul class="nav nav-pills nav-stacked">
	<li><a href="index.php">Inicio</a></li>
	<?php if ($auth->isLogged()): ?>
		<li><a href="profile.php">Mi Perfil</a></li>
		<li><a href="board.php">Mi Pizarra</a></li>
	<?php endif; ?>
</ul>