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
    echo "Fallo";
  }

  $mysqli->close();

  echo json_encode($data);
  //return json data
  //var_dump($data);
  //$myJSONString = json_encode($data);
  //$myArray = json_decode($myJSONString);

  //var_dump($myArray);

?>
