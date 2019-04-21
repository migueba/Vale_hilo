<?php
  include("bd.php") ;

  $consulta = "SELECT UPPER(A.descripcion) as descripcion, A.hilo
    FROM existencia A
    WHERE UPPER(A.descripcion) LIKE '%" . strtoupper($_GET['term']) . "%'
    ORDER BY A.descripcion";

  if ($resultado = $mysqli->query($consulta)) {
    $HilosData = array();
    if($resultado->num_rows > 0){
      while($row = $resultado->fetch_assoc()){
        $data['label'] = $row['descripcion'];
        $data['value'] = $row['hilo'];

        array_push($HilosData, $data);
      }
    }

    $resultado->close();
    $mysqli->close();
    echo json_encode($HilosData,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }
?>
