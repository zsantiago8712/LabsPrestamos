<?
class laboratoros {
    var $id_lab;
    var $lab;
    var $id_usuario;
    var $link;
    var $lasterror;

    function __construct($link, $id=0)
    {
        $this->link = $link;
        if($id)
        {
            if(!$this->load($id))
            {
                $this->lab = null;
            }
        }
    }
    function load($id)
    {
        $query = "SELECT laboratorio
        FROM inventarios_deii.usuarios_laboratorios
        INNER JOIN inventarios_deii.laboratorios
        ON inventarios_deii.usuarios_laboratorios.id_laboratorio = inventarios_deii.laboratorios.id_laboratorio
        WHERE inventarios_deii.usuarios_laboratorios.id_usuario = ?";
        $resultado = $this->link->query($query,array($id));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        $this->lab = Array();
        $this->id_lab = Array();
        foreach($resultado as $key => $value)
        {
            array_push($this->lab,$value["laboratorio"]);
            $query = "SELECT id_laboratorio FROM inventarios_deii.laboratorios WHERE laboratorio = ?";
            $resultado = $this->link->query($query,array($value["laboratorio"]));
            if(!$resultado){
                $this->lasterror = $this->link->getLastError();
                return false;
            }
            $resultado = $resultado->fetchAllArrayAsoc();
            array_push($this->id_lab,$resultado[0]["id_laboratorio"]);
        }
        $this->id_usuario = $id;
        return true;
    }
    function get_id($lab)
    {
        $query = "SELECT id_laboratorio FROM inventarios_deii.laboratorios WHERE laboratorio = ?";
        $resultado = $this->link->query($query,array($lab));
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        $resultado = $resultado->fetchAllArrayAsoc();
        return $resultado[0]["id_laboratorio"];
    }
    function get_all_lab()
    {
        $query = "SELECT id_laboratorio, laboratorio FROM inventarios_deii.laboratorios";
        $resultado = $this->link->query($query);
        if(!$resultado){
            $this->lasterror = $this->link->getLastError();
            return false;
        }
        return $resultado->fetchAllArrayAsoc();
    }
    

}
?>