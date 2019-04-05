<?php
  // Database Structure
  $host="localhost";
  $username="root";
  $password="";
  $databasename="urdido_engomado";

  $mysqli = new mysqli($host,$username,$password,$databasename);
  $mysqli->set_charset("utf8");

  $consulta = "SELECT A.clave, IF(A.oriextra=0,\"NORMAL   \",\"DEVOLUCION\") AS entrada, A.lote,
    A.tarima, A.pesoneto, (A.bobinas+A.Cbobina+A.Pbobina) as bobinas ,
    IF(A.tarima<>0,\"TARIMA  \",IF(A.bolsa<>0,\"BOLSA  \",IF(A.caja<>0,\"CAJA  \",IF(A.palet<>0,\"PALET  \",\"N/D  \")))) as presentacion,
  	IF(A.tipo=1,\"BOBINA LLENA  \",\"BOBINA GALLO  \") AS tipo,
    0 AS sel, A.id_ent
  FROM entradash A
    WHERE A.clave = ". $_POST['idhilo'] ." AND A.estatus = ( 1 )
    ORDER BY A.clave, A.oriextra";

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
    echo "Fallo la Conexion a la Base de Datos";
  }

?>
