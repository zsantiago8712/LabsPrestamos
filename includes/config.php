<?php
/*
*Desarrollado para la Universidad Iberoamericana por:
*
*Jefe de Desarrollo de Soluciones de Software: 
*	Antonio Carlos Cardena Matamoros
*Programadores:
*
*Fecha de ultima modificacion:
*	20 de Febrero de 2016
*Modifico:
*	Antonio Carlos Cardena Matamoros
*
*04 noviembre 2021: Migración a php 7.4 mysql 5.7
*Omar Ugalde
*/

/*Informacion para conexion a la base de datos*/
/*Servidor*/
$DBSERVER = "172.16.3.113";
/*Usuario*/
$DBUSER = "root";
$DBPASSWD = "Cal1d@dCCA";
/*Bases de datos*/
$DBISED = "ised";
$DBISAPE = "recursos_humanos";
$DBISACA = "catalogos";
$DBISAAC = "isaac";
$DBINST = "institutos";
$DEFAULTDB = "inventarios_deii";
$LAB = "inventarios_deii";
$CHARSET = "utf8mb4";

/*Informacion para conexion con el Active Directory*/
/*Definimos la variables para establecer conexion con el Active Directory*/
$USUARIOADMINAD = "****";
$CONTRASENAADMINAD = "****";
$SERVIDORAD = "ldaps://172.16.2.16";
$NOMBREDOMINIOAD = "@alumnos.uia";
$NOMBRECONTENEDORDOMINIO = "OU=Alumnos,DC=alumnos,DC=uia";

/*Informacion del sistema*/
/*Nombre completo del sistema*/
$SISTEMANOMBRE = "";
$SISTEMAACRONIMO = "";
$SISTEMAVERSION = "";
$IDAPLICACION = 2201;

/*Infromacion para el webservice*/
/*Definición de constantes para el webservice*/
//$WSDL = "http://serviciosenlineapru.ibero.mx/ws/WSServiciosIbero.cfc?wsdl&method=accesoTarjetas&tipoConsulta=1&token=";
//$WSTOKEN = "72B3C263EAD3B00R";
$WSDL = "https://serviciosenlinea.ibero.mx/ws/WSServiciosIbero.cfc?wsdl&method=accesoTarjetas&tipoConsulta=1&usrauth=c9f1b784d96fb088cd42d5641b1d74f4&token=";
$WSTOKEN = "72B3C263EAD3B00R";
?>