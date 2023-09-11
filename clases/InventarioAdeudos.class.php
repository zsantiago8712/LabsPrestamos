<?php
class adeudos {
    private $id = 0;
    var $id_usuario;
    var $id_inventario_prestamo;
    var $comentarios;
    var $activo;
    var $fecha_adeudo;
    var $id_tipo_adeudo;
    var $link;
    var $lasterror;

    function __construct($link,$id=0)
    {
        $this->link = $link;
        if($id)
        {
            $this->load($id);
        }

    }
    private function load($id)
    {
        $query = "SELECT id_adeudo, id_usuario,id_inventario_prestamo,comentarios,activo,fecha_adeudo,id_tipo_adeudo FROM inventarios_deii.inventario_adeudos WHERE id_adeudo = ?";
        $resultado = $this->link->query($query, array($id));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
        $resultado = $resultado->fetchAllArrayAsoc();
        $this->id = $resultado[0]["id_adeudo"];
        $this->id_usuario = $resultado[0]["id_usuario"];
        $this->id_inventario_prestamo = $resultado[0]["id_inventario_prestamo"];
        $this->comentarios = $resultado[0]["comentarios"];
        $this->fecha_adeudo = $resultado[0]["fecha_adeudo"];
        $this->activo = $resultado[0]["activo"];
        $this->id_tipo_adeudo = $resultado[0]["id_tipo_adeudo"];
    }

    function save()
    {
        if($this->id == 0)
        {
           return  $this->agregar();
        }else{
           return  $this->editar();
        }
    }
    private function agregar()
    {
    
        $query = "INSERT INTO inventarios_deii.inventario_adeudos VALUES(null,?,?,?,?,null,?)";
        $resultado = $this->link->query($query, array($this->id_usuario,$this->id_inventario_prestamo,$this->comentarios,1,$this->id_tipo_adeudo));
            if(!$resultado)
            {
                $this->lasterror = $this->link->lasterror;
                return false;
            }
        $query = "UPDATE inventarios_deii.inventario_prestamos set id_status_prestamo = ? WHERE id_inventario_prestamo = ? ";
        $resultado = $this->link->query($query, array(6,$this->id_inventario_prestamo));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
    private function editar()
    {
        $query = "UPDATE inventarios_deii.inventario_adeudos set comentarios = ?, activo = ? WHERE id_adeudo = ?";
        $resultado = $this->link->query($query, array($this->comentarios,$this->activo, $this->id));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
    }
}
?>