<?php

include ("../includes/conectaBD.php");


class logInventario
{

	private static $db;
	protected static $instance = null;

	public static function initLogger($link) {
		self::$db = $link;
	}
	var $lasterror;
	public static function logChange(
		$usuario,
		$id_registro,
		$tabla,
		$accion
	) {
	
		$resultado = self::$db->query("call inventarios_deii.splog_" . $tabla . "(?,?,?)", array($id_registro, $usuario, $accion));
		if(!$resultado){
			
			echo  self::$db->getLastError();
			return false;
		}
	}
}





