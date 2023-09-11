<?php

class InventarioPrestamo
{
    public $id_inventario_prestamo;
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
    public $numRows = 0;

    public function __construct($link, $id_inv_prestamo = 0){

        $this->db   = $link;

        if ($id_inv_prestamo > 0){
            $this->id_inventario_prestamo = $id_inv_prestamo;
            $this->load();
        }
    }

    private function load(){
        $query = "SELECT id_inventario_prestamo,
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
                   FROM inventarios_deii.inventario_prestamos
                   WHERE id_inventario_prestamo = ?";

        $result = $this->db->query($query, array($this->id_inventario_prestamo));

        if (!$result || !$result->rowCount()) {
            $this->lastError = $this->db->getLastError();
            $this->numRows = 0;
            return;
        }

        $this->numRows = $result->rowCount();
        $prest = $result->fetchArrayAsoc();
        $this->id_inventario_prestamo   = $prest['id_inventario_prestamo'];
        $this->id_usuario               = $prest['id_usuario'];
        $this->id_existencia_lab        = $prest['id_existencia_lab'];
        $this->id_serie_lote            = $prest['id_serie_lote'];
        $this->cantidad_solicitada      = $prest['cantidad_solicitada'];
        $this->cantidad_entregada       = $prest['cantidad_entregada'];
        $this->fecha_solicitud          = $prest['fecha_solicitud'];
        $this->fecha_recepcion          = $prest['fecha_recepcion'];
        $this->fecha_entrega_programada = $prest['fecha_entrega_programada'];
        $this->fecha_entrega_real       = $prest['fecha_entrega_real'];
        $this->id_status_prestamo       = $prest['id_status_prestamo'];
        $this->renovaciones             = $prest['renovaciones'];
        $this->adeudo_saldado           = $prest['adeudo_saldado'];

    }

    public function insert(){
        $query = "INSERT INTO inventarios_deii.inventario_prestamos (
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
            adeudo_saldado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

        $result = $this->db->insert($query, array($this->id_usuario,
                                                  $this->id_existencia_lab,
                                                  $this->id_serie_lote,
                                                  $this->cantidad_solicitada,
                                                  $this->cantidad_entregada,
                                                  $this->fecha_recepcion,
                                                  $this->fecha_entrega_programada,
                                                  $this->fecha_entrega_real,
                                                  $this->id_status_prestamo,
                                                  $this->renovaciones,
                                                  $this->adeudo_saldado));
        if (!$result) {
            $this->lastError = $this->db->getLastError();
            return false;
        }
        $this->id_inventario_prestamo = $this->db->lastid;
        return true;
    }


    public function update(){
        $query = "UPDATE inventarios_deii.inventario_prestamos
                    SET id_usuario                  = ?,
                        id_existencia_lab           = ?,
                        id_serie_lote               = ?,
                        cantidad_solicitada         = ?,
                        cantidad_entregada          = ?,
                        fecha_solicitud             = ?,
                        fecha_recepcion             = ?,
                        fecha_entrega_programada    = ?,
                        fecha_entrega_real          = ?,
                        id_status_prestamo          = ?,
                        renovaciones                = ?,
                        adeudo_saldado              = ?
                    WHERE id_inventario_prestamo = ?";

        $result = $this->db->update($query, array($this->id_usuario,
                                                  $this->id_existencia_lab,
                                                  $this->id_serie_lote,
                                                  $this->cantidad_solicitada,
                                                  $this->cantidad_entregada,
                                                  $this->fecha_solicitud,
                                                  $this->fecha_recepcion,
                                                  $this->fecha_entrega_programada,
                                                  $this->fecha_entrega_real,
                                                  $this->id_status_prestamo,
                                                  $this->renovaciones,
                                                  $this->adeudo_saldado,
                                                  $this->id_inventario_prestamo));

        if (!$result || !$result->rowCount()) {
            $this->lastError = $this->db->getLastError();
            return false;
        }
        return true;
    }

    public function save()
    {
        if (empty($this->id_inventario_prestamo) || $this->id_inventario_prestamo == 0) {
            return $this->insert();
        } 
        else {
            return $this->update();
        }
    }
}


if (isset($_GET['test'])) {
    include_once("../includes/conectaBD.php");

    $inventario = new InventarioPrestamo($link,"14");

    //Campos obligatorios
    $inventario->id_usuario = "104";
    $inventario->id_existencia_lab="3";
    $inventario->adeudo_saldado = 0;
    $inventario->cantidad_solicitada = "1";
    $inventario->cantidad_entregada="1";
    $inventario->id_status_prestamo="2";

    $inventario->save();
}