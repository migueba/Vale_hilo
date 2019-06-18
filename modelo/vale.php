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
        for($i=0; $i < count($lista_entradas); $i++){
          $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada,presenta_cant,Bobinas,presenta,kilos)"
            ."VALUES(" .$ultimo_idvale. "," .$lista_entradas[$i]['id']. "," .$lista_entradas[$i]['cantidadP']. "," .$lista_entradas[$i]['cantidadB'].
            "," .(trim($lista_entradas[$i]['Presenta'])==="BOLSA"?2:(trim($lista_entradas[$i]['Presenta'])==="CAJA"?3:(trim($lista_entradas[$i]['Presenta'])==="PALET"?4:(trim($lista_entradas[$i]['Presenta'])==="TARIMA"?1:5)))).
            "," .$lista_entradas[$i]['cantidadK']. ")" ;

          $mysqli->query($consulta3) ;
        }
      }else{
          $lista_entradas = $_GET['id_ent'] ;
          for($i=0; $i < count($lista_entradas); $i++){
            $detalle_entrada = "SELECT * FROM entradash WHERE identradash = ".$lista_entradas[$i]['id']  ;
            $resultado = $mysqli->query($detalle_entrada) ;

            while($row = $resultado->fetch_assoc()){
              $idpresenta_ = $row['id_presenta'] ;
              $numero_ = $row['numero'] ;
              $bobinas_ = $row['bobinas'] ;
              $kilos_ = $row['pesoneto'] ;
            }
            $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada,presenta_cant,Bobinas,presenta,kilos)"
              . "VALUES(" .$ultimo_idvale. "," .$lista_entradas[$i]['id']. "," .$numero_. "," .$bobinas_.","
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
    }
}
?>
