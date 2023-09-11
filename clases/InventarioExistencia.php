<?php

 
class InventarioExistencia {

 var $id_existencia_lab;
 var $id_inventario;
 var $id_laboratorio;
 var $id_espacio;
 var $existencia;
 var $disponible;
 var $prestamos;
 var $dias;
 var $renovar;
 var $visibilidad_web;

 var $DB;
 var $lasterror;
 var $numrows = 0;
 var $lock = 0;		

 function __construct($link, $id_ex_lab = 0, $lock = 0){
     
     $this->DB 		= $link;
     $this->lock	= $lock;

     if($id_ex_lab > 0){
     	 $this->id_existencia_lab = $id_ex_lab;
         $this->load();
     }

 }

 function load(){

 	$forupdate = "";
 	if($this->lock == 1){
 		$forupdate = "FOR UPDATE";
 	}
 	$qry = "SELECT	id_existencia_lab,
 					id_inventario,
 					id_laboratorio,
 					id_espacio,
 					existencia,
 					disponible,
 					prestamos,
 					dias,
 					renovar,
 					visibilidad_web
 			FROM	inventarios_deii.inventario_existencia_laboratorio
 			WHERE	id_existencia_lab = ?
 					$forupdate";

 	$res = $this->DB->query($qry, array($this->id_existencia_lab));

 	if(!$res || !$res->rowCount()){
 		$this->lasterror = $this->DB->lasterror;
 		$this->numrows = 0;
 		return;
 	}

 	$this->numrows = $res->rowCount();
 	$inv = $res->fetchArrayAsoc();
 	$this->id_existencia_lab	=	$inv['id_existencia_lab'];
 	$this->id_inventario		=	$inv['id_inventario'];
 	$this->id_laboratorio		=	$inv['id_laboratorio'];
 	$this->id_espacio			=	$inv['id_espacio'];
 	$this->existencia			=	$inv['existencia'];
 	$this->disponible			=	$inv['disponible'];
 	$this->prestamos			=	$inv['prestamos'];
 	$this->dias					=	$inv['dias'];
 	$this->renovar				=	$inv['renovar'];
 	$this->visibilidad_web		=	$inv['visibilidad_web'];

 }

 private function insert(){

 	$qry = "insert into inventarios_deii.inventario_existencia_laboratorio
 			(id_inventario,
 			 id_laboratorio,
 			 id_espacio,
 			 existencia,
 			 disponible,
 			 prestamos,
 			 dias,
 			 renovar,
 			 visibilidad_web) 
 			 values(?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?,
 			 		?);";
 	$chk = $this->DB->insert($qry, array( $this->id_inventario,
 										 $this->id_laboratorio,
 										 $this->id_espacio,
 										 $this->existencia,
 										 $this->disponible,
 										 $this->prestamos,
 										 $this->dias,
 										 $this->renovar,
 										 $this->visibilidad_web));
 	if(!$chk){
 		$this->lasterror = $this->DB->lasterror;
 		return false;
 	}
 	$this->id_existencia_lab = $this->DB->lastid;
 	return true;
 }

 private function update(){

 	$qry = "update inventarios_deii.inventario_existencia_laboratorio
 			set id_espacio		= ?,
 			 	existencia		= ?,
 			 	disponible		= ?,
 			 	prestamos		= ?,
 			 	dias			= ?,
 			 	renovar			= ?,
 			 	visibilidad_web	= ?
 			 where id_existencia_lab = ?";

 	$chk = $this->DB->update($qry,array( $this->id_espacio,
 										 $this->existencia,
 										 $this->disponible,
 										 $this->prestamos,
 										 $this->dias,
 										 $this->renovar,
 										 $this->visibilidad_web,
 										 $this->id_existencia_lab));
 	if(!$chk){
 		$this->lasterror = $this->DB->lasterror;
 		return false;
 	}
 	return true;
 }

 public function save(){

 	if(empty($this->id_existencia_lab) || $this->id_existencia_lab == 0) {
 	    return $this->insert();
 	}
 	else{
 	    return $this->update();
 	}
 }

}