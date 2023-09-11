<?
class PrestamosSrv{
	public static $DB;
	public static $numrows;
	public static $lasterror;
	public static function carritoPorEntregar($link,$usuario, $tipo_usuario, $id_laboratorio){
		self::$DB = $link;

		$qry = "update	inventarios_deii.inventario_prestamos as ip

						inner join inventarios_deii.usuarios_prestamos as up on
						ip.id_usuario = up.id_usuario

						inner join inventarios_deii.inventario_existencia_laboratorio ex on
						ex.id_existencia_lab = ip.id_existencia_lab

				set 	ip.id_status_prestamo = 100

				where 	ip.id_status_prestamo = 1	
					and	up.cuenta = ? 
					and up.id_tipo_usuario = ?
					and ex.id_laboratorio  = ?";
		

		$chk = self::$DB->query($qry,array($usuario,$tipo_usuario,$id_laboratorio));

		self::$numrows = self::$DB->numrows;
		
		self::$lasterror = self::$DB->lasterror;
		
		return $chk;

	}
	public static function checkAdeudos($link,$usuario, $id_laboratorio)
	{
		$qry="SELECT a.id_status_prestamo, b.id_laboratorio FROM inventarios_deii.inventario_prestamos a
						inner join inventarios_deii.inventario_existencia_laboratorio b on
						b.id_existencia_lab = a.id_existencia_lab
						Inner join inventarios_deii.usuarios_prestamos as c on
						a.id_usuario = c.id_usuario
						WHERE a.id_status_prestamo = 6 AND c.cuenta = ? AND b.id_laboratorio = ?";
		$chk = self::$DB->query($qry,array($usuario,$id_laboratorio));

		self::$numrows = self::$DB->affectedrows;
		
		self::$lasterror = self::$DB->lasterror;
		
		return $chk->fetchArrayAsoc();
	}
}
?>