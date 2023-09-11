<?php

class InventarioPrestamo
{
    public $id_inventario_prestamo = 0;
    public $id_usuario;
    public $id_existencia_lab;
    public $id_serie_lote;
    public $cantidad_solicitada;
    public $cantidad_entregada;
    public $fecha_solicitud;
    public $fecha_recepcion;
    public $fecha_entrega_programada;
    public $fecha_entrega_real;
    public $id_status_prestamo;
    public $renovaciones;
    public $adeudo_saldado;
    public $db;

    public $lastError;

    public $numRows;

    public function __construct($link, $id = "")
    {
        $this->db = $link;
        if(!empty($id)) {
            $this->load();
        }
    }

    public function save()
    {
        if($this->id_inventario_prestamo == 0) {
            $this->insert();
        }else {
            $this->update();
        }
    }


    private function load()
    {
        echo "cargo";
        $query = "SELECT
            id_inventario_prestamo,
            id_usuario,
            id_existencia_lab,
            id_serie_lote,
            cantidad_solicitada,
            cantidad_entregada,
            fecha_solicitud,
            fecha_recepcion,
            fecha_entrega_programada,
            fecha_entrega_real,
            id_status_prestamo,
            renovaciones,
            adeudo_saldado
        FROM
            inventario_prestamos;
        WHERE id_inventario_prestamo = ?";


        $result = $this->db->query($query, array($this->id_inventario_prestamo));

        if(!$result || !$result->rowCount()) {
            $this->lastError = $this->db->getLastError();
            echo "Error: " . $this->lastError;
            return false;
        }

        $this->numRows = $result->rowCount();
        $user = $result->fetchArrayAsoc();

        foreach ($user as $key => $value) {
            $this->$key = $value;
        }

        var_dump($this);       
    }

    public function insert()
    {
        echo "Inseert";
        $query = "INSERT INTO inventario_prestamos (
            id_usuario,
            id_existencia_lab,
            id_serie_lote,
            cantidad_solicitada,
            cantidad_entregada,
            fecha_recepcion,
            fecha_entrega_programada,
            fecha_entrega_real,
            id_status_prestamo,
            renovaciones,
            adeudo_saldado
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        
        $temp_values = array_values(get_object_vars($this));
        $values = array_slice($temp_values, 0, count($temp_values) - 3);
        unset($values[5]);
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


    public function update()
    {
        echo "update";
        $query = 'UPDATE inventario_prestamos
        SET
            id_usuario = ?,
            id_existencia_lab = ?,
            id_serie_lote = ?,
            cantidad_solicitada = ?,
            cantidad_entregada = ?,
            fecha_solicitud = ?,
            fecha_recepcion = ?,
            fecha_entrega_programada = ?,
            fecha_entrega_real = ?,
            id_status_prestamo = ?,
            renovaciones = ?,
            adeudo_saldado = ?
        WHERE id_inventario_prestamo = ?';

        $temp_values = array_values(get_object_vars($this));
        $values = array_slice($temp_values, 0, count($temp_values) - 3);
        array_shift($values);
        array_push($values, $this->id_inventario_prestamo);
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
}



if (isset($_GET['test'])) {
    echo "Yes";
    include_once("includes/conectaBD.php");

    $inventario = new InventarioPrestamo($link, "1");
    $inventario->id_usuario = "9";

    $inventario->adeudo_saldado = 0;

    //var_dump($inventario);
    $inventario->save();
}