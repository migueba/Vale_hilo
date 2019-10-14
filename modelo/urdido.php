<?php
  function ultima_orden() {
    include("bd.php") ;

    $consulta = "SELECT A.orden FROM urdido_engomado.urdido A order by A.hora desc limit 1";
    $ultima_orden = 0 ;

    if ($resultado = $mysqli->query($consulta)) {
      while($row = $resultado->fetch_assoc()){
        $ultima_orden = $row['orden'] ;
      }
      $resultado->close();
    }
    $mysqli->close();
    header('content-type text/plain') ;
    echo $ultima_orden ;
  }

  function lista_urdido() {
    include("bd.php") ;

    $consulta = "SELECT A.hilo, C.descripcion, SUM(B.julios) as julios, B.numeros, A.orden, A.fecha, A.horas, sum(B.roturas) as roturas,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
    GROUP BY B.id_urdido, B.numeros, B.julios
    ORDER BY A.hora DESC";

    if ($resultado = $mysqli->query($consulta)) {
      $rawdata = array();

      $i=0;
      while($rows = $resultado->fetch_array(MYSQLI_ASSOC)){
        //$rawdatapre[$i] = $rows;
        $rawdata[$i] = $rows;
        $i++;
      }

      $resultado->close();
      $mysqli->close();
      header('Content-Type: application/json');
      echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
    }
  }

  function guarda_urdido() {
      include("bd.php") ;
      /* PAra Obtener el ID del Empleado */
      $id_oficial_consulta = "SELECT * FROM usuarios WHERE num_emp = ".$_GET['n_oficial'] ;
      $resultado = $mysqli->query($id_oficial_consulta) ;

      while($row2 = $resultado->fetch_assoc()){
        $id_oficial_ = $row2['idusuario'] ;
      }

      $date = str_replace('/', '-',$_GET['fecha']) ;
      $newDate = date("Y-m-d", strtotime($date)) ;

      $consulta = "INSERT INTO urdido(tela, urdido_usuario, horas, hilo, orden, fecha)".
        "VALUES(\"" .$_GET['tela']. "\"," .$id_oficial_. "," .$_GET['horas']. "," .$_GET['clave_hilo']. "," .$_GET['orden']. ",\"" .$newDate. "\")" ;

      if ($resultado = $mysqli->query($consulta)) {
        $ultimo_id = $mysqli->insert_id ;
        $i = 1;
        foreach ( $_GET['detalle'] as $row) {
          $consulta = "INSERT INTO urdido_detalle(numeros, bobinas, julios, id_urdido, roturas)"
            . "VALUES(" .$row['numero']. "," .$row['bobina']. "," . ( $i==(count($_GET['detalle'])) ? 1-($i-$_GET['julios']) : 1) . "," .$ultimo_id. "," .$row['rotura']. ")" ;
          $mysqli->query($consulta) ;
          $i++;
        }

        $mysqli->close();
        header('content-type text/plain');
        echo "Se Guardo";
      }else {
        $mysqli->close();
        header('content-type text/plain');
        echo "No se pudo Almacenar";
      }
  }

  if(isset($_GET['function']) && !empty($_GET['function'])){
      $function = $_GET['function'];
      switch($function) {
          case 'guarda_urdido' : guarda_urdido(); break;
          case 'lista_urdido' : lista_urdido(); break;
          case 'ultima_orden' : ultima_orden(); break;
      }
  }

?>
