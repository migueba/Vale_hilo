<?php

  function guarda_urdido() {
      include("bd.php") ;

      $date = str_replace('/', '-',$_GET['fecha']) ;
      $newDate = date("Y-m-d", strtotime($date)) ;

      $consulta = "INSERT INTO urdido(urdido_usuario, roturas, turno, hilo, julios, fecha)".
      "VALUES(" .$_GET['n_oficial']. "," .$_GET['roturas']. "," .$_GET['turno']. "," .$_GET['clave_hilo'].
        "," .$_GET['julios']. "," .$newDate. ")" ;


  }

?>
