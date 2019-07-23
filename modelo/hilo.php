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

    $consulta = "SELECT * FROM
      (SELECT A.hilo as clave,A.Fecha , A.lote,
        (A.numero-(SUM(IFNULL(D.numero,0))+SUM(IFNULL(B.numero,0)))) as tarima, E.descripcion as presentacion,
        ROUND((A.pesoneto-(SUM(IFNULL(D.peso,0))+SUM(IFNULL(B.peso,0)))),4) AS pesoneto, (A.bobinas-(SUM(IFNULL(D.bobinas,0))+SUM(IFNULL(B.bobinas,0)))) as bobinas ,
        IF(A.origen=1,\"NORMAL   \",IF(A.origen=6,\"NORMAL   \",\"DEVOLUCION\")) as entrada,
        A.identradash as id, B.numero as numerob, B.peso as pesob, B.bobinas as bobib
      FROM entradash A
      LEFT JOIN (
    		  SELECT sum(IFNULL(A.kilos,0)) as peso, sum(IFNULL(A.Bobinas,0)) as bobinas, sum(IFNULL(A.presenta_cant,0)) as numero, A.id_entrada
            FROM vale_entrada A
            INNER JOIN vale_hilo B ON A.idvale = B.idvale_hilo
      		  WHERE B.hilo = ". $_GET['idhilo'] ." AND B.estado = -1 group by A.id_entrada
    	) B ON  A.identradash = B.id_entrada
      LEFT JOIN (
    		SELECT A.id_entrada, SUM(IFNULL(A.numero,0)) as numero, SUM(IFNULL(A.peso,0)) as peso, SUM(IFNULL(A.bobinas,0)) as bobinas
            FROM salidash_detalle A
            INNER JOIN entradash B ON A.id_entrada = B.identradash AND B.estatus <> 0
            INNER JOIN salidash C ON A.id_salida = C.idsalidash AND C.estatus <> 0
            WHERE B.hilo = ". $_GET['idhilo'] ."
            GROUP BY A.id_entrada
    	) D ON A.identradash = D.id_entrada
      INNER JOIN presentacion E ON A.id_presenta = E.idpresentacion
        WHERE A.hilo = ". $_GET['idhilo'] ." AND A.estatus <> ( 0 ) AND A.fecha <= ". $_GET['fecha_v'] ."
        GROUP BY A.identradash
        ORDER BY A.fecha ASC) as S
  WHERE S.pesoneto > 0 AND S.bobinas <> 0 ";

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
