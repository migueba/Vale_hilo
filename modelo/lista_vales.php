<?php
  // Database Structure
  $host="localhost";
  $username="root";
  $password="";
  $databasename="urdido_engomado";

  $mysqli = new mysqli($host,$username,$password,$databasename);
  $mysqli->set_charset("utf8");

  $consulta = "SELECT A.idvale_hilo as id, A.clave_hilo, A.fecha, A.turno,
    A.supervisor,
    SUM(B.Bobinas) as Bobinas,
    SUM(B.kilos) as Kilos,
    IF(A.estado=-1,\"PENDIENTE\",\"SURTIDO   \") as estado
  FROM vale_hilo A
    LEFT JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    WHERE A.estado <> 0
    GROUP BY A.idvale_hilo  ";

    if ($resultado = $mysqli->query($consulta)) {
      $rawdata = array(); //creamos un array
      //guardamos en un array multidimensional todos los datos de la consulta
      $i=0;
      while($row = mysqli_fetch_array($resultado)){
        $rawdata[$i] = $row;
        $i++;
      }
      $resultado->close();
      $mysqli->close();
      echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
    }
?>
