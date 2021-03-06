<?php
function lista_vale() {
  include("bd.php") ;

  $consulta = "SELECT A.idvale_hilo as vale, A.hilo, D.descripcion as nombre, A.fecha, A.turno,
    concat(trim(C.nombre),\" \",trim(C.apaterno),\" \",trim(C.amaterno)) as supervisor,
    SUM(B.Bobinas) as bobinas,
    SUM(B.kilos) as kilos,
    IF(A.estado=-1, \"PENDIENTE\", \"SURTIDO   \") as estado
  FROM vale_hilo A
    LEFT JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    LEFT JOIN usuarios C ON A.supervisor = C.num_emp
    LEFT JOIN articulo D ON A.hilo = D.hilo
    WHERE A.estado <> 0
  GROUP BY A.idvale_hilo  ";

  if ($resultado = $mysqli->query($consulta)) {
    $rawdata = array();
    //$rawdatapre = array();

    $i=0;
    while($rows = $resultado->fetch_array(MYSQLI_ASSOC)){
      //$rawdatapre[$i] = $rows;
      $rawdata[$i] = $rows;
      $i++;
    }

    //$rawdata['data'] = $rawdatapre ;

    $resultado->close();
    $mysqli->close();
    header('Content-Type: application/json');
    echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
  }
}

function reporte_vale_detallado() {
    include("bd.php") ;

    $fecha1 = explode("-",$_GET['fecha1']) ;
    $fecha2 = explode("-",$_GET['fecha2'])  ;
    $valida_fecha1 = $fecha1[0].$fecha1[1].$fecha1[2] ;
    $valida_fecha2 = $fecha2[0].$fecha2[1].$fecha2[2] ;

    $consulta = "SELECT A.fecha, B.hilo, B.descripcion as nombre , C.bobinas as bobinas, C.kilos as kgs,
    	IFNULL(D.descripcion,\" \") as destino, A.idvale_hilo as vale,
    	CONCAT(TRIM(E.nombre ),\" \",TRIM(E.apaterno)) as autoriza
    FROM vale_hilo A
    	INNER JOIN articulo B ON A.hilo = B.hilo
    	INNER JOIN vale_hilo_detalle C ON A.idvale_hilo = C.idvale_hilo
        LEFT JOIN origen D ON C.destino = D.idorigen
        LEFT JOIN usuarios E ON A.supervisor = E.num_emp
    WHERE A.estado = 1 AND (A.fecha >= " . $valida_fecha1 . " AND A.Fecha <= ". $valida_fecha2 ." )
    ORDER BY B.h_practico,A.fecha,A.idvale_hilo  " ;

    if ($resultado = $mysqli->query($consulta)) {
      $rawdata = array(); $i=0;
      while($rows = $resultado->fetch_array(MYSQLI_ASSOC)){
        $rawdata[$i] = $rows;
        $i++;
      }

      $resultado->close();
      $mysqli->close();
      header('Content-Type: text/html; charset=utf-8');
      echo "<html lang=\"es\">
        <head>
          <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
          <meta name=\"description\" content=\"Reporte Detallado de Vales\">
          <meta name=\"author\" content=\"JuCeBaRo\">
          <link rel=\"stylesheet\" type=\"text/css\" href=\"../css/bootstrap.css\">
        </head>
        <body>
          <div class=\"container\">
            <div class=\"row\">
              <div class=\"col-md-3\" >
                <img style=\"height: 100px; width: 220px; display: block;\"  src=\"../images/FABRICA MARÍA SIN FONDO_negro_corta.png\" />
              </div>
              <div class=\"col-md-9\" >
                <h3 class=\"mt-4\">Reporte Detallado de Vales del ".$fecha1[2]."/".$fecha1[1]."/".$fecha1[0]." al ".$fecha2[2]."/".$fecha2[1]."/".$fecha2[0]." </h3>
              </div>
            </div>
            <table class=\"table\">
            <thead class=\"thead-dark\">
              <tr>
                <th scope=\"col\">Fecha</th>
                <th scope=\"col\">Vale</th>
                <th scope=\"col\">Hilo</th>
                <th scope=\"col\"></th>
                <th scope=\"col\">Bobinas</th>
                <th scope=\"col\">Kilos</th>
                <th scope=\"col\">Destino</th>
                <th scope=\"col\">Autoriza</th>
              </tr>
            </thead>
            <tbody>";

        $totalkilos = 0 ;
        $totalbobinas = 0 ;
        $clavetitulo = 0.00 ;
        $tkgssub = 0 ;
        $tbobisub = 0;
        for($i=0; $i < count($rawdata); $i++){
          $totalkilos = $totalkilos + (float)$rawdata[$i]['kgs'] ;
          $totalbobinas = $totalbobinas + (int)$rawdata[$i]['bobinas'] ;

          if ($i===0){
            $clavetitulo = $rawdata[$i]['hilo'] ;

            $tkgssub = (float)$rawdata[$i]['kgs'] ;
            $tbobisub = (int)$rawdata[$i]['bobinas'] ;

          }else if($clavetitulo != $rawdata[$i]['hilo']){
            $clavetitulo = $rawdata[$i]['hilo'] ;

            echo "<tr>
              <th colspan=\"4\" >Subtotal :</th>
              <td><b>".number_format($tbobisub, 0, '.', ',')."</b></td>
              <td><b>".number_format($tkgssub, 2, '.', ',')."</b></td>
            </tr>";

            $tkgssub = (float)$rawdata[$i]['kgs'] ;
            $tbobisub = (int)$rawdata[$i]['bobinas'] ;

          }else if($i !=  count($rawdata)-1 ){
            $tkgssub = $tkgssub + (float)$rawdata[$i]['kgs'] ;
            $tbobisub = $tbobisub + (int)$rawdata[$i]['bobinas'] ;
          }

          echo "<tr>
                  <td>".date_format(date_create($rawdata[$i]['fecha']),'d/m/Y')."</td>
                  <td>".$rawdata[$i]['vale']."</td>
                  <td>".$rawdata[$i]['hilo']."</td>
                  <td>".$rawdata[$i]['nombre']."</td>
                  <td>".$rawdata[$i]['bobinas']."</td>
                  <td>".number_format($rawdata[$i]['kgs'], 2, '.', ',')."</td>
                  <td>".$rawdata[$i]['destino']."</td>
                  <td>".$rawdata[$i]['autoriza']."</td>
                </tr>";
          if ($i ===  count($rawdata)-1 ){
            echo "<tr>
              <th colspan=\"4\" >Subtotal :</th>
              <td><b>".number_format($tbobisub , 0, '.', ',')."</b></td>
              <td><b>".number_format($tkgssub , 2, '.', ',')."</b></td>
            </tr>";
          }
        }

      echo "
              <tr>
                <th colspan=\"4\" >Totales :</th>
                <td><b>".number_format($totalbobinas, 0, '.', ',')."</b></td>
                <td><b>".number_format($totalkilos, 2, '.', ',')."</b></td>
              </tr>

            </tbody>
            </table>
      </div>
      </body>
      </html>";

    }else{
      $mysqli->close();
      echo "No se pudo obtener la infomacion deseada";
    }
}


////////////// Para validar la informacion de el Vale y asi poder guardarlo //////////////
function validar_vale() {
  $validaciones = [];

  // Reviso si el GET tiene algo
  if(!empty($_GET)){
    if(empty($_GET['turno'])){
        $validaciones['turno'] = 'El campo turno es requerido';
    }

    if(empty($_GET['idsupervisor'])){
        $validaciones['idsupervisor'] = 'El campo idsupervisor es requerido' ;
    }

    if(empty($_GET['supervisor'])){
        $validaciones['supervisor'] = 'El campo supervisor es requerido' ;
    }

    if (count($_GET['detalle']) === 0){
      $validaciones['detalle'] = 'debe Llenar los Campos Necesarios para Continuar' ;
    }else{
      foreach($_GET['detalle'] as $item){
          foreach($item as $key => $value){
            //echo $key; // Nombre de la variable(nom, des, rut, etc)
            //echo $value; // Su valor
            if(empty ($value)){
              $validaciones['detalle'] = $key.' Tiene un Valor vacio,debe Llenar los Campos Necesarios para Continuar' ;
              break 2;
            }
          }
      }
    }

    if (isset($_GET['detallecomprado'])){
      if (count($_GET['detallecomprado']) === 0 && trim ($_GET['tipo']) === "COMPRADO" ){
          $validaciones['detallecomprado'] = 'debe Selecionar al Menos una Entrada' ;
      }
    }

    if (count($validaciones) === 0){
      $validaciones['id_vale'] = guardar_vale() ;
    }else{
      $validaciones['id_vale'] = 0 ;

    }

    //header('Content-Type: application/json');
    echo json_encode([
        'response' => count($validaciones) === 0,
        'errors'   => $validaciones
    ],JSON_UNESCAPED_UNICODE);

  }
}

function guardar_vale() {
  if(!empty($_GET)){
    include("bd.php") ;

    $date = str_replace('/', '-',$_GET['fecha']) ;
    $newDate = date("Y-m-d", strtotime($date)) ;
    // Creo un Nuevo registro del vale
    $consulta = "INSERT INTO vale_hilo(fecha,supervisor,hilo,turno)".
    "VALUES(\"" .$newDate. "\"," .$_GET['idsupervisor']. "," .$_GET['clave_hilo']. "," .$_GET['turno']. ")" ;

    if ($resultado = $mysqli->query($consulta)) {
      // Consigo el ultimo ID insertado
      $ultimo_idvale = $mysqli->insert_id ;
      // anexo la lista de Id_Entradas que Saldran en caso de que el Hilo sea Producido
      if (trim ($_GET['tipo']) === "COMPRADO"){
        $lista_entradas = $_GET['detallecomprado'] ;

        foreach ($lista_entradas as $row) {
          $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada,presenta_cant,Bobinas,presenta,kilos) "
            ."VALUES(" .$ultimo_idvale. "," .$row['id']. "," .$row['cantidadP']. "," .$row['cantidadB'].
            "," .(trim($row['Presenta'])==="BOLSA"?2:(trim($row['Presenta'])==="CAJA"?3:(trim($row['Presenta'])==="PALET"?4:(trim($row['Presenta'])==="TARIMA"?1:5)))).
            "," . $row['cantidadK'] . ")" ;

          $mysqli->query($consulta3) ;
        }
      }else{
          $lista_entradas = $_GET['id_ent'] ;

          foreach ($_GET['id_ent'] as $row) {
            $detalle_entrada = "SELECT * FROM entradash WHERE identradash = ".$row['id'] ;
            $resultado = $mysqli->query($detalle_entrada) ;

            while($row2 = $resultado->fetch_assoc()){
              $idpresenta_ = $row2['id_presenta'] ;
              $numero_ = $row2['numero'] ;
              $bobinas_ = $row2['bobinas'] ;
              $kilos_ = $row2['pesoneto'] ;
            }

            $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada,presenta_cant,Bobinas,presenta,kilos)"
              . "VALUES(" .$ultimo_idvale. "," .$row['id']. "," .$numero_. "," .$bobinas_.","
              . $idpresenta_ . "," .$kilos_. ")" ;

            $mysqli->query($consulta3) ;
          }

      }
      // Lleno la informacion de detalle
      $lista_detalle = $_GET['detalle'] ;
      for($i=0; $i < count($lista_detalle); $i++){
        $consulta4 = "INSERT INTO vale_hilo_detalle(Bobinas,kilos,destino,tela,idvale_hilo)"
          ." VALUES(".$lista_detalle[$i]['bobinas'].",".$lista_detalle[$i]['kgs'].","
          .$lista_detalle[$i]['destino'].",\"".$lista_detalle[$i]['tela']."\",".$ultimo_idvale.")" ;

        $mysqli->query($consulta4) ;
      }
    }
    $mysqli->close();
    return strval($ultimo_idvale) ;
  }
}
//    ****************    ----------------------------    ***************************   //

function cancelar_vale() {
  include("bd.php") ;

  $consulta = "SELECT * FROM vale_hilo WHERE idvale_hilo = ". $_GET['id_vale'];
  $resultado = $mysqli->query($consulta) ;

  while($row = $resultado->fetch_assoc()){
    $estado_vale = $row['estado'] ;
  }

  if ($estado_vale === "-1" ){
    $consulta = "UPDATE vale_hilo SET estado = 0 WHERE idvale_hilo = ". $_GET['id_vale'];
    if ($resultado = $mysqli->query($consulta)) {
      $mysqli->close();
      header('content-type text/plain');
      echo "Se Cancelo el Vale";
    }else{
      $mysqli->close();
      header('content-type text/plain');
      echo "NO SE PUDO CANCELAR EL VALE";
    }
  }else{
    $mysqli->close();
    header('content-type text/plain');
    echo "NO SE PUDO CANCELAR EL VALE, Ya fue Verificado/Cancelado.".$estado_vale ;
  }
}

function ver_vales() {
  require '../vendor/autoload.php' ;

  $content = '';

  $content .= '
  <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">

  <div id="vale" style="width: 750px; margin: 0 auto; margin-top: 15px; border-style: dashed; border-color: black; ">
    <div class="row">
      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4" >
        <img style="height: 100px; width: 220px; display: block;"  src="../images/FABRICA MARÍA SIN FONDO_negro_corta.png" />
      </div>
      <div class="col-xl-8 col-lg-8 col-md-8 col-sm-8" >
        <h3 style="margin-top:35px; margin-left:18%;">Vale de Hilo N° 1</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
        <p>Fecha: <strong></strong></p>
        <p>Hilo: <strong></strong></p>
        <p>Bobinas: <strong></strong></p>
      </div>
      <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" >
        <p>Supervisor: <strong></strong></p>
        <p><strong></strong></p>
        <p>Nº Tarima: <strong></strong></p>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
        <p>Turno: <strong></strong></p>
        <p><p><strong></strong></p></p>
        <p>Kilos: <strong></strong></p>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
          <p>Bolsas: <strong></strong></p>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
          <p>Cajas: <strong></strong></p>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
          <p>Palet: <strong></strong></p>
      </div>
      <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3" >
          <p>Tarimas: <strong></strong></p>
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
          </tbody>
        </table>
      </div>
    </div>

  </div>
  ';

  echo $content ;

}

if(isset($_GET['function']) && !empty($_GET['function'])){
    $function = $_GET['function'];
    switch($function) {
        case 'lista_vale' : lista_vale(); break;
        case 'validar_vale' : validar_vale(); break;
        case 'cancelar_vale' : cancelar_vale(); break;
        case 'ver_vales' : ver_vales(); break;
        case 'reporte_vale_detallado' : reporte_vale_detallado(); break;
    }
}
?>
