<?php
  // Database Structure
  $host="localhost";
  $username="root";
  $password="";
  $databasename="urdido_engomado";

  $mysqli = new mysqli($host,$username,$password,$databasename);
  $mysqli->set_charset("utf8");

  $consulta = "SELECT UPPER(A.descripcion) as descripcion FROM existencia A ORDER BY A.descripcion";

  if ($resultado = $mysqli->query($consulta)) {
    while ($obj = $resultado->fetch_object()) {
        $data[] = $obj->descripcion;
    }
    $resultado->close();
  }else{
    echo "Fallo la Conexion a la Base de Datos";
  }

  $mysqli->close();

  echo json_encode($data,JSON_UNESCAPED_UNICODE);
?>
