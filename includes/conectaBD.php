<?php
/*
Desarrollado para la Universidad Iberoamericana por:

Jefe de Procesos CCA: 
	Omar Ugalde Puebla

Proyecto: MigraciÃ³n Isaac php 7.4 mysql 5.7
Fecha: 05 octubre 2021
*/
include("config.php");
include_once("ccaMySQL.php");

/*Intenta conectarse con la base de datos*/
$dsn =  "mysql:host=$DBSERVER;" .
        "dbname=$DEFAULTDB;" . 
        "charset=$CHARSET";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true
	];
try 
{

    $pdo = new PDO($dsn, $DBUSER, $DBPASSWD, $options);
    
} 
catch (Exception $e)
{
  
  echo "Error al conectarse con la base de datos.\n";
  if( !error_reporting() ) 
      echo $e->getMessage();
  exit();

 }
 $link = new ccaMySQL($pdo);
?>