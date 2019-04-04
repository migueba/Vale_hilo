<?php
  // Database Structure
  $host="localhost";
  $username="root";
  $password="";
  $databasename="urdido_engomado";

  $mysqli = new mysqli($host,$username,$password,$databasename);
  $mysqli->set_charset("utf8");

  $consulta = "SELECT UPPER(A.descripcion) as descripcion, A.hilo
    FROM existencia A ORDER BY A.descripcion";

  if ($resultado = $mysqli->query($consulta)) {
    $HilosData = array();
    if($resultado->num_rows > 0){
      while($row = $resultado->fetch_assoc()){
        $data['label'] = $row['descripcion'];
        $data['value'] = $row['hilo'];
        array_push($HilosData, $data);
      }
    }

    $resultado->close();
    $mysqli->close();
    echo json_encode($HilosData,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }
?>
