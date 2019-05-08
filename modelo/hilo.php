<?php
function hilo_inf() {
  include("bd.php") ;

  $consulta = "SELECT IF(A.Prod_neta<>0.96,\"COMPRADO \",\"PRODUCIDO \") as prod ,
    UPPER(A.descripcion) as descripcion, UPPER(A.generico) as generico
    FROM articulo A WHERE A.hilo = ". $_GET['idhilo'] ."  LIMIT 1";

  if ($resultado = $mysqli->query($consulta)) {
    $fila = $resultado->fetch_row() ;
    $data = array();

    $data['descripcion'] = $fila[1] ;
    $data['prod'] = $fila[0] ;
    $data['generico'] = $fila[2] ;

    $resultado->close();
    $mysqli->close();

    if(!is_null($data['descripcion'])){
      header('Content-Type: application/json');
      echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }else{
      echo "No Hay Datos";
    }
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }
}

function hilo_entradas() {
  include("bd.php") ;

  $consulta = "SELECT A.hilo as clave,A.Fecha , A.lote,
    (A.numero-SUM(IFNULL(D.numero,0))) as tarima, E.descripcion as presentacion,
    (A.pesoneto-SUM(IFNULL(D.peso,0))) AS pesoneto, (A.bobinas-SUM(IFNULL(D.bobinas,0))) as bobinas ,
    IF(A.origen=1,\"NORMAL   \",\"DEVOLUCION\") as entrada,
    A.id_ent as id
  FROM entradash A
    LEFT JOIN vale_entrada B ON A.id_ent = B.id_entrada
    LEFT JOIN vale_hilo C ON B.idvale = C.idvale_hilo AND C.estado <> 0
    LEFT JOIN salidash_detalle D ON A.identradash = D.id_entrada
    INNER JOIN presentacion E ON A.id_presenta = E.idpresentacion
    WHERE A.hilo = ". $_GET['idhilo'] ." AND A.estatus = ( 1 ) AND IFNULL(C.estado,0) = 0
    GROUP BY A.identradash
    ORDER BY A.fecha ASC";

    if ($resultado = $mysqli->query($consulta)) {
      $rawdata = array();
      $i=0;
      while($row = mysqli_fetch_array($resultado)){ $rawdata[$i] = $row;$i++;}
      $resultado->close();
      $mysqli->close();
      header('Content-Type: application/json');
      echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
      echo "Fallo la Conexion a la Base de Datos";
    }
}

function hilo_nombre() {
  include("bd.php") ;

  $consulta = "SELECT UPPER(A.descripcion) as descripcion, A.hilo
    FROM articulo A
    WHERE UPPER(A.descripcion) LIKE '%" . strtoupper($_GET['term']) . "%'
    ORDER BY A.descripcion";

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
    header('Content-Type: application/json');
    echo json_encode($HilosData,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }
}

if(isset($_GET['function']) && !empty($_GET['function'])){
    $function = $_GET['function'];
    switch($function) {
        case 'hilo_inf' : hilo_inf();break;
        case 'hilo_entradas' : hilo_entradas();break;
        case 'hilo_nombre' : hilo_nombre();break;
        // ...etc...
    }
}
?>
