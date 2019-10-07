<?php

  // Obtiene el Inventario General
  function inventario_general() {
    include("bd.php") ;

    $consulta = "SELECT s.fecha, s.hilo, s.lote, s.turno, s.origen,
      sum(s.bobinas) as bobinas, sum(s.pesoneto_cal ) as pesoneto_cal, s.descripcion
    FROM
    	 (SELECT A.fecha, A.hilo, A.lote, A.turno, A.origen,
        A.Bobinas-SUM(IFNULL(B.bobinas,0)) as bobinas,
        A.PESONETO-SUM(IFNULL(B.peso,0)) as pesoneto_cal, C.descripcion
    	 FROM entradash A
    	  LEFT JOIN SALIDASH_DETALLE B ON A.identradash = B.id_entrada
    	   INNER JOIN articulo C ON A.hilo = C.hilo
    	 WHERE a.eSTATUS <> 0 GROUP BY A.identradash ORDER BY A.hilo) as s
    where s.pesoneto_cal <> 0 group by s.hilo order by s.descripcion";

    if($resultado = $mysqli->query($consulta)) {
      $rawdata = array();
      while($row = $resultado->fetch_array(MYSQLI_ASSOC)){
        $rawdata[] = $row;
      }
      $resultado->close();
      $mysqli->close();

      header('Content-Type: application/json');
      echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
    }
  }

  // Obtiene los hilos que tienen inventario
  function inventario_hilo() {
    include("bd.php") ;

    /*$consulta = "SELECT s.descripcion, sum(s.pesoneto_cal ) as pesoneto_cal
    FROM
       (SELECT A.fecha, A.hilo, A.lote, A.turno, A.origen,
        A.Bobinas-SUM(IFNULL(B.bobinas,0)) as bobinas,
        A.PESONETO-SUM(IFNULL(B.peso,0)) as pesoneto_cal, C.descripcion
       FROM entradash A
        LEFT JOIN SALIDASH_DETALLE B ON A.identradash = B.id_entrada
         INNER JOIN articulo C ON A.hilo = C.hilo
       WHERE a.eSTATUS <> 0 GROUP BY A.identradash ORDER BY A.hilo) as s
    where s.pesoneto_cal <> 0 group by s.hilo order by s.descripcion";*/

    $consulta = "SELECT s.descripcion, SUM(s.bobinas) as bobinas, ROUND(SUM(s.pesoneto_cal),2) as pesoneto_cal
    FROM
    	(SELECT A.hilo,
    		A.Bobinas-SUM(IF(E.estatus <> 0, IFNULL(B.bobinas,0), 0)) as bobinas,
    		A.PESONETO-SUM(IF(E.estatus <> 0,IFNULL(B.peso,0),0)) as pesoneto_cal, C.descripcion
    	FROM entradash A
    		LEFT JOIN SALIDASH_DETALLE B ON A.identradash = B.id_entrada
    		LEFT JOIN salidash E ON B.id_salida = E.idsalidash
    		INNER JOIN articulo C ON A.hilo = C.hilo
    	WHERE A.ESTATUS <> 0
    	GROUP BY A.identradash ORDER BY A.hilo) as s
    WHERE s.pesoneto_cal <> 0 GROUP BY s.hilo ORDER BY s.hilo,s.descripcion";

    if($resultado = $mysqli->query($consulta)) {
      $rawdata = array();
      while($row = $resultado->fetch_array(MYSQLI_ASSOC)){
        $rawdata[] = $row;
      }
      $resultado->close();
      $mysqli->close();

      header('Content-Type: application/json');
      echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
    }
  }



  if(isset($_GET['function']) && !empty($_GET['function'])){
      $function = $_GET['function'];
      switch($function) {
          case 'inventario_general' : inventario_general();break;
          case 'inventario_hilo' : inventario_hilo();break;
          // ...etc...
      }
  }
?>
