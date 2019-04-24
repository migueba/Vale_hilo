<?php
  include("bd.php") ;

  if($_POST['tipo'] === "COMPRADO"){
    $consulta = "SELECT A.hilo as clave, \"NORMAL\" AS entrada,
      SUM(A.exisbolsa) as lote,
      SUM(A.exiscaja) as tarima,
      SUM(A.exispalet) as presentacion,
      SUM(A.exis_bllena) as pesoneto,
      SUM(A.exis_clleno+A.exis_bcono+A.exis_ccono+A.exis_cono) as bobinas ,
    	A.hilo as id
    FROM existencia A
      WHERE A.hilo = ". $_POST['idhilo'] ." group by A.hilo ";
  }else{
  $consulta = "SELECT A.clave, IF(A.oriextra=0,\"NORMAL   \",\"DEVOLUCION\") AS entrada, A.lote,
    IF(A.tarima<>0,A.tarima,IF(A.bolsa<>0,A.bolsa,IF(A.caja<>0,A.caja,IF(A.palet<>0,A.palet,0)))) as tarima,
    A.pesoneto, (A.bobinas+A.Cbobina+A.Pbobina) as bobinas ,
    IF(A.tarima<>0,\"TARIMA  \",IF(A.bolsa<>0,\"BOLSA  \",IF(A.caja<>0,\"CAJA  \",IF(A.palet<>0,\"PALET  \",\"N/D.  \")))) as presentacion,
  	IF(A.tipo=1,\"BOBINA LLENA  \",\"BOBINA GALLO  \") AS tipo, A.id_ent as id
  FROM entradash A
    LEFT JOIN vale_entrada B ON A.id_ent = B.id_entrada
    WHERE A.clave = ". $_POST['idhilo'] ." AND A.estatus = ( 1 ) AND B.id_entrada is null
    ORDER BY A.clave, A.oriextra";
  }

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
    echo json_encode($rawdata,JSON_UNESCAPED_UNICODE);
  }else{
    $mysqli->close();
    echo "Fallo la Conexion a la Base de Datos";
  }

?>
