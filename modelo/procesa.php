<?php
  var_dump($_POST['idsupervisor']);
  var_dump($_POST['id_ent']);
  var_dump($_POST['bobinatotal']);

  $superheroes = $_POST['detalle'] ;
  $kilos_totales = $_POST['pesototal'] ;
  $kilos_detalle = 0 ;

  $keys = array_keys($superheroes);
  for($i = 0; $i < count($superheroes); $i++) {
      echo $keys[$i] . "{<br>";
      foreach($superheroes[$keys[$i]] as $key => $value) {
          echo $key . " : " . $value . "<br>";
          if($key === "kgs" ){
            $kilos_detalle =  $kilos_detalle + floatval($value) ;
          }
      }
      echo "}<br>";
  }

  echo $kilos_detalle ;

?>
