<?
class cuantificacion {
    var $id;
    var $cuantificacion;
    var $link;
    var $lasterror;
    function __construct($link = "null")
    {
        $this->link = $link;
    }
    function load()
    {
        $query = "SELECT id_cuantificacion,descrip from inventarios_deii.item_cuantificacion";
        $resultado = $this->link->query($query,Array());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_Cuantificacion($id)
    {
        $query = "SELECT descrip from inventarios_deii.item_cuantificacion WHERE id_cuantificacion = ? ";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
   
}
?>