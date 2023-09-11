<?php
/*
* Creado por: Derek Escamilla.
* fecha: 3/27/2023
* Ubicación: Universidad Iberoamericana
*
*
*/
class jsTree {
    var $tree = Array();
    var $nodo;
    var $id = Array();
    var $parent;
    var $old_parent;
    var $text;

    var $link;
    var $numrows;
    var $lasterror;
    function __construct($_link) {
        $this->link = $_link;

    }
    function save ($nuevos = [], $bd = []) 
    {
        $this->link->start_transaction();
        if(count($nuevos))
        {
            foreach($nuevos as $new => $value)
            {
                $query = "INSERT INTO inventarios_deii.clasificacion VALUES (null,?)";
                $resultado = $this->link->insert($query,array($value[0]["text"]));
                if(!$resultado){
                    $this->lasterror = $this->link->getLastError();
                    $this->link->rollback();
                    return false;
                }
                $id = array("js" => $value[0]["id"], "id" => $this->link->lastid);
                array_push($this->id, $id);
            }
            $nuevo = $this->generate_id($nuevos);
            foreach($nuevo as $new => $value)
            {
                $query = "insert into inventarios_deii.clasificacion_estructura(id_clasificacion, id_clasificacion_parent) VALUES(?,?);";
                $resultado = $this->link->insert($query,array($value["id"], 
                                                        $value["parent"]));
                if(!$resultado){
                    $this->lasterror = $this->link->getLastError();
                    $this->link->rollback();
                    return false;
                }
            }
            
        }
        if(count($bd))
        {
            foreach($bd as $key => $value)
            {
                if($value[0]["borrar"] == "1")
                {
                    if($this->checkInventario($value[0]["id"]))
                    {
                        $this->lasterror = "Está carpeta posee elementos asociados.";
                        return false;
                    }
                    $query = "DELETE from inventarios_deii.clasificacion_estructura WHERE id_clasificacion = ?";
                    $resultado =  $this->link->delete($query, array($value[0]["id"]));
                    if(!$resultado){
                        $this->lasterror = $this->link->getLastError();
                        $this->link->rollback();
                        return false;
                    }
                    $query = "DELETE from inventarios_deii.clasificacion WHERE id_clasificacion = ?";
                    $resultado =  $this->link->delete($query, array($value[0]["id"]));
                    if(!$resultado){
                        $this->lasterror = $this->link->getLastError();
                        $this->link->rollback();
                        return false;
                    }

                }else{
                    if($value[0]["old_parent"] != "0")
                    {
                        $query = "UPDATE inventarios_deii.clasificacion_estructura set id_clasificacion_parent = ? where id_clasificacion = ?" ;
                        $resultado =  $this->link->update($query, array($value[0]["parent"],$value[0]["id"]));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                    }
                    if($value[0]["old"] != "0")
                    {
                        $query = "UPDATE inventarios_deii.clasificacion set clasificacion = ? WHERE id_clasificacion = ?"; 
                        $resultado =  $this->link->update($query, array($value[0]["text"],$value[0]["id"]));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                    }
                }
                
            }
            
        }
        $this->link->commit();
        return true;
        
    }
    function create_node ($id, $parent, $text)
    {
        $this->id = $id;
        $this->parent = $parent;
        $this->text = $text; 
        $this->nodo = Array($this->id, $this->$parent, $this->$text);
        array_push($this->tree, $this->nodo);
    }
    function load()
    {
        $query = "SELECT a.id_clasificacion,a.clasificacion, b.id_clasificacion_parent from inventarios_deii.clasificacion as a INNER JOIN inventarios_deii.clasificacion_estructura as b on a.id_clasificacion = b.id_clasificacion";
        $resultado = $this->link->query($query);
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        foreach($resultado->fetchAllArrayAsoc() as $key => $value)
        {
            if($value["id_clasificacion_parent"] == null || $value["id_clasificacion_parent"] == "0")
            {
                $value["id_clasificacion_parent"] = "#";
            }
            $this->nodo = Array("id" => $value["id_clasificacion"], "parent" => $value["id_clasificacion_parent"],"text" => $value["clasificacion"]);
            array_push($this->tree, $this->nodo);

        }
        return $this->tree;
    }
    function load_parents()
    {
        $query = "SELECT id_clasificacion,clasificacion FROM  inventarios_deii.clasificacion where id_clasificacion != 1";
        $resultado = $this->link->query($query,Array());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function generate_id($nuevos)
    {
        $registros = Array();
        foreach($nuevos as $new => $value)
        {
            foreach($this->id as $key => $id)
            {
                if($id["js"] == $value[0]["parent"])
                {
                    $value[0]["parent"] = $id["id"];
                }
                if($id["js"] == $value[0]["id"])
                {
                    array_push($registros, Array("id" =>  $id["id"], "text" => $value[0]["text"], "parent" =>$value[0]["parent"] ));
                }
            }
        }
        return $registros;
    }
    function get_clasificacion($id)
    {
        $query = "SELECT * FROM  inventarios_deii.clasificacion where id_clasificacion = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $resultado = $resultado[0]["clasificacion"];
        $query = "SELECT id_clasificacion_parent FROM  inventarios_deii.clasificacion_estructura where id_clasificacion = ?";
        $result = $this->link->query($query,array($id));
        if(!$result){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        $result = $result->fetchAllArrayAsoc();
        $result = $result[0]["id_clasificacion_parent"];
        $query = "SELECT clasificacion FROM  inventarios_deii.clasificacion where id_clasificacion = ?";
        $result = $this->link->query($query,array($result));
        if(!$result){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        $result = $result->fetchAllArrayAsoc();
        $result = $result[0]["clasificacion"];
        $total = Array("clasificacion" => $resultado, "parent" => $result);
        return $total;
    }
    function checkInventario($id)
    {
        $query = "SELECT id_clasificacion FROM inventarios_deii.inventario_item where id_clasificacion = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        if($this->link->numrows)
        {
            return true;
        }else{
            return false;
        }
    }
}

?>