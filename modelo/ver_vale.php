<!DOCTYPE html>
<?php session_start();
    include("inf_vale.php") ;
?>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vale de Hilo N°<?php echo $_GET['id_vale']; ?></title>

    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/sticky-footer.css">
    <script src="../js/bootstrap.js"></script>
  </head>

  <body>
    <main role="main" class="container" style="border-width: 2px; border-style: dashed; border-color: black; ">
      <h3 class="mt-5 text-center">Vale de Hilo N°<?php echo $_GET['id_vale']; ?></h3>
      <div class="row">
        <div class="col-md-3" >
          <p>Fecha: <strong><?php echo date("d-m-Y",strtotime($valedata[0]['Fecha'])); ?></strong></p>
          <p>Hilo: <strong><?php echo $valedata[0]['hilo']; ?></strong></p>
          <p>Bobinas: <strong><?php echo $total_bobinas; ?></strong></p>
        </div>
        <div class="col-md-6" >
          <p>Supervisor: <strong><?php echo $valedata[0]['supervisor']; ?></strong></p>
          <p><strong><?php echo $valedata[0]['descripcion']; ?></strong></p>
          <p>Kilos: <strong><?php echo $total_kilos; ?></strong></p>
        </div>
        <div class="col-md-3" >
          <p>Turno: <strong><?php echo $valedata[0]['turno']; ?></strong></p>
          <p><p><strong><?php echo $valedata[0]['tipo']; ?></strong></p></p>
          <p>Tarimas: <strong><?php echo $Tarimas; ?></strong></p>
        </div>
      </div>

      <div class="row">
      </div>

      <div class="row">
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
                for($i=0; $i < count($valedata); $i++){
                  echo "<tr>";
                  echo "<td>".$valedata[$i]['Bobinas']."</td>";
                  echo "<td>".$valedata[$i]['Kilos']."</td>";
                  echo "<td>".$valedata[$i]['destino']."</td>";
                  echo "<td>".$valedata[$i]['tela']."</td>";
                  echo "</tr>";
                }
                ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
</body></html>
