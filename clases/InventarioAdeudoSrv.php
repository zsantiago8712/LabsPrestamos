<?php
class InventarioAdeudoSrv {
    public static $DB;
    public static $lasterror;
    public static $numrows;

    public static function inicializaInventarioAdeudo($DB)
    {
        self::$DB = $DB;
    }
    public static function quitarAdeudo($link,$id_prestamo)
    {
        self::$DB = $link;
        $qry = "UPDATE inventarios_deii.inventario_prestamos set id_status_prestamo = 7 WHERE id_inventario_prestamo = ? ";

        $resultado = self::$DB->query($qry,array($id_prestamo));
        if(!$resultado)
        {
            self::$lasterror = self::$DB->lasterror;
            return false;
        }
        self::$numrows = $resultado->rowCount();
        return true;
    }
}
?>