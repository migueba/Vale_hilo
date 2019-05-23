<?php
function lista_vale() {
  include("bd.php") ;

  $consulta = "SELECT A.idvale_hilo as vale, A.hilo, A.fecha, A.turno,
    concat(trim(C.nombre),\" \",trim(C.apaterno),\" \",trim(C.amaterno)) as supervisor,
    SUM(B.Bobinas) as bobinas,
    SUM(B.kilos) as kilos,
    IF(A.estado=-1, \"PENDIENTE\", \"SURTIDO   \") as estado
  FROM vale_hilo A
    LEFT JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    LEFT JOIN usuarios C ON A.supervisor = C.num_emp
    WHERE A.estado <> 0
  GROUP BY A.idvale_hilo  ";

  if ($resultado = $mysqli->query($consulta)) {
    $rawdata = array(); //creamos un array
    //guardamos en un array multidimensional todos los datos de la consulta
    $i=0;
    while($row = mysqli_fetch_array($resultado)){
      $rawdata[$i] = $row;
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

    if (count($_GET['detallecomprado']) === 0){
        $validaciones['detallecomprado'] = 'debe Selecionar al Menos una Entrada' ;
    }

    if (count($validaciones) === 0){
      $validaciones['id_vale'] = guardar_vale() ;
    }

    header('Content-Type: application/json');
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
      $lista_entradas = $_GET['detallecomprado'] ;
      for($i=0; $i < count($lista_entradas); $i++){
        $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada,presenta_cant,Bobinas,presenta,kilos)"
          ."VALUES(" .$ultimo_idvale. "," .$lista_entradas[$i]['id']. "," .$lista_entradas[$i]['cantidadP']. "," .$lista_entradas[$i]['cantidadB'].
          "," .(trim($lista_entradas[$i]['Presenta'])==="BOLSA"?2:(trim($lista_entradas[$i]['Presenta'])==="CAJA"?3:(trim($lista_entradas[$i]['Presenta'])==="PALET"?4:(trim($lista_entradas[$i]['Presenta'])==="TARIMA"?1:5)))).
          "," .$lista_entradas[$i]['cantidadK']. ")" ;

        $mysqli->query($consulta3) ;
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

function inf_vale() {

}

if(isset($_GET['function']) && !empty($_GET['function'])){
    $function = $_GET['function'];
    switch($function) {
        case 'lista_vale' : lista_vale(); break;
        case 'validar_vale' : validar_vale(); break;
    }
}
?>
