<?php
class InventarioSerieSrv {
    public static $DB;
    public static $lasterror;
    public static $numrows;

    public static function inicializaInventarioSerie($DB)
    {
        self::$DB = $DB;
    }
    public static function getSeries($id_inventario,$id_lab) 
    {
        $qry = "select  id_serie_lote,
        concat(
            ifnull(concat('Serie: ', num_serie),''),
            ' ',
            ifnull(concat('Clave Interna: ', clave_interna),'')
            ) as serie_clave
            from    inventarios_deii.inventario_serie_lote
            where    id_inventario = ?
            and        id_laboratorio = ?
            and        id_estatus = 1
            and         disponible = 1 ";

        $resultado = self::$DB->query($qry,array($id_inventario,$id_lab));
        if(!$resultado)
        {
            self::$lasterror = self::$DB->lasterror;
            return false;
        }
        self::$numrows = $resultado->rowCount();
        return $resultado->fetchAllArrayAsoc();
    }
    public static function getSerieLote($id_serie)
    {
        $qry = "SELECT clave_interna from inventarios_deii.inventario_serie_lote WHERE id_serie_lote = ?";
        $resultado = self::$DB->query($qry,array($id_serie));
        if(!$resultado)
        {
            self::$lasterror = self::$DB->lasterror;
            return false;
        }
        self::$numrows = $resultado->rowCount();
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado[0]["clave_interna"];
    }
}
?>