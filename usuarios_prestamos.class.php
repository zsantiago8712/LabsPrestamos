<?php



class UsuarioPrestamo {

    public $id_usuario = 0;
    public $cuenta = null;
    public $id_tipo_usuario = null;
    public $nombres = null;
    public $ap_paterno = null;
    public $ap_materno = null;
    public $correo = null;
    public $id_programa = null;
    public $programa = null;

    public $db;

    public $lastError;

    public $numRows;


    public function __construct($link, $cuenta="", $tipo_usuario="") {
        
        $this->db = $link;

        if (!empty($cuenta))  {
            echo "load";
            $this->cuenta = $cuenta;
            $this->id_tipo_usuario = $tipo_usuario;
            $this->load();
        }
        
    }


    public function save() {

        if($this->id_usuario == 0) {
            $this->insert();
            
        }else {
            $this->update();
        }
    }


    private function update() {
        echo "update";
        $query = "UPDATE inventarios_deii.usuarios_prestamos 
                  SET cuenta = ?,
                    id_tipo_usuario = ?,
                    nombres = ?,
                    ap_paterno = ?,
                    ap_materno = ?,
                    correo = ?,
                    id_programa = ?,
                    programa = ?
                  WHERE id_usuario = ?";

        $temp_values = array_values(get_object_vars($this));
        $values = array_slice($temp_values, 0, count($temp_values) - 3);
        array_shift($values);
        array_push($values, $this->id_usuario);

        var_dump($values);
        $result = $this->db->update($query, $values);
                
        if(!$result || !$result->rowCount()) {
             $this->lastError = $this->db->getLastError();
             echo "Error: " . $this->lastError;
            return false;
        }
        echo "Si updateo";
        return true;
    }

    private function insert()
    {
        echo "  Insert";
        $query = "INSERT INTO inventarios_deii.usuarios_prestamos
                 (
                    cuenta,
                    id_tipo_usuario, -- 1 alumno #, 2 profesor ac# , 3 intercambio i
                    nombres,
                    ap_paterno,
                    ap_materno,
                    correo,
                    id_programa,
                    programa
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )";
        $temp_values = array_values(get_object_vars($this));
        $values = array_slice($temp_values, 0, count($temp_values) - 3);
        array_shift($values);
        var_dump($values);
        $result = $this->db->insert($query, $values);
                    
        if(!$result || !$result->rowCount()) {
            $this->lastError = $this->db->getLastError();
            echo "Error: " . $this->lastError;
            return false;
       }
       echo "Si inserto";
        return true;

    }

    private function load() {

        echo "LOAD";
        $query = "SELECT
                    id_usuario,
                    cuenta,
                    id_tipo_usuario, -- 1 alumno #, 2 profesor ac# , 3 intercambio i
                    nombres,
                    ap_paterno,
                    ap_materno,
                    id_programa,
                    programa
                FROM inventarios_deii.usuarios_prestamos
                WHERE cuenta = ?
                AND id_tipo_usuario = ?;";
                    
        $result = $this->db->query($query,
                                        array(
                                        $this->cuenta,
                                        $this->id_tipo_usuario
                                    ));

        if(!$result || !$result->rowCount()) {
             $this->lastError = $this->db->getLastError();
             return false;
        }

        $this->numRows = $result->rowCount();
        $user = $result->fetchArrayAsoc();
        
        foreach ($user as $key => $value) {
                $this->$key = $value;
        }

        var_dump($this);
    }

}



if( isset($_GET['test']) ) {
    echo "Yes";
    include_once ("includes/conectaBD.php");


    $user = new UsuarioPrestamo($link, "217924", "1");
    $user->cuenta = "217924";
    $user->ap_materno = "Solis";
    $user->ap_paterno ="Zamora";
    $user->id_tipo_usuario = "1";
    $user->nombres = "Daniel";
    $user->correo = "Zs1@icloud.com";
    $user->id_programa = "1";
    $user->programa = "loll";

    // $user->ap_materno = "";
    

    $user->save();

}