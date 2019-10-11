<?php

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

      $consulta = "INSERT INTO urdido(tela, urdido_usuario, roturas, turno, hilo, orden, fecha)".
        "VALUES(\"" .$_GET['tela']. "\"," .$id_oficial_. "," .$_GET['roturas']. "," .$_GET['turno']. "," .$_GET['clave_hilo']. "," .$_GET['orden']. ",\"" .$newDate. "\")" ;

      if ($resultado = $mysqli->query($consulta)) {
        $ultimo_id = $mysqli->insert_id ;
        $i = 0;
        foreach ( $_GET['detalle'] as $row) {
          $consulta = "INSERT INTO urdido_detalle(numeros, bobinas, julios, id_urdido)"
            . "VALUES(" .$row['numero']. "," .$row['bobina']. "," . ( $i===(count($row)+1) ? $_GET['julios']-$i : 1) . "," .$ultimo_id. ")" ;
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
      }
  }

?>
