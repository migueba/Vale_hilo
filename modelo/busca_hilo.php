<?php
  // Database Structure
  $host="localhost";
  $username="root";
  $password="";
  $databasename="urdido_engomado";

  $mysqli = new mysqli($host,$username,$password,$databasename);
  $mysqli->set_charset("utf8");

  $consulta = "SELECT IF(A.Prod_neta<>0.96,\"COMPRADO \",\"PRODUCIDO \") as prod ,
    UPPER(A.descripcion) as descripcion, UPPER(A.generico) as generico
    FROM existencia A WHERE A.hilo = ". $_POST['idhilo'] ."  LIMIT 1";

  if ($resultado = $mysqli->query($consulta)) {
    /* obtener el array de objetos */
    $fila = $resultado->fetch_row() ;
    $data = array();

    $data['descripcion'] = $fila[1] ;
    $data['prod'] = $fila[0] ;
    $data['generico'] = $fila[2] ;

    $resultado->close();
    $mysqli->close();
    echo json_encode($data,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }
?>
