<?php
include ("cuantificacion.class.php");
include ("consumo.class.php");
include ("jsTree.class.php"); // clasificacion
include ("marca.class.php");
include ("modelo.class.php");
include("caracteristicas.class.php");
include("laboratorio.class.php");
include("LogInventario.class.php");
include "QueryHelper.php";
class item {
    var $consumo;
    var $marca;
    var $modelo;
    var $cuantificacion;
    var $clasificacion;
    var $caracteristicas;
    var $serie;
    var $link;
    var $lasterror;
    function __construct($link = null)
    {
        $this->link = $link;
        logInventario::initLogger($link);
    }
    function save($General, $Caracteristicas, $Laboratorios, $id="0", $borrarCara,$generalLab,$usuario)
    {
        if($id != "0")
        {
           
           return $this->edit($General, $Caracteristicas, $Laboratorios, $id, $borrarCara, $generalLab,$usuario);
        }else {
            
            return $this->insert($General, $Caracteristicas, $Laboratorios,$generalLab, $usuario);
        }
    }
    function edit($General, $Caracteristicas, $Laboratorios, $id, $borrarCara,$generalLab,$usuario)
    {
        $this->link->start_transaction();
        $query = "UPDATE inventarios_deii.inventario_item SET id_marca = ?, id_modelo=?, id_cuantificacion = ?, id_consumo = ?, id_clasificacion = ?, descripcion = ?, num_parte = ?, id_prefijo = ?, id_unidad = ? WHERE id_inventario = ? ";
        $resultado = $this->link->insert($query, array($General["marca"],$General["modelo"],$General["cuantificacion"],$General["consumo"],$General["clasificacion"],$General["descripcion"],$General["numParte"], $General["prefijo"], $General["unidad"] ,$id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }

        if(logInventario::logChange($usuario, $id, "inventario_item", "UPDATE")) {
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }



        if($borrarCara)
        { 
            foreach($borrarCara as $key => $value)
            {
                $query = "DELETE FROM inventarios_deii.inventario_caracteristica_item WHERE id_inv_carac_item = ?";
                $resultado = $this->link->query($query,array($value));
                if(!$resultado){
                    $this->lasterror = $this->link->getLastError();
                    $this->link->rollback();
                    return false;
                }
            }
        }

        foreach($Caracteristicas as $key => $value)
        { 
            if($value["id"] == "0")
            {
                if($value["tipo"] == "1")
                {
                    $query = "INSERT INTO inventarios_deii.inventario_caracteristica_item VALUES(null,?,?,?,?,null)";
                    $resultado = $this->link->insert($query, array($value["caracteristica"],$id,$value["prefijo"],$value["valor"])); 

                }else {
                    $query = "SELECT valor FROM inventarios_deii.inventario_caracteristica_lista WHERE id_lista = ?";
                    $resultado =  $this->link->query($query,array($value["valor"]));
                    if(!$resultado){
                        $this->lasterror = $this->link->getLastError();
                        $this->link->rollback();
                        return false;
                    }
                    $resultado = $resultado->fetchAllArrayAsoc();
                    $mivalor = $resultado[0]["valor"];
                    $query = "INSERT INTO inventarios_deii.inventario_caracteristica_item VALUES(null,?,?,null,?,?)";
                    $resultado = $this->link->insert($query, array($value["caracteristica"],$id,$mivalor,$value["valor"]));
                }
            }else{
                if($value["tipo"] == "1")
                {
                    $query = "UPDATE inventarios_deii.inventario_caracteristica_item set id_prefijo = ?, valor = ? WHERE id_inv_carac_item = ? ";
                    $resultado = $this->link->query($query, array($value["prefijo"],$value["valor"], $value["id"])); 

                }else {
                    $query = "SELECT valor FROM inventarios_deii.inventario_caracteristica_lista WHERE id_lista = ?";
                    $resultado =  $this->link->query($query,array($value["valor"]));
                    if(!$resultado){
                        $this->lasterror = $this->link->getLastError();
                        $this->link->rollback();
                        return false;
                    }
                    $resultado = $resultado->fetchAllArrayAsoc();
                    $mivalor = $resultado[0]["valor"];
                    $query = "UPDATE inventarios_deii.inventario_caracteristica_item set valor = ?, id_lista = ? WHERE id_inv_carac_item = ? ";
                    $resultado = $this->link->query($query, array($mivalor,$value["valor"],$value["id"]));
                }
            }
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                $this->link->rollback();
                return false;
            }
        }

        $misLaboratorios = new laboratoros($this->link);
        foreach($Laboratorios as $key => $value)
        {
                $lab = $misLaboratorios->get_id($key);
                $cantidad = 0;
                $idLab = $generalLab[$key]["id"];
                foreach($value as $llave => $valor)
                {
                    if(isset($generalLab[$key]))
                    {
                    $prestamo = $generalLab[$key]["prestamos"];
                    $dias = $generalLab[$key]["dias"];
                    $renovar = $generalLab[$key]["renovar"];
                    $visibilidad = $generalLab[$key]["visibilidad"];
                }else {
                    $prestamo = null;
                    $dias = null;
                    $renovar = null;
                    $visibilidad = 0;
                }
                    if(isset($valor["cantidad"])&&!isset($valor["Lote"]))
                    {
                        $total = $valor["cantidad"];
                        if($total == null)
                        {
                            continue;
                        }
                    }else {
                        if(isset($valor["serie"]))
                        {
                            if($valor["id"] == "0")
                            {
                                $disponible = 1;
                                if($valor["estado"]!="1")
                                {
                                    $disponible = 0;
                                }
                                $query = "INSERT INTO inventarios_deii.inventario_serie_lote VALUES(null,?,?,null,null,?,?,null,?,?,?,?)";
                                $resultado = $this->link->insert($query,array($id,$valor["serie"],$valor["uia"],$lab,$disponible,1,$valor["estado"],$valor["qr"]));
                                if(!$resultado){
                                    $this->lasterror = $this->link->getLastError();
                                    $this->link->rollback();
                                    return false;
                                }
                                logInventario::logChange($usuario, $this->link->lastid, "inventario_serie_lote", "INSERT");

                            }else {
                                $query = "Update inventarios_deii.inventario_serie_lote set num_serie = ?, clave_interna = ?, id_estatus = ?, qr  = ? WHERE id_serie_lote = ?";
                                $resultado = $this->link->query($query,array($valor["serie"],$valor["uia"],$valor["estado"],$valor["qr"],$valor["id"]));
                                if(!$resultado){
                                    $this->lasterror = $this->link->getLastError();
                                    $this->link->rollback();
                                    return false;
                                }
                                logInventario::logChange($usuario, $valor["id"], "inventario_serie_lote", "UPDATE");
                            }

                        }else {
                            if($valor["id"] == "0")
                            {
                                $query = "INSERT INTO inventarios_deii.inventario_serie_lote VALUES(null,?,null,?,?,null,?,null,?,?,1,?)";
                                $resultado = $this->link->insert($query,array($id,$valor["Lote"],$valor["fecha"],$lab,$valor["cantidad"],$valor["cantidad"],$valor["qr"]));
                                if(!$resultado){
                                    $this->lasterror = $this->link->getLastError();
                                    $this->lasterror = $this->lasterror . " " . $valor["id"];
                                    $this->link->rollback();
                                    return false;
                                }
                                logInventario::logChange($usuario,$this->link->lastid,"inventario_serie_lote","INSERT");
                                
                            }else {
                                $query = "Update inventarios_deii.inventario_serie_lote set num_lote = ?, fecha_caducidad = ?, disponible = ?, existencia = ?, qr = ? WHERE id_serie_lote = ? ";
                                $resultado = $this->link->insert($query,array($valor["Lote"],$valor["fecha"],$valor["cantidad"],$valor["cantidad"],$valor["qr"],$valor["id"]));
                                if(!$resultado){
                                    $this->lasterror = $this->link->getLastError();
                                    $this->lasterror = $this->lasterror . " " . $valor["id"];
                                    $this->link->rollback();
                                    return false;
                                }
                                logInventario::logChange($usuario,$valor["id"],"inventario_serie_lote", "UPDATE");
                            }
                            
                            

                        }
                        $query = "SELECT SUM(disponible) as total
                            FROM inventarios_deii.inventario_serie_lote 
                            WHERE id_laboratorio = ? AND id_inventario = ?";
                        $resultado = $this->link->query($query,array($lab,$id));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        $resultado = $resultado->fetchAllArrayAsoc();
                        $total = intval($resultado[0]["total"]);
                       
                    }
                    if($idLab == 0)
                    {
                        $query = "INSERT into inventarios_deii.inventario_existencia_laboratorio VALUES(null,?,?,null,?,?,?,?,?,?)";
                        $resultado = $this->link->insert($query,array($id,$lab,$total,$total,$prestamo,$dias,$renovar,$visibilidad));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        logInventario::logChange($usuario,$this->link->lastid,"inventario_existencia_laboratorio", "INSERT");
                        $idLab = 1;

                    }else{ 
                     $query = "UPDATE inventarios_deii.inventario_existencia_laboratorio set  existencia = ?,prestamos = ?,dias = ?,renovar = ?, visibilidad_web=? WHERE id_inventario = ? AND id_laboratorio = ?";
                        $resultado = $this->link->insert($query,array($total,$prestamo,$dias,$renovar,$visibilidad,$id,$lab));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        $query = "SELECT id_existencia_lab FROM inventarios_deii.inventario_existencia_laboratorio WHERE id_inventario = ? AND id_laboratorio = ?";
                        $resultado = $this->link->insert($query,array($id,$lab));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        $resultado = $resultado->fetchAllArrayAsoc();
                        logInventario::logChange($usuario,$resultado[0]["id_existencia_lab"],"inventario_existencia_laboratorio", "UPDATE");
                }}
                
            }
        $this->link->commit();
        return true;
    }
    function insert($General, $Caracteristicas, $Laboratorios,$generalLab,$usuario)
    {
        $this->link->start_transaction();
        //Empezamos por las generales. 
        $query = "INSERT INTO inventarios_deii.inventario_item VALUES(null,?,?,?,?,?,?,?,?,?)";
        $resultado = $this->link->insert($query, array($General["marca"],$General["modelo"],$General["cuantificacion"],$General["consumo"],$General["clasificacion"],$General["descripcion"],$General["numParte"], $General["prefijo"], $General["unidad"]));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        $id = $this->link->lastid;
        logInventario::logChange($usuario,$id,"inventario_item", "INSERT");
        foreach($Caracteristicas as $key => $value)
        { 
            if($value["tipo"] == "1")
            {
                $query = "INSERT INTO inventarios_deii.inventario_caracteristica_item VALUES(null,?,?,?,?,null)";
                $resultado = $this->link->insert($query, array($value["caracteristica"],$id,$value["prefijo"],$value["valor"])); 

            }else {
                $query = "SELECT valor FROM inventarios_deii.inventario_caracteristica_lista WHERE id_lista = ?";
                $resultado =  $this->link->query($query,array($value["valor"]));
                if(!$resultado){
                    $this->lasterror = $this->link->getLastError();
                    $this->link->rollback();
                    return false;
                }
                $resultado = $resultado->fetchAllArrayAsoc();
                $mivalor = $resultado[0]["valor"];
                $query = "INSERT INTO inventarios_deii.inventario_caracteristica_item VALUES(null,?,?,null,?,?)";
                $resultado = $this->link->insert($query, array($value["caracteristica"],$id,$mivalor,$value["valor"]));
            }
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                $this->link->rollback();
                return false;
            }
        }
        $misLaboratorios = new laboratoros($this->link);
            foreach($Laboratorios as $key => $value)
            {
                $lab = $misLaboratorios->get_id($key);
                $cantidad = 0;
                if(isset($generalLab[$key]))
                {
                    $prestamo = $generalLab[$key]["prestamos"];
                    $dias = $generalLab[$key]["dias"];
                    $renovar = $generalLab[$key]["renovar"];
                    $visibilidad = $generalLab[$key]["visibilidad"];
                }else {
                    $prestamo = null;
                    $dias = null;
                    $renovar = null;
                    $visibilidad = 0;
                }
                
                foreach($value as $llave => $valor)
                {
                    
                    if(isset($valor["cantidad"])&&!isset($valor["Lote"]))
                    {
                        if($valor["cantidad"] == null)
                        {
                            continue;
                        }
                        $query = "INSERT INTO inventarios_deii.inventario_existencia_laboratorio VALUES(null,?,?,null,?,?,?,?,?,?)";
                        $resultado = $this->link->insert($query,array($id,$lab,$valor["cantidad"],$valor["cantidad"],$prestamo,$dias,$renovar,$visibilidad));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        logInventario::logChange($usuario,$this->link->lastid,"inventario_existencia_laboratorio", "INSERT");
                    }else {
                        if(isset($valor["serie"]))
                        {
                            $disponible = 1;
                            if($valor["estado"]!="1")
                            {
                                $disponible = 0;
                            }
                            $query = "INSERT INTO inventarios_deii.inventario_serie_lote VALUES(null,?,?,null,null,?,?,null,?,?,?,?)";
                            $resultado = $this->link->insert($query,array($id,$valor["serie"],$valor["uia"],$lab,$disponible,1,$valor["estado"],$valor["qr"]));
                            if(!$resultado){
                                $this->lasterror = $this->link->getLastError();
                                $this->link->rollback();
                                return false;
                            }
                            logInventario::logChange($usuario,$this->link->lastid,"inventario_serie_lote", "INSERT");
                            $cantidad = $cantidad +1;
                        }else {
                            $query = "INSERT INTO inventarios_deii.inventario_serie_lote VALUES(null,?,null,?,?,null,?,null,?,?,?,?)";
                            $resultado = $this->link->insert($query,array($id,$valor["Lote"],$valor["fecha"],$lab,$valor["cantidad"],$valor["cantidad"],1,$valor["qr"]));
                            if(!$resultado){
                                $this->lasterror = $this->link->getLastError();
                                $this->link->rollback();
                                return false;
                            }
                            logInventario::logChange($usuario,$this->link->lastid,"inventario_serie_lote", "INSERT");
                            $cantidad = $cantidad + intval($valor["cantidad"]);

                        }
                        

                    }
                }
                if($cantidad > 0)
                {
                    $query = "INSERT INTO inventarios_deii.inventario_existencia_laboratorio VALUES(null,?,?,null,?,?,?,?,?,?)";
                        $resultado = $this->link->insert($query,array($id,$lab,$cantidad,$cantidad,$prestamo,$dias,$renovar,$visibilidad));
                        if(!$resultado){
                            $this->lasterror = $this->link->getLastError();
                            $this->link->rollback();
                            return false;
                        }
                        logInventario::logChange($usuario,$this->link->lastid,"inventario_existencia_laboratorio", "INSERT");
                }
            }

        $this->link->commit();
        return true;

    }
    function load($id = 0)
    {
        if($id == 0)
        {
            $query = "SELECT * from inventarios_deii.inventario_item";
            $resultado = $this->link->query($query);
        }else{
            $query = "SELECT * from inventarios_deii.inventario_item WHERE id_inventario = ?";
            $resultado = $this->link->query($query, array($id));
        }
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            $this->link->rollback();
            return false;
        }
        $General = $this->get_datosGenerales($resultado->fetchAllArrayAsoc());
        return $General;
    }
    function get_datosGenerales($general)
    {
        $Cuantificacion = new cuantificacion($this->link);
        $Consumo = new consumo($this->link);
        $Clasificacion = new jsTree($this->link);
        $Marca = new marca($this->link);
        $Modelo = new Modelo($this->link);
        $General = Array();
        $Caracteristicas = new Caracteristica($this->link);
        foreach($general as $key => $value)
        {
            $cuantificacion = $Cuantificacion->get_Cuantificacion($value["id_cuantificacion"]);
            $consumo = $Consumo->get_consumo($value["id_consumo"]);
            $clasificacion = $Clasificacion->get_clasificacion($value["id_clasificacion"]);
            $marca = $Marca->get_marca($value["id_marca"]);
            $modelo = $Modelo->get_modelo($value["id_modelo"]);
            $descripcion = $value["descripcion"];
            $caracteristicas = $Caracteristicas->item_caracteristica($value["id_inventario"]);
            $unidad = $value["id_unidad"];
            array_push($General,Array("cuantificacion"=>$cuantificacion,"consumo" =>$consumo,"clasificacion" =>$clasificacion,"id_clasificacion"=>$value["id_clasificacion"], "marca" => $marca,"modelo" =>$modelo,"desc"=>$descripcion,"caracteristicas"=>$caracteristicas,"num_parte" => $value["num_parte"], "id" => $value["id_inventario"], "id_unidad" => $unidad, "id_prefijo" => $value["id_prefijo"]));
        }
        return $General;
    }
    function get_unidad($id=0)
    {
        if($id == 0)
        {
            $query = "SELECT id_unidad, unidad from inventarios_deii.inventario_unidad";
            $resultado = $this->link->query($query);
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            return $resultado;
        }else{ 
        $query = "SELECT id_unidad, unidad from inventarios_deii.inventario_unidad WHERE id_unidad = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado;
    }
    }

    function get_Existencias($id)
    {
        $query = "SELECT id_cuantificacion from inventarios_deii.inventario_item WHERE id_inventario = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $cuantifiacion = $resultado[0]["id_cuantificacion"];
        if($cuantifiacion== "2" || $cuantifiacion == "3")
        {
            $query = "SELECT id_serie_lote, 
            CASE 
              WHEN num_serie IS NOT NULL THEN num_serie 
              ELSE CONCAT(num_lote, ' ', fecha_caducidad) 
            END AS num_o_lote_y_caducidad, 
            clave_interna, 
            id_laboratorio, 
            disponible, 
            existencia, 
            id_estatus,
            qr
          FROM 
            inventarios_deii.inventario_serie_lote 
          WHERE 
            id_inventario = ?";
            $resultado = $this->link->insert($query,array($id));
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
        }else{
            $query = "SELECT id_laboratorio,existencia FROM inventarios_deii.inventario_existencia_laboratorio WHERE id_inventario = ?";
            $resultado = $this->link->insert($query,array($id));
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
        }
        return $resultado;
    }
    function getGenerales_Lab($id)
    {
        $query = "SELECT id_existencia_lab,id_laboratorio,existencia,prestamos,dias,renovar, visibilidad_web FROM inventarios_deii.inventario_existencia_laboratorio WHERE id_inventario = ?";
        $resultado = $this->link->insert($query,array($id));
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                return false;
            }
        return $resultado->fetchAllArrayAsoc();
    }
    function get_estatus($id = 0)
    {
        if($id == 0)
        {
            $query = "SELECT id_estatus,estatus FROM inventarios_deii.inventario_estatus";
            $resultado = $this->link->insert($query);
        }else{
            $query = "SELECT estatus FROM inventarios_deii.inventario_estatus WHERE id_estatus = ?";
            $resultado = $this->link->insert($query,array($id));
        }
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado;
    }
    function busqueda($term,$lab)
    {
        $Qh = new QueryHelper();
        $term = explode(" ", $term);
        $query = "SELECT i.id_inventario,
            i.id_marca,
            i.id_modelo,
            i.id_cuantificacion,
            i.id_consumo,
           i.id_clasificacion,
            i.descripcion,
            i.num_parte,
           i.id_prefijo,
            i.id_unidad,
            concat(i.descripcion, ' ',
              IFNULL(
                group_concat(
                    case c.tipo
                        when 1 
                            then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
                        else
                            concat(c.caracteristica, ' : ' , il.valor)
                    end 
                ORDER BY c.caracteristica SEPARATOR ' '),
                '')
            ) as descrip_carac
            
    FROM	inventarios_deii.inventario_item i

            LEFT JOIN inventarios_deii.inventario_existencia_laboratorio e on
            i.id_inventario = e.id_inventario
            
            LEFT JOIN inventarios_deii.laboratorios l on
            l.id_laboratorio = e.id_laboratorio
            
            LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on
            ci.id_inventario = i.id_inventario
            
            LEFT JOIN inventarios_deii.inventario_caracteristica c on
            c.id_caracteristica = ci.id_caracteristica
            
            LEFT JOIN inventarios_deii.unidad_prefijos u on
            u.id_prefijo = ci.id_prefijo
            
            LEFT JOIN inventarios_deii.inventario_caracteristica_lista il on
            il.id_lista 			= ci.id_lista
        and	il.id_caracteristica 	= ci.id_caracteristica  
    WHERE cond_1
    GROUP BY	i.id_inventario,
                i.descripcion,
                e.id_laboratorio,
                e.disponible,
                l.laboratorio
    HAVING cond_2
";
$Qh->setQuery($query);
$Qh->registerConditions(array("cond_1", "cond_2"));
$Qh->IN("cond_1","e.id_laboratorio",$lab);
$Qh->LIKE_AND("cond_2","descrip_carac",$term);
    $resultado = $this->link->query($Qh->getQuery(),$Qh->getParams());
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $General = $this->get_datosGenerales($resultado->fetchAllArrayAsoc());
        return $General;

    }
    function laboratorioBusqueda($lab)
    {
        $dato = "{in}" . $lab;
        $query = "SELECT inventarios_deii.inventario_item.id_inventario,
        inventarios_deii.inventario_item.id_marca,
        inventarios_deii.inventario_item.id_modelo,
        inventarios_deii.inventario_item.id_cuantificacion,
        inventarios_deii.inventario_item.id_consumo,
        inventarios_deii.inventario_item.id_clasificacion,
        inventarios_deii.inventario_item.descripcion,
        inventarios_deii.inventario_item.num_parte,
        inventarios_deii.inventario_item.id_prefijo,
        inventarios_deii.inventario_item.id_unidad
            FROM inventarios_deii.inventario_item 
            INNER JOIN inventarios_deii.inventario_existencia_laboratorio 
            on inventarios_deii.inventario_item.id_inventario = inventarios_deii.inventario_existencia_laboratorio.id_inventario 
            WHERE inventarios_deii.inventario_existencia_laboratorio.id_laboratorio in ({in}) ";
        $resultado = $this->link->query($query,array($dato));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return $this->lasterror;
        }
        $General = $this->get_datosGenerales($resultado->fetchAllArrayAsoc());
        return $General;
    }
    
}
