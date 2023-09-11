<?php

class UsuarioInventarioSrv{
 
 var $cuenta;
 var $tipo_usuario;
 var $id_usuario;
 var $bloqueado = 0;
 var $totItems = array();

 var $DB;
 var $lasterror;
 var $numrows = 0;
 var $lock = 0;

 function __construct($link, $cuenta, $tipo){

    $this->DB 			= $link;
    $this->cuenta 		= $cuenta;
    $this->tipo_usuario = $tipo;

    $this->load();
    $this->checkBloqueo();


 }

 private function checkBloqueo(){

 	$qry = "SELECT	count(*) as bloqueo
        
			 FROM	inventarios_deii.inventario_prestamos pres

			 INNER JOIN inventarios_deii.usuarios_prestamos us ON
        				pres.id_usuario	=	us.id_usuario
        
			 WHERE	us.cuenta			= ?
				AND	us.id_tipo_usuario	= ?
    			AND (	pres.id_status_prestamo = 4
					OR	( 	pres.id_status_prestamo = 6
						and pres.adeudo_saldado = 0
					)
				)
				AND cast(fecha_entrega_programada as date) < now()";

	$res = $this->DB->query($qry, array($this->cuenta,
										$this->tipo_usuario	));

	if(!$res){
		$this->lasterror = $this->DB->lasterror;
		return;
	}
	
	    $this->bloqueado = $res->fetchAllArrayAsoc()[0]["bloqueo"];
	    return;	
}


  private function load(){
     $qry = "SELECT	pres.id_usuario,
     				pres.id_existencia_lab,
					sum(pres.cantidad_entregada) as total_prestados
        
			 FROM	inventarios_deii.inventario_prestamos pres

			 INNER JOIN inventarios_deii.usuarios_prestamos us ON
        				pres.id_usuario	=	us.id_usuario
        
			 WHERE	us.cuenta			= ?
				AND	us.id_tipo_usuario	= ?
    			AND (	pres.id_status_prestamo in (2,4)
					OR	( 	pres.id_status_prestamo = 6
						and pres.adeudo_saldado = 0
					)
				)
			 GROUP BY	pres.id_usuario,
			 			pres.id_existencia_lab;";

	$res = $this->DB->query($qry, array($this->cuenta,
										$this->tipo_usuario	));

	if(!$res){
		$this->lasterror = $this->DB->lasterror;
		return;
	}
	$this->numrows = $res->rowCount();
	if($this->numrows > 0){
	    $this->totItems = $res->fetchAllArrayAsoc();
	    return;	
	}
	
 }

 function getCantidadPrestados($id_existencia_lab){

 	if(!count($this->totItems)){
 		return 0;
 	}

 	$arr = array_filter($this->totItems, 
 						function($item) use($id_existencia_lab){
		   					return $item["id_existencia_lab"] == $id_existencia_lab ;
  						});
 	if(count($arr) && $arr != null){

 		return $arr[array_keys($arr)[0]]["total_prestados"];
 	}
 	return 0;

 }
 

}

if(isset($_GET["test"])){
	//include("../security/security_template.php");
	include("../includes/conectaBD.php");
	$srvus =  new UsuarioInventarioSrv($link,213701,2);
	echo $srvus->getCantidadPrestados(14);
	//include("../includes/conectaBD.php");
}
