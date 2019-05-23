<?php
  include("bd.php") ;

  $total_bobinas = 0 ;
  $total_kilos = 0 ;
  $Cajas = 0 ;
  $Palet = 0 ;
  $Bolsas = 0 ;
  $Tarimas = "";
  $TarimasT = 0;

  $consulta = "SELECT A.idvale_hilo as id, A.Fecha, A.turno,B.Bobinas, B.kilos ,D.descripcion as Presenta,
    B.Presenta_cant as numero, CONCAT(trim(E.nombre),\" \",trim(E.apaterno),\" \",trim(E.amaterno)) as supervisor,
    C.hilo, C.descripcion, IF(C.prod_neta <> .96,\"COMPRA-MAQ.\",\"PRODUCIDO   \") as tipo
  FROM vale_hilo A
    INNER JOIN vale_entrada B ON A.idvale_hilo = B.idvale
    LEFT JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN Presentacion D ON B.Presenta = D.idpresentacion
    LEFT JOIN Usuarios E ON A.supervisor = E.num_emp
  WHERE A.idvale_hilo = " .$_GET['id_vale']. " AND A.estado <> 0";

  if ($resultado = $mysqli->query($consulta)) {
    $valedata = array(); $i=0;
    while($row = mysqli_fetch_array($resultado)){
      $valedata[$i] = $row;
      $i++;
    }

    for($i=0; $i < count($valedata); $i++){
      $total_bobinas += (int)$valedata[$i]['Bobinas'] ;
      $total_kilos += (float)$valedata[$i]['kilos'] ;

      if(trim($valedata[$i]['Presenta']) === "BOLSA"){                 // Bolsa
          $Bolsas += (int)$valedata[$i]['numero'] ;
      }else if(trim($valedata[$i]['Presenta']) === "CAJA"){             // CAJA
          $Cajas += (int)$valedata[$i]['numero'] ;
      }else if(trim($valedata[$i]['Presenta']) === "PALET"){            //PALET
          $Palet += (int)$valedata[$i]['numero'] ;
      }else if(trim($valedata[$i]['Presenta']) === "TARIMA"){
        $Tarimas = ($i<>0) ? $Tarimas. " , " .$valedata[$i]['numero'] : $Tarimas. "" .$valedata[$i]['numero'] ;
        $TarimasT++;
      }
    }

    $consul = "SELECT A.Bobinas, A.Kilos, B.descripcion as destino, A.tela
      FROM vale_hilo_detalle A
      LEFT JOIN Origen B ON A.destino = B.idorigen
    WHERE A.idvale_hilo =  ".$_GET['id_vale'] ;

    $result = $mysqli->query($consul) ;
    $vale_detalle = array(); $i=0;
    while($row = mysqli_fetch_array($result)){
      $vale_detalle[$i] = $row;
      $i++;
    }

    $result->close();
    $resultado->close();
    $mysqli->close();
  }else{
    $mysqli->close();
  }
?>

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="Muestra la informacion del Vale">
    <meta name="author" content="Julio Cesar Barradas">

    <title>Vale de Hilo N°<?php echo $_GET['id_vale']; ?></title>

    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/sticky-footer.css">
    <script src="../js/bootstrap.js"></script>
  </head>

  <body>
    <div class="container" style="margin-top: 20px; border-style: dashed; border-color: black; ">
      <div class="row">
        <div class="col-md-4" >
          <img style="height: 100px; width: 220px; display: block;"  src="../images/FABRICA MARÍA SIN FONDO_negro_corta.png" />
        </div>
        <div class="col-md-8" >
          <h3 class="mt-4 text-center">Vale de Hilo N° <?php echo $_GET['id_vale']; ?></h3>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3" >
          <p>Fecha: <strong><?php echo date("d/m/Y",strtotime($valedata[0]['Fecha'])); ?></strong></p>
          <p>Hilo: <strong><?php echo $valedata[0]['hilo']; ?></strong></p>
          <p>Bobinas: <strong><?php echo number_format($total_bobinas,2); ?></strong></p>
        </div>
        <div class="col-md-6" >
          <p>Supervisor: <strong><?php echo $valedata[0]['supervisor']; ?></strong></p>
          <p><strong><?php echo $valedata[0]['descripcion']; ?></strong></p>
          <p>Nº Tarima: <strong><?php echo $Tarimas; ?></strong></p>
        </div>
        <div class="col-md-3" >
          <p>Turno: <strong><?php echo $valedata[0]['turno']; ?></strong></p>
          <p><p><strong><?php echo $valedata[0]['tipo']; ?></strong></p></p>
          <p>Kilos: <strong><?php echo number_format($total_kilos,2); ?></strong></p>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3" >
            <p>Bolsas: <strong><?php echo $Bolsas; ?></strong></p>
        </div>
        <div class="col-md-3" >
            <p>Cajas: <strong><?php echo $Cajas; ?></strong></p>
        </div>
        <div class="col-md-3" >
            <p>Palet: <strong><?php echo $Palet; ?></strong></p>
        </div>
        <div class="col-md-3" >
            <p>Tarimas: <strong><?php echo $TarimasT; ?></strong></p>
        </div>
      </div>

      <div class="row" style="margin-top: 20px;">
        <div class="col-md-12" >
          <table id="destino" class="table">
            <thead class=\"thead-light\">
              <tr>
                <th scope=\"col\">Bobinas</th>
                <th scope=\"col\">Kilos</th>
                <th scope=\"col\">Destino</th>
                <th scope=\"col\">Tela</th>
              </tr>
            </thead>
            <tbody>
              <?php
                for($i=0; $i < count($vale_detalle); $i++){
                  echo "<tr>";
                  echo "<td>" .$vale_detalle[$i]['Bobinas']. "</td>";
                  echo "<td>" .$vale_detalle[$i]['Kilos']. "</td>";
                  echo "<td>" .$vale_detalle[$i]['destino']. "</td>";
                  echo "<td>" .$vale_detalle[$i]['tela']. "</td>";
                  echo "</tr>";
                }
                ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </body>
</html>
