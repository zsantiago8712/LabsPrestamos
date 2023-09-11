<?php
class consumo{
    var $id;
    var $consumo;
    var $detalles;
    var $link;
    var $lasterror;
    function __construct($link = null)
    {
        $this->link = $link;
    }
    function load()
    {
        $query = "SELECT id_consumo,descrip from inventarios_deii.item_tipo_consumo";
        $resultado = $this->link->query($query,Array());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_consumo($id)
    {
        $query = "SELECT descrip from inventarios_deii.item_tipo_consumo WHERE id_consumo = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
}
?>