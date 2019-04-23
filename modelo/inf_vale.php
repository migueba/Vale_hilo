<?php
  include("bd.php") ;

  $total_bobinas = 0 ;
  $total_kilos = 0 ;
  $Cajas = 0 ;
  $Palet = 0 ;
  $Bolsas = 0 ;
  $Tarimas = "";
  $TarimasT = 0;

  $consulta = "SELECT A.idvale_hilo as id, A.Fecha, A.turno,
    B.Bobinas, B.Kilos,
    IF(B.Destino=1,\"Urdido\",IF(B.Destino=2,\"Tejido\",IF(B.Destino=3,\"Maquila\",\"Torzal\"))) as destino,
    B.tela, B.Presenta, B.Presenta_cant as cantidad, A.supervisor,
    C.hilo, C.descripcion, IF(C.prod_neta<>.96,\"COMPRADO-MAQ \",\"PRODUCIDO     \") as tipo
    FROM vale_hilo A
    INNER JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    INNER JOIN existencia C ON A.clave_hilo = C.hilo
    WHERE A.idvale_hilo = ".$_GET['id_vale']." AND A.estado <> 0
    ";

      if ($resultado = $mysqli->query($consulta)) {
        $valedata = array(); //creamos un array
        //guardamos en un array multidimensional todos los datos de la consulta
        $i=0;
        while($row = mysqli_fetch_array($resultado)){
          $valedata[$i] = $row;
          $i++;
        }

        for($i=0; $i < count($valedata); $i++){
          $total_bobinas += (int)$valedata[$i]['Bobinas'] ;
          $total_kilos += (float)$valedata[$i]['Kilos'] ;

          if($valedata[$i]['Presenta'] === '2'){
            // Bolsa
            $Bolsas += (int)$valedata[$i]['cantidad'] ;
          }else if($valedata[$i]['Presenta'] === '3'){
            // CAJA
            $Cajas += (int)$valedata[$i]['cantidad'] ;
          }else if($valedata[$i]['Presenta'] === '4'){
            //PALET
            $Palet += (int)$valedata[$i]['cantidad'] ;
          }
        }

        $consul = "SELECT B.tarima FROM vale_entrada A
          INNER JOIN entradash B ON A.id_entrada = B.id_ent
          WHERE A.idvale = ".$_GET['id_vale'] ;

        $result = $mysqli->query($consul) ;

        $i = 0 ;
        while($row2 = mysqli_fetch_array($result)){
          $dato = (string) $row2['tarima'] ;

          // IF Abreviado
          $Tarimas = ($i<>0) ? $Tarimas. " , " .$dato : $Tarimas. "" .$dato;

          $TarimasT++;
          $i++;
        }

        $result->close();
        $resultado->close();
        $mysqli->close();
      }else{
        $mysqli->close();
      }
?>
