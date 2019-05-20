<?php

  // Obtiene el Nombre de un Usuario teniendo solo su ID
  function busca_id() {
    include("bd.php") ;

    $consulta = "SELECT UPPER(CONCAT(TRIM(A.nombre),' ',TRIM(A.apaterno),' ',TRIM(A.amaterno))) as nombre
    FROM usuarios A
    	 WHERE A.num_emp = ". $_GET['idemp'] ."  LIMIT 1";

    if($resultado = $mysqli->query($consulta)) {
      $fila = $resultado->fetch_row() ;
      $data = array();

      $data['nombre'] = $fila[0] ;

      $resultado->close();
      $mysqli->close();

      header('Content-Type: application/json');
      echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
    }
  }

  // Obtiene La lista de los usuarios con una conincidencia de letras
  function lista_usuarios() {
    include("bd.php") ;

    $consulta = " SELECT S.num_emp, S.nombre
      FROM
      (SELECT A.num_emp, UPPER(CONCAT(TRIM(A.nombre),' ',TRIM(A.apaterno),' ',TRIM(A.amaterno))) as nombre
        FROM usuarios A) as S
      WHERE S.nombre LIKE '%" . strtoupper($_GET['term']) . "%'
      ORDER BY S.nombre";

    if ($resultado = $mysqli->query($consulta)) {
      $HilosData = array();
      if($resultado->num_rows > 0){
        while($row = $resultado->fetch_assoc()){
          $data['label'] = $row['nombre'];
          $data['value'] = $row['num_emp'];
          array_push($HilosData, $data);
        }
      }
      $resultado->close();
      $mysqli->close();
      header('Content-Type: application/json');
      echo json_encode($HilosData,JSON_UNESCAPED_UNICODE);
    }else{
      $mysqli->close();
      echo "Fallo la Conexion a la Base de Datos";
    }
  }

  if(isset($_GET['function']) && !empty($_GET['function'])){
      $function = $_GET['function'];
      switch($function) {
          case 'busca_id' : busca_id(); break;
          case 'lista_usuarios' : lista_usuarios(); break;
          // ...etc...
      }
  }
?>
