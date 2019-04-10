<?php
  $validaciones = [];

  function is_date($value) {
    $value = explode('/', $value);

    if(count($value) !== 3) return false;

    return @checkdate ( $value[1] , $value[0] , $value[2] );
  }

  if(!empty($_POST)){
    if(empty($_POST['turno'])){
        $validaciones['turno'] = 'El campo turno es requerido';
    }

    // dd/mm/yyyy
    if(!empty($_POST['fecha'])){
        if(!is_date($_POST['fecha'])) {
            $validaciones['fecha'] = 'El campo fecha requiere una fecha válida dd/mm/yyyy';
        }
    }

    if(empty($_POST['idsupervisor'])){
        $validaciones['idsupervisor'] = 'El campo idsupervisor es requerido';
    }

    if(empty($_POST['supervisor'])){
        $validaciones['supervisor'] = 'El campo supervisor es requerido';
    }

    if (count($_POST['id_ent']) === 0){
        $validaciones['id_ent'] = 'debe Selecionar al Menos una Entrada';
    }

    if (count($_POST['id_ent']) === 0){
        $validaciones['id_ent'] = 'debe Selecionar al Menos una Entrada';
    }

    if (count($_POST['detalle']) === 0){
        $validaciones['detalle'] = 'debe definir al menos un detalle';
    }

    if (count($validaciones) === 0){
      guardar_info() ;
    }

    echo json_encode([
        'response' => count($validaciones) === 0,
        'errors'   => $validaciones
    ]);
  }

  function guardar_info() {
    if(!empty($_POST)){
      // Database Structure
      $host="localhost";
      $username="root";
      $password="";
      $databasename="urdido_engomado";

      $mysqli = new mysqli($host,$username,$password,$databasename);
      $mysqli->set_charset("utf8");
    }else{
      alert("Esta Vacio el POST");
    }

  }

?>
