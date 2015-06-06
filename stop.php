<?php

	/**
	* Si esta en modo debug mostramos la informacion de depuracion.
	*/
	if ($SMM['debug']) {
		echo '<div class="container">';
		echo '<div class="row col-xs-6 col-xs-offset-3">';
		echo '<div class="panel panel-info">';
		echo '<div class="panel-heading">';
		echo '<h3 class="panel-title">DEBUG</h3>';
		echo '</div>';
		echo '<div class="panel-body">';
		foreach ($GLOBALS['query_log'] as $log) {
			echo "<div class=\"alert alert-info\"><p>SQL: <strong>{$log['sql']}</strong></p><p>VARS: <strong>{" . implode(', ', $log['arguments']) . '}</strong></p></div>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	* Cerramos la conexion con la base de datos.
	*/
	$database->close();