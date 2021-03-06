<?php
  $validaciones = [];

  //function is_date($value) {
  //  $value = explode('/', $value);

  //  if(count($value) !== 3) return false;
  //  return @checkdate ( $value[1] , $value[0] , $value[2] );
  //}

  if(!empty($_POST)){
    if(empty($_POST['turno'])){
        $validaciones['turno'] = 'El campo turno es requerido';
    }

    if(empty($_POST['idsupervisor'])){
        $validaciones['idsupervisor'] = 'El campo idsupervisor es requerido' ;
    }

    if(empty($_POST['supervisor'])){
        $validaciones['supervisor'] = 'El campo supervisor es requerido' ;
    }

    if (count($_POST['detalle']) === 0){
      $validaciones['detalle'] = 'debe Llenar los Campos Necesarios para Continuar' ;
    }else{
      foreach($_POST['detalle'] as $item){
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

    if (count($_POST['id_ent']) === 0){
        $validaciones['id_ent'] = 'debe Selecionar al Menos una Entrada' ;
    }

    if (count($validaciones) === 0){
      $validaciones['id_vale'] = guardar_info() ;
    }

    echo json_encode([
        'response' => count($validaciones) === 0,
        'errors'   => $validaciones
    ]);

  }

  function guardar_info() {
    if(!empty($_POST)){
      include("bd.php") ;

      $date = str_replace('/', '-',$_POST['fecha']) ;
      $newDate = date("Y-m-d", strtotime($date)) ;
      // Creo un Nuevo registro del vale
      $consulta = "INSERT INTO vale_hilo(fecha,supervisor,clave_hilo,turno)
      VALUES(\"".$newDate."\",".$_POST['idsupervisor'].",".$_POST['clave_hilo'].",".$_POST['turno'].")" ;

      if ($resultado = $mysqli->query($consulta)) {
        $consulta2 = "SELECT idvale_hilo FROM vale_hilo ORDER BY idvale_hilo DESC LIMIT 1;" ;
        // Consigo el ultimo ID insertado
        if ($resultado2 = $mysqli->query($consulta2)){
          $ultimo_idvale = $resultado2->fetch_row() ;
          $resultado2->close();

          // anexo la lista de Id_Entradas que Saldran en caso de que el Hilo sea Producido
          if(trim($_POST['tipo']) === "PRODUCIDO"){
            $lista_entradas = $_POST['id_ent'] ;
            for($i=0; $i < count($lista_entradas); $i++){
              $consulta3 = "INSERT INTO vale_entrada(idvale,id_entrada)"
                ."VALUES(". $ultimo_idvale[0] .",".$lista_entradas[$i].")" ;
              $mysqli->query($consulta3) ;
            }
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
      }else{
        //$validaciones['guardadoerror'] = 'Ocurrio un Error al guardar la informacion' ;
      }
    }else{
      //$validaciones['guardadoerror'] = 'Ocurrio un Error al guardar la informacion' ;
    }

    $mysqli->close();

    return strval($ultimo_idvale[0]) ;
  }

?>
