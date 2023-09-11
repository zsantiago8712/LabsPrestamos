<?
class Caracteristica
{
    var $id;
    var $caracteristica;
    var $unidad;
    var $simbolo;
    var $tipo;
    var $lista = Array();
    var $opcion;
    var $link;
    var $numrows;
    var $lasterror;
    function __construct($_link, $id = 0) {
        $this->link = $_link;
        if($id != 0)
        {
            
            if(!$this->get_Caracteristica($id))
            {
                echo $this->link->lasterror;
            }
        } 
    }

    function get_Caracteristica($id)
    {
        $query = "SELECT a.id_caracteristica, 
                            a.caracteristica, 
                            a.unidad, 
                            a.simbolo, 
                            a.tipo, 
                            b.id_lista, 
                            b.valor 
        FROM inventarios_deii.inventario_caracteristica AS a 
        LEFT JOIN inventarios_deii.inventario_caracteristica_lista AS b 
        ON a.id_caracteristica = b.id_caracteristica 
        WHERE a.id_caracteristica = ?";
        $resultado = $this->link->query($query, array($id));
        if(!$resultado)
        {
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        if($resultado[0]["tipo"] == "2")
        {
            foreach($resultado as $key => $value)
            {
                array_push($this->lista, Array("id" => $value["id_lista"], "valor" => $value["valor"]));
            }
        }
        $this->id = $id;
        $this->caracteristica = $resultado[0]["caracteristica"];
        $this->unidad = $resultado[0]["unidad"];
        $this->simbolo = $resultado[0]["simbolo"];
        $this->tipo = $resultado[0]["tipo"];
        return true;

    
    }
    function get_caracteristicas()
    {
        $query = "SELECT id_caracteristica, caracteristica, unidad,simbolo,tipo from inventarios_deii.inventario_caracteristica";
        $resultado = $this->link->query($query);
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function load()
    {
        $query = "SELECT a.id_caracteristica, a.caracteristica, a.unidad, a.simbolo, a.tipo, b.id_lista, b.valor 
        FROM inventarios_deii.inventario_caracteristica AS a 
        LEFT JOIN inventarios_deii.inventario_caracteristica_lista AS b 
        ON a.id_caracteristica = b.id_caracteristica ORDER BY caracteristica ASC";
        $resultado = $this->link->query($query, array());
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
    return $resultado->fetchAllArrayAsoc();
    }
    function get_lista($id)
    {
        $query = "SELECT id_lista, valor from inventarios_deii.inventario_caracteristica_lista WHERE id_caracteristica = ?";
        $resultado = $this->link->query($query, array($id));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_prefijos($id = 0)
    {
        if($id != 0)
        {
            $query = "SELECT nombre from inventarios_deii.unidad_prefijos WHERE id_prefijo = ?";
            $resultado = $this->link->query($query,array($id));
            if(!$resultado){
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $resultado = $resultado[0]["nombre"];
            return $resultado;
        }
        $query = "SELECT id_prefijo,nombre,simbolo from inventarios_deii.unidad_prefijos";
        $resultado = $this->link->query($query);
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    function item_caracteristica($id)
    {
        $query = "Select id_inv_carac_item,id_caracteristica, id_prefijo, valor, id_lista from inventarios_deii.inventario_caracteristica_item WHERE id_inventario = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $misCaracteristicas = Array();
        foreach($resultado as $key => $value)
        {
            $prefijo = $this->get_prefijos($value["id_prefijo"]);
            $id_inv_caracteristica = $value["id_inv_carac_item"];
            if($value["id_prefijo"] == null){ $prefijo = "";}
            $caracteristica = $this->get_CaracteristicaEspecifica($value["id_caracteristica"],$value["id_lista"]);
            array_push($misCaracteristicas, Array("prefijo" => $prefijo, "caracteristicas" => $caracteristica, "valor" => $value["valor"], "id" => $id_inv_caracteristica));
        }
        if(count($misCaracteristicas ) == 0)
        {
            array_push($misCaracteristicas, Array("caracteristicas" => array("Sin caracteristicas"), "prefijo" => "","valor" => "", "id" => ""));
        }
        return $misCaracteristicas;
    }
    function get_CaracteristicaEspecifica($id, $id_lista)
    {
        $query = "SELECT caracteristica FROM inventarios_deii.inventario_caracteristica WHERE id_caracteristica = ?";
        $resultado = $this->link->query($query, array($id));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $Caracteristica = Array();
        $resultado = $resultado->fetchAllArrayAsoc();
        $resultado = $resultado[0]["caracteristica"];
        array_push($Caracteristica,$resultado);
        if($id_lista != null )
        {
            $query = "SELECT valor FROM inventarios_deii.inventario_caracteristica_lista WHERE id_lista = ?";
            $resultado = $this->link->query($query, array($id_lista));
            if(!$resultado){
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            $resultado = $resultado[0]["valor"];
            array_push($Caracteristica,$resultado);
        }
        return $Caracteristica;
        
    }
    function get_all_caracteristica($id_caracteristica){
        $query = "SELECT  id_caracteristica, caracteristica, unidad, simbolo, tipo 
                  FROM inventarios_deii.inventario_caracteristica WHERE id_caracteristica = ?";
        $resultado = $this->link->query($query, array($id_caracteristica));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $caractCheck=$resultado->fetchAllArrayAsoc();
        $caractCheck=$caractCheck[0];

        if($caractCheck["tipo"] == '2'){
            $query = "SELECT  id_lista, valor
                      FROM inventarios_deii.inventario_caracteristica_lista WHERE id_caracteristica = ?";
           $resultado_lista = $this->link->query($query, array($id_caracteristica));
           if(!$resultado_lista){
                $this->lasterror = $this->link->lasterror;
                return false;
            }
            
            array_push($caractCheck,$resultado_lista->fetchAllArrayAsoc());
        }
        return $caractCheck;
    }
    function agregar_caracteristica($caracteristica, $unidad, $simbolo, $tipo)
    {
        $query = "INSERT INTO inventarios_deii.inventario_caracteristica (id_caracteristica, caracteristica, unidad, simbolo, tipo)
        VALUES (null,?,?,?,?)";
        $resultado = $this->link->insert($query, array($caracteristica, $unidad, $simbolo, $tipo));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        $this->id = $this->link->lastid;

        return true;
    }
    function agregar_opciones($opcion){

        $query = "INSERT INTO inventarios_deii.inventario_caracteristica_lista (id_lista, id_caracteristica, valor)
        VALUES (null,?,?)";
        $resultado = $this->link->query($query, array($this->id, $opcion));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        return true;
 
    }
    function actualizar_opciones($id_car, $opciones){
        
    foreach($opciones as $opcion){
        if($opcion["id"] == 0){
            $this->id = $id_car;
            $this->agregar_opciones($opcion["valor"]);
        }
        $query = "UPDATE inventarios_deii.inventario_caracteristica_lista SET id_caracteristica = ?, valor = ? WHERE id_lista = ?";
        $resultado = $this->link->query($query, array($id_car, $opcion["valor"], $opcion["id"]));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
    }
    }
    
    function actualizar_caracteristica($id, $car, $unidad, $abr, $tipo, $opciones){

        $query = "UPDATE inventarios_deii.inventario_caracteristica SET caracteristica = ?, unidad = ?, simbolo = ?, tipo = ? WHERE id_caracteristica = ?";
        $resultado = $this->link->query($query, array($car, $unidad, $abr, $tipo, $id));
        if(!$resultado){
            $this->lasterror = $this->link->lasterror;
            return false;
        }
        if($opciones != 0){
            $this->actualizar_opciones($id, $opciones);
        }
        
        return true;
 
    }

    
}