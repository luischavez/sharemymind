<?php
	
	/**
	* Esta clase es necesaria ya que en algunas versiones de php
	* no se puede acceder a un arreglo de la siguiente manera: $object->function()['key']
	*/
	class Arrays {

		/**
		* Regresa el valor $key contenido en el arreglo $array
		*/
		public function value($array, $key) {
			return $array[$key];
		}
	}

	/**
	* Objecto con las funciones para manejar arreglos.
	*/
	$arrays = new Arrays;