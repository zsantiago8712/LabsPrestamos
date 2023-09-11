<?php
class marca {
    var $id;
    var $marca;
    var $link;
    var $lasterror;
    function __construct($link = null)
    {
        $this->link = $link;
    }
    function load()
    {
        $query = "SELECT id_marca,marca from inventarios_deii.marca";
        $resultado = $this->link->query($query,Array());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_marca($id)
    {
        $query = "SELECT marca from inventarios_deii.marca WHERE id_marca = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_id($marca)
    {
        $query = "SELECT id_marca from inventarios_deii.marca WHERE marca = ?";
        $resultado = $this->link->query($query,array($marca));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function save($marca)
    {
        $query = "INSERT into inventarios_deii.marca VALUES(null,?)";
        $resultado = $this->link->insert($query,array($marca));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
         $this->id = $this->link->lastid;
        return true;
    }
    function checkString($marca)
    {
        $query = "SELECT * from inventarios_deii.marca WHERE marca = ?";
        $resultado = $this->link->query($query,array($marca));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        if($this->link->numrows)
        {
            $resultado = $resultado->fetchAllArrayAsoc();
            $resultado = $resultado[0]["id_marca"];
            $this->id = $resultado;
            return true;
        }else{
            return false;
        }
    }
}
?>