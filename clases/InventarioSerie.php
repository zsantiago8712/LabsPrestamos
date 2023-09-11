<?php
class InventarioSerie {

 var $id_serie_lote;
 var $id_inventario;
 var $id_laboratorio;
 var $num_serie;
 var $clave_interna;
 var $disponible;
 var $existencia;
 var $id_estatus;
var $id_existencia_lab;
 var $DB;
 var $lasterror;
 var $numrows = 0;
 var $lock = 0;		

 function __construct($link, $id_ex_lab = 0, $lock = 0){
     
     $this->DB 		= $link;
     $this->lock	= $lock;

     if($id_ex_lab > 0){
     	 $this->id_serie_lote = $id_ex_lab;
         $this->load();
     }

 }

 function load(){

 	$forupdate = "";
 	if($this->lock == 1){
 		$forupdate = "FOR UPDATE";
 	}
 	$qry = "SELECT	id_serie_lote,
 					id_inventario,
 					id_laboratorio,
 					num_serie,
 					existencia,
 					disponible,
 					clave_interna,
 					id_estatus
 			FROM	inventarios_deii.inventario_serie_lote
 			WHERE	id_serie_lote = ?
 					$forupdate";

 	$res = $this->DB->query($qry, array($this->id_serie_lote));

 	if(!$res || !$res->rowCount()){
 		$this->lasterror = $this->DB->lasterror;
 		$this->numrows = 0;
 		return;
 	}

 	$this->numrows = $res->rowCount();
 	$inv = $res->fetchArrayAsoc();
 	$this->id_serie_lote	=	$inv['id_serie_lote'];
 	$this->id_inventario		=	$inv['id_inventario'];
 	$this->id_laboratorio		=	$inv['id_laboratorio'];
 	$this->clave_interna			=	$inv['clave_interna'];
 	$this->existencia			=	$inv['existencia'];
 	$this->disponible			=	$inv['disponible'];
 	$this->id_estatus			=	$inv['id_estatus'];

    $qry="SELECT id_existencia_lab FROM inventarios_deii.inventario_existencia_laboratorio
    WHERE id_inventario = ? AND id_laboratorio = ?";
    $res = $this->DB->query($qry, array($this->id_inventario,$this->id_laboratorio));

    if(!$res || !$res->rowCount()){
        $this->lasterror = $this->DB->lasterror;
        $this->numrows = 0;
        return;
    }

    $this->numrows = $res->rowCount();
    $inv = $res->fetchArrayAsoc();
 	$this->id_existencia_lab = $inv['id_existencia_lab'];

 }

 private function insert(){

 	$qry = "insert into inventarios_deii.inventario_serie_lote
 			(id_inventario,
 			 id_laboratorio,
 			 clave_interna,
 			 existencia,
 			 disponible,
 			 id_estatus,
 			 dias,
 			 renovar,
 			 visibilidad_web) 
 			 values(?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?);";
 	$chk = $this->DB->insert($qry, array( $this->id_inventario,
 										 $this->id_laboratorio,
 										 $this->clave_interna,
 										 $this->existencia,
 										 $this->disponible,
 										 $this->id_estatus));
 	if(!chk){
 		$this->lasterror = $this->DB->lasterror;
 		return false;
 	}
 	$this->id_serie_lote = $this->DB->lastid;
 	return true;
 }

 private function update(){

 	$qry = "update inventarios_deii.inventario_serie_lote
 			set clave_interna		= ?,
 			 	existencia		= ?,
 			 	disponible		= ?,
 			 	id_estatus		= ?
 			 where id_serie_lote = ?";

 	$chk = $this->DB->update($qry,array( $this->clave_interna,
 										 $this->existencia,
 										 $this->disponible,
 										 $this->id_estatus,
 										 $this->id_serie_lote));
 	if(!$chk){
 		$this->lasterror = $this->DB->lasterror;
 		return false;
 	}
 	return true;
 }

 public function save(){

 	if(empty($this->id_serie_lote) || $this->id_serie_lote == 0) {
 	    return $this->insert();
 	}
 	else{
 	    return $this->update();
 	}
 }

}
?>