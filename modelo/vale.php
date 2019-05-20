<?php
function lista_vale() {
  include("bd.php") ;

  $consulta = "SELECT A.idvale_hilo as vale, A.hilo, A.fecha, A.turno,
    C.nombre+\" \"+C.apaterno+\" \"+C.amaterno as supervisor,
    SUM(B.Bobinas) as bobinas,
    SUM(B.kilos) as kilos,
    IF(A.estado=-1, \"PENDIENTE\", \"SURTIDO   \") as estado
  FROM vale_hilo A
    LEFT JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    LEFT JOIN usuarios C ON A.supervisor = C.num_emp
    WHERE A.estado <> 0
  GROUP BY A.idvale_hilo  ";
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

    if (count($_GET['id_ent']) === 0){
        $validaciones['id_ent'] = 'debe Selecionar al Menos una Entrada' ;
    }

    if (count($validaciones) === 0){
      $validaciones['id_vale'] = guardar_vale() ;
    }

    echo json_encode([
        'response' => count($validaciones) === 0,
        'errors'   => $validaciones
    ]);

  }
}

function guardar_vale() {
  if(!empty($_POST)){
    include("bd.php") ;

    $date = str_replace('/', '-',$_GET['fecha']) ;
    $newDate = date("Y-m-d", strtotime($date)) ;
    // Creo un Nuevo registro del vale
    $consulta = "INSERT INTO vale_hilo(fecha,supervisor,hilo,turno)
    VALUES(\"".$newDate."\",".$_POST['idsupervisor'].",".$_POST['clave_hilo'].",".$_POST['turno'].")" ;

    if ($resultado = $mysqli->query($consulta)) {
      // Consigo el ultimo ID insertado
      $ultimo_idvale = mysql_insert_id() ;
      // anexo la lista de Id_Entradas que Saldran en caso de que el Hilo sea Producido
      $lista_entradas = $_POST['id_ent'] ;
      for($i=0; $i < count($lista_entradas); $i++){
        $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada)"
          ."VALUES(". $ultimo_idvale[0] .",".$lista_entradas[$i].")" ;
        $mysqli->query($consulta3) ;
      }
      // Lleno la informacion de detalle
      $lista_detalle = $_POST['detalle'] ;
      for($i=0; $i < count($lista_detalle); $i++){
        $consulta4 = "INSERT INTO vale_hilo_detalle(Bobinas,kilos,destino,tela,idvale_hilo,presenta_cant,presenta)"
          ." VALUES(".$lista_detalle[$i]['bobinas'].",".$lista_detalle[$i]['kgs'].","
          .$lista_detalle[$i]['destino'].",\"".$lista_detalle[$i]['tela']."\",".$ultimo_idvale[0].","
          .$lista_detalle[$i]['cantidad'].",".$lista_detalle[$i]['presenta'].")" ;

        $mysqli->query($consulta4) ;
      }
    }
    $mysqli->close();
    return strval($ultimo_idvale) ;
  }
}
//    ****************    ----------------------------    ***************************   //

if(isset($_GET['function']) && !empty($_GET['function'])){
    $function = $_GET['function'];
    switch($function) {
        case 'lista_vale' : lista_vale();break;
    }
}
?>
