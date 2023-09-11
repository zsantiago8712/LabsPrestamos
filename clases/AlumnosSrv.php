<?php
// []   <>
class alumno {
    var $cuenta;
    var $tipo_usuario;
    var $id_usuario;
    var $nombre;
    var $ap_paterno;
    var $ap_materno;
   
    var $DB;
    var $lasterror;
    var $numrows = 0;
    var $lock = 0;
   

    function __construct($link, $id){

        $this->DB 			= $link;
        $this->id_usuario 		= $id;
    
        $this->load();
    
    
     }
     private function load(){
        $query = "SELECT cuenta, id_tipo_usuario, ap_paterno, ap_materno, nombres FROM inventarios_deii.usuarios_prestamos WHERE id_usuario = ?";
        $resultado = $this->DB->query($query,array($this->id_usuario));
        if(!$resultado)
        {
            $this->lasterror = $this->DB->lasterror;
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $resultado = $resultado[0];
        $this->cuenta = $resultado["cuenta"];
        $this->tipo_usuario = $resultado["id_tipo_usuario"];
        $this->ap_materno = $resultado["ap_materno"];
        $this->ap_paterno = $resultado["ap_paterno"];
        $this->nombre = $resultado["nombres"];
        return true;
     }
}
?>