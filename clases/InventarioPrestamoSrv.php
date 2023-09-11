<?php
/*
*
* Clase InventarioPrestamosSrv
* creada Por Derek Escamilla. 
* fecha 31 Agosto 2023
* Descripción: Está clase actua como un servicio que se genera a la tabla inventarios_deii.Inventariosprestamos. 
* En este se generan funciones para sacar las solictidues, activos y adeudos que tenga un estudiante por laboratorio. 
* Posee funcioners adicionales como sacar todas las solicitdues y cambiar las solitidues a por entregar. 
*/
class InventarioPrestamoSrv{
    public static $DB;
    public static $lasterror;
    public static $numrows;

    public static function inicializaInventarioPrestamoSrv($DB)
    {
        self::$DB = $DB;
    }
    public static function getPrestamosSolicitudPorLaboratorio($id_lab,$cuenta_alumno = "")
    {
        $WhereClause = "";
        $parametros = Array();
        if($cuenta_alumno == "")
        {
            $WhereClause = "ip.id_status_prestamo in(100,2,4,6,1)";
        }else{
            array_push($parametros,$cuenta_alumno);
           $WhereClause = "up.cuenta = ?";
        }
        array_push($parametros,$id_lab);
        $qry="SELECT 	up.nombres, 
		up.ap_paterno, 
        up.ap_materno, 
        up.cuenta, 
        up.id_usuario,
        if(
        sum( case
            ip.id_status_prestamo
			when 6
			 then 1
			else 0
            end
            ) > 0, 1, 0)
            as adeudos,
            if(
		sum( case
             when fecha_entrega_programada < cast(now() as date)
				and ip.id_status_prestamo = 2
			then 1
            else 0
            end) > 0,1,0) as vencidos
               
FROM 	inventarios_deii.inventario_prestamos as ip

        INNER JOIN inventarios_deii.inventario_existencia_laboratorio as el on 
        ip.id_existencia_lab = el.id_existencia_lab
        
        INNER JOIN inventarios_deii.usuarios_prestamos as up on 
        ip.id_usuario = up.id_usuario
        
WHERE " . 	$WhereClause . "
	and el.id_laboratorio = ?
    
GROUP BY	up.nombres, 
			up.ap_paterno, 
			up.ap_materno, 
			up.cuenta, 
			up.id_usuario";
        $resultado = self::$DB->query($qry,$parametros);
        if(!$resultado)
        {
            self::$lasterror = self::$DB->lasterror;
            return false;
        }
        self::$numrows = $resultado->rowCount();
        return $resultado->fetchAllArrayAsoc();
    }
    public static function getActivosAlumno($id_lab,$id_usuario)
    {
        $query = "SELECT a.id_inventario_prestamo, a.id_serie_lote,
                        z.disponible, z.id_existencia_lab, 
                        d.id_inventario, d.id_cuantificacion, a.cantidad_solicitada, 
                        a.cantidad_entregada, a.fecha_recepcion,
                        date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
                        a.fecha_solicitud,  
        concat(d.descripcion, ' ',
        IFNULL(
          group_concat(
              case c.tipo
                  when 1 
                      then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
                  else
                      concat(c.caracteristica, ' : ' , il.valor)
              end 
          ORDER BY c.caracteristica SEPARATOR ' '),
          '')) as descripcion
            FROM inventarios_deii.inventario_prestamos as a
            INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on
                         a.id_existencia_lab = z.id_existencia_lab
        INNER JOIN inventarios_deii.inventario_item as d on 
                        z.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on 
                    ci.id_inventario = d.id_inventario
        LEFT JOIN inventarios_deii.inventario_caracteristica c on c.id_caracteristica = ci.id_caracteristica
        LEFT JOIN inventarios_deii.unidad_prefijos u on 
                    u.id_prefijo = ci.id_prefijo
        LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
            il.id_lista 			= ci.id_lista
        and	il.id_caracteristica 	= ci.id_caracteristica
        WHERE a.id_status_prestamo = 2 AND z.id_laboratorio = ? AND a.id_usuario = ?
        GROUP by  a.id_inventario_prestamo,
					z.disponible, 
                    z.id_existencia_lab, 
                    d.id_inventario, 
                    a.cantidad_solicitada,
                    a.cantidad_entregada, 
                    a.fecha_recepcion,
                    a.fecha_solicitud";
        $resultado = self::$DB->query($query, array($id_lab,$id_usuario));
            if(!$resultado)
            {
                self::$lasterror = self::$DB->lasterror;
                return false;
            }
            return $resultado->fetchAllArrayAsoc();
    }
    public static function getAdeudosAlumno($id_lab,$id_usuario)
    {
        $query = "SELECT a.id_inventario_prestamo,
        z.disponible, 
        z.id_existencia_lab,z.prestamos as cant_max,
         d.id_inventario, 
         a.cantidad_entregada,
         d.id_cuantificacion, 
         a.cantidad_solicitada, 
         g.comentarios,
         g.id_adeudo,
         a.fecha_recepcion,
         date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
         a.fecha_solicitud, 
        concat(d.descripcion, ' ',
            IFNULL(
            group_concat(
                case c.tipo
                    when 1 
                        then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
                    else
                        concat(c.caracteristica, ' : ' , il.valor)
                end 
            ORDER BY c.caracteristica SEPARATOR ' '),
            '')) as descripcion

            FROM inventarios_deii.inventario_prestamos as a

            INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on 
            a.id_existencia_lab = z.id_existencia_lab

            INNER JOIN inventarios_deii.inventario_item as d on 
                        z.id_inventario = d.id_inventario

            LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on 
                        ci.id_inventario = d.id_inventario

            LEFT JOIN inventarios_deii.inventario_caracteristica c on 
                        c.id_caracteristica = ci.id_caracteristica

            LEFT JOIN inventarios_deii.unidad_prefijos u on 
                        u.id_prefijo = ci.id_prefijo

            LEFT JOIN inventarios_deii.inventario_adeudos g on 
                        g.id_usuario = a.id_usuario
                        and a.id_inventario_prestamo = g.id_inventario_prestamo

            LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
                    il.id_lista 			= ci.id_lista
                    and	il.id_caracteristica 	= ci.id_caracteristica

            WHERE a.id_status_prestamo = 6 AND z.id_laboratorio = ? AND a.id_usuario = ? AND g.activo = 1
            GROUP by  a.id_inventario_prestamo,
                        z.disponible, 
                        z.id_existencia_lab, 
                        d.id_inventario, 
                        a.cantidad_solicitada,
                        a.cantidad_entregada, 
                        a.fecha_recepcion,
                        a.fecha_solicitud";
    $resultado = self::$DB->query($query, array($id_lab,$id_usuario));
    if(!$resultado)
    {
        self::$lasterror = self::$DB->lasterror;
        return false;
    }
    return $resultado->fetchAllArrayAsoc();
    }
    public static function getSolicitudAlumno($id_lab,$id_usuario)
    {
        $query = "SELECT a.id_inventario_prestamo,
        z.disponible, 
        z.id_existencia_lab,z.prestamos as cant_max,
         d.id_inventario, 
         d.id_cuantificacion, 
         a.cantidad_solicitada, 
         a.fecha_recepcion,
         date_format(a.fecha_entrega_programada,'%d-%m-%y') as fecha_entrega_programada, 
         a.fecha_solicitud, 
concat(d.descripcion, ' ',
IFNULL(
group_concat(
case c.tipo
  when 1 
      then concat(ci.valor,' ',u.simbolo, ' ', c.simbolo )
  else
      concat(c.caracteristica, ' : ' , il.valor)
end 
ORDER BY c.caracteristica SEPARATOR ' '),
'')) as descripcion
FROM inventarios_deii.inventario_prestamos as a
INNER JOIN inventarios_deii.inventario_existencia_laboratorio as z on a.id_existencia_lab = z.id_existencia_lab
INNER JOIN inventarios_deii.inventario_item as d on z.id_inventario = d.id_inventario
LEFT JOIN inventarios_deii.inventario_caracteristica_item ci on ci.id_inventario = d.id_inventario
LEFT JOIN inventarios_deii.inventario_caracteristica c on c.id_caracteristica = ci.id_caracteristica
LEFT JOIN inventarios_deii.unidad_prefijos u on u.id_prefijo = ci.id_prefijo
LEFT JOIN inventarios_deii.inventario_caracteristica_lista as il on
il.id_lista 			= ci.id_lista
and	il.id_caracteristica 	= ci.id_caracteristica
WHERE a.id_status_prestamo = 100 AND z.id_laboratorio = ? AND a.id_usuario = ?
GROUP by  a.id_inventario_prestamo,
    z.disponible, 
    z.id_existencia_lab, 
    d.id_inventario, 
    a.cantidad_solicitada,
    a.cantidad_entregada, 
    a.fecha_recepcion,
    a.fecha_solicitud";
     $resultado = self::$DB->query($query, array($id_lab,$id_usuario));
     if(!$resultado)
     {
         self::$lasterror = self::$DB->lasterror;
     return false;
     }
     return $resultado->fetchAllArrayAsoc();
    }
    public static function carritoPorEntregar($link,$usuario, $tipo_usuario, $id_laboratorio){
        self::$DB = $link;
		$qry = "update	inventarios_deii.inventario_prestamos as ip

						inner join inventarios_deii.usuarios_prestamos as up on
						ip.id_usuario = up.id_usuario

						inner join inventarios_deii.inventario_existencia_laboratorio ex on
						ex.id_existencia_lab = ip.id_existencia_lab

				set 	ip.id_status_prestamo = 100

				where 	ip.id_status_prestamo = 1	
					and	up.cuenta = ? 
					and up.id_tipo_usuario = ?
					and ex.id_laboratorio  = ?";
		

		$chk = self::$DB->query($qry,array($usuario,$tipo_usuario,$id_laboratorio));

		self::$numrows = self::$DB->numrows;
		
		self::$lasterror = self::$DB->lasterror;
		
		return $chk;

	}
	public static function checkAdeudos($link,$usuario, $id_laboratorio)
	{
		$qry="SELECT a.id_status_prestamo, b.id_laboratorio FROM inventarios_deii.inventario_prestamos a
						inner join inventarios_deii.inventario_existencia_laboratorio b on
						b.id_existencia_lab = a.id_existencia_lab
						Inner join inventarios_deii.usuarios_prestamos as c on
						a.id_usuario = c.id_usuario
						WHERE a.id_status_prestamo = 6 AND c.id_usuario = ? AND b.id_laboratorio = ?";
		$chk = self::$DB->query($qry,array($usuario,$id_laboratorio));

		self::$numrows = self::$DB->affectedrows;
		
		self::$lasterror = self::$DB->lasterror;
		
		return $chk->fetchArrayAsoc();
	}
}
?>