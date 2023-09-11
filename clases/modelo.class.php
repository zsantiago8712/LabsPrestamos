<?php
class modelo{
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
        $query = "SELECT id_modelo,modelo from inventarios_deii.modelo";
        $resultado = $this->link->query($query,Array());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_modelo($id)
    {
        $query = "SELECT id_modelo, modelo from inventarios_deii.modelo WHERE id_modelo = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc(); 
    }
    function get_Modelo_Marca($id)
    {
        $query = "SELECT id_modelo,modelo from inventarios_deii.modelo WHERE id_marca = ? OR modelo = 'SIN MODELO'";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function save($modelo, $marca)
    {
        $query = "INSERT into inventarios_deii.modelo VALUES(?,null,?)";
        $resultado = $this->link->insert($query,array($marca,$modelo));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $this->id = $this->link->lastid;
        return true;
    }
    function checkString($modelo, $marca)
    {
        $query = "SELECT * from inventarios_deii.modelo WHERE modelo = ? AND id_marca = ?";
        $resultado = $this->link->query($query,array($modelo,$marca));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        if($this->link->numrows)
        {
            $resultado = $resultado->fetchAllArrayAsoc();
            $resultado = $resultado[0]["id_modelo"];
            $this->id = $resultado;
            return true;
        }else{
            return false;
        }
    }
}
?>