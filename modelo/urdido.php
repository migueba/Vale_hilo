<?php
  require '../vendor/autoload.php' ;

  use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;

  function ultima_orden() {
    include_once("bd.php") ;

    $consulta = "SELECT A.orden FROM urdido_engomado.urdido A order by A.hora desc limit 1";
    $ultima_orden = 0 ;

    if ($resultado = $mysqli->query($consulta)) {
      while($row = $resultado->fetch_assoc()){
        $ultima_orden = $row['orden'] ;
      }
      $resultado->close();
    }
    $mysqli->close();
    header('content-type text/plain') ;
    echo $ultima_orden ;
  }

  function lista_urdido() {
    include_once("bd.php") ;

    $consulta = "SELECT A.hilo, C.descripcion, SUM(B.julios) as julios, B.numeros, A.orden, A.fecha, A.horas, sum(B.roturas) as roturas,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
    GROUP BY B.id_urdido, B.numeros, B.julios
    ORDER BY A.hora DESC";

    if ($resultado = $mysqli->query($consulta)) {
      $rawdata = array();

      $i=0;
      while($rows = $resultado->fetch_array(MYSQLI_ASSOC)){
        //$rawdatapre[$i] = $rows;
        $rawdata[$i] = $rows;
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

  function guarda_urdido() {
      include_once("bd.php") ;
      /* PAra Obtener el ID del Empleado */
      $id_oficial_consulta = "SELECT * FROM usuarios WHERE num_emp = ".$_GET['n_oficial'] ;
      $resultado = $mysqli->query($id_oficial_consulta) ;

      while($row2 = $resultado->fetch_assoc()){
        $id_oficial_ = $row2['idusuario'] ;
      }

      $date = str_replace('/', '-',$_GET['fecha']) ;
      $newDate = date("Y-m-d", strtotime($date)) ;

      $consulta = "INSERT INTO urdido(tela, urdido_usuario, horas, hilo, orden, fecha)".
        "VALUES(\"" .$_GET['tela']. "\"," .$id_oficial_. "," .$_GET['horas']. "," .$_GET['clave_hilo']. "," .$_GET['orden']. ",\"" .$newDate. "\")" ;

      if ($resultado = $mysqli->query($consulta)) {
        $ultimo_id = $mysqli->insert_id ;
        $i = 1;
        foreach ( $_GET['detalle'] as $row) {
          $consulta = "INSERT INTO urdido_detalle(numeros, bobinas, julios, id_urdido, roturas)"
            . "VALUES(" .$row['numero']. "," .$row['bobina']. "," . ( $i==(count($_GET['detalle'])) ? 1-($i-$_GET['julios']) : 1) . "," .$ultimo_id. "," .$row['rotura']. ")" ;
          $mysqli->query($consulta) ;
          $i++;
        }

        $mysqli->close();
        header('content-type text/plain');
        echo "Se Guardo";
      }else {
        $mysqli->close();
        header('content-type text/plain');
        echo "No se pudo Almacenar";
      }
  }

  function genera_excel() {
    include_once("bd.php") ;

    $date1 = str_replace('/', '-', $_GET['fecha1'] ) ;
    $newDate1 = date("Y/m/d", strtotime($date1)) ;

    $date2 = str_replace('/', '-', $_GET['fecha2'] ) ;
    $newDate2 = date("Y/m/d", strtotime($date2)) ;

    $consulta = "SELECT A.hilo, C.descripcion, SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden, A.fecha, A.horas, sum(B.roturas) as roturas,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial, C.generico, A.horas
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY A.fecha, A.urdido_usuario
    ORDER BY A.fecha,A.urdido_usuario DESC" ;

    $documento = new Spreadsheet() ;
    $documento
        ->getProperties()
        ->setCreator("Aquí va el creador, como cadena")
        ->setLastModifiedBy('juCeBaRo') // última vez modificado por
        ->setTitle('Reporte de Urdido')
        ->setSubject('Reporte')
        ->setDescription('Este documento fue generado para obtener la informacion de Urdido')
        ->setKeywords('Urdido')
        ->setCategory('Urdido');

    $sheet = $documento->getActiveSheet() ;

    $sheet->getColumnDimension('A')->setWidth(32) ;
    $sheet->getColumnDimension('B')->setWidth(16) ;
    $sheet->getColumnDimension('C')->setWidth(13) ;
    $sheet->getColumnDimension('D')->setWidth(15) ;
    $sheet->getColumnDimension('E')->setWidth(12) ;
    $sheet->getColumnDimension('F')->setWidth(10) ;
    $sheet->getColumnDimension('G')->setWidth(11) ;
    $sheet->getColumnDimension('K')->setWidth(21) ;
    $sheet->getColumnDimension('O')->setWidth(19) ;

    /////////////// Encabezados URDIDORES ///////////////
    $sheet->getStyle('2')->getFont()->setBold(true) ;
    $sheet->getStyle('A2:E2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $sheet->getStyle('A2:E2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
    $sheet->setTitle("Urdido")
          ->setCellValueByColumnAndRow(1, 2, "Oficial")
          ->setCellValueByColumnAndRow(2, 2, "Numeros Urdidos Totales")
          ->setCellValueByColumnAndRow(3, 2, "Roturas Totales")
          ->setCellValueByColumnAndRow(4, 2, "Fecha")
          ->setCellValueByColumnAndRow(5, 2, "Horas") ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        $sheet->setCellValueByColumnAndRow(1, $i, $row['oficial'] ) ;
        $sheet->setCellValueByColumnAndRow(2, $i, $row['numeros'] ) ;
        $sheet->setCellValueByColumnAndRow(3, $i, $row['roturas'] ) ;
        $date = str_replace('-', '/', $row['fecha'] ) ;
        $newDate = date("d/m/Y", strtotime($date)) ;
        $sheet->setCellValueByColumnAndRow(4, $i, $newDate)  ;
        $sheet->setCellValueByColumnAndRow(5, $i, $row['horas'] )  ;

        $i++;
      }
      $resultado->close();
    }

    // **************** FINAL **************** //
    $consulta = "SELECT DISTINCT A.urdido_usuario, CONCAT(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
      FROM urdido_engomado.urdido A
      LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
      WHERE A.fecha >= \"" .$newDate1. "\" AND A.fecha <= \"" .$newDate2 . "\"" ;

    if( $resultado2 = $mysqli->query($consulta) ) {
      $ii = $i ;

      $sheet->getStyle('A'.($i+2).':F'.($i+2) )->getFont()->setBold(true) ;
      $sheet->getStyle('A'.($i+2).':F'.($i+2) )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
      $sheet->getStyle('A'.($i+2).':F'.($i+2) )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
      $sheet->setCellValueByColumnAndRow( 1, $i+2, "OFICIAL" ) ;
      $sheet->setCellValueByColumnAndRow( 2, $i+2, "NUMEROS" ) ;
      $sheet->setCellValueByColumnAndRow( 3, $i+2, "HORAS" ) ;
      $sheet->setCellValueByColumnAndRow( 4, $i+2, "NUMEROS X HORA" ) ;
      $sheet->setCellValueByColumnAndRow( 5, $i+2, "NUMEROS %" ) ;
      $sheet->setCellValueByColumnAndRow( 6, $i+2, "HORAS %" ) ;

      while( $row = $resultado2->fetch_assoc() ){
        $sheet->setCellValueByColumnAndRow( 1, $i+3, $row['oficial'] )  ;
        $sheet->getStyle('C'.($i+3).':D'.($i+3) )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00) ;
        $sheet->setCellValueByColumnAndRow( 2, $i+3, "=SUMIF(A3:A" .$ii. ",\"" .$row['oficial']. "\",B3:B".$ii.")" )  ;
        $sheet->setCellValueByColumnAndRow( 3, $i+3, "=SUMIF(A3:A" .$ii. ",\"" .$row['oficial']. "\",E3:E".$ii.")" )  ;
        $sheet->setCellValueByColumnAndRow( 4, $i+3, "=B".($i+3)." / C".($i+3) )  ;
        $sheet->getStyle('E'.($i+3).':F'.($i+3) )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00) ;
        $sheet->setCellValueByColumnAndRow( 5, $i+3, "=B".($i+3)." / B".($ii+4+count($row)) )  ;
        $sheet->setCellValueByColumnAndRow( 6, $i+3, "=C".($i+3)." / C".($ii+4+count($row)) )  ;
        $i++;
      }

      $sheet->getStyle('A'.($i+3).':F'.($i+3) )->getFont()->setBold(true) ;
      $sheet->setCellValueByColumnAndRow( 1, $i+3, "TOTAL: " )  ;
      $sheet->setCellValueByColumnAndRow( 2, $i+3, "=SUM(B" .($ii+3). ":B" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 3, $i+3, "=SUM(C" .($ii+3). ":C" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 4, $i+3, "=SUM(D" .($ii+3). ":D" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 5, $i+3, "=SUM(E" .($ii+3). ":E" .($i+2). ")*100" )  ;
      $sheet->setCellValueByColumnAndRow( 6, $i+3, "=SUM(F" .($ii+3). ":F" .($i+2). ")*100" )  ;
    }






    /////////////// Encabezados TELAS ///////////////
    $consulta = "SELECT A.hilo, C.descripcion, SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden, A.fecha, A.horas, sum(B.roturas) as roturas,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial, C.generico, C.h_practico as titulo, A.horas
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY A.fecha, A.tela
    ORDER BY A.fecha DESC" ;

    $sheet->getStyle('I2:O2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $sheet->getStyle('I2:O2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
    $sheet->setCellValueByColumnAndRow(9, 2, "TELA")
          ->setCellValueByColumnAndRow(10, 2, "NUMEROS URDIDOS")
          ->setCellValueByColumnAndRow(11, 2, "Numeros Por Kilometro")
          ->setCellValueByColumnAndRow(12, 2, "TITULO DE HILO")
          ->setCellValueByColumnAndRow(13, 2, "TITULO GENERICO")
          ->setCellValueByColumnAndRow(14, 2, "ROTURAS TOTALES")
          ->setCellValueByColumnAndRow(15, 2, "PAROS POR KILOMETRO") ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        $sheet->setCellValueByColumnAndRow(9, $i, $row['tela'] ) ;
        $sheet->setCellValueByColumnAndRow(10, $i, $row['numeros'] ) ;
        $sheet->setCellValueByColumnAndRow(11, $i, "= J".$i."/100" ) ;
        $sheet->setCellValueByColumnAndRow(12, $i, $row['titulo'] ) ;
        $sheet->setCellValueByColumnAndRow(13, $i, is_numeric( trim($row['generico']) ) ? trim($row['generico']).' ALG' : trim($row['generico']) ) ;
        $sheet->setCellValueByColumnAndRow(14, $i, $row['roturas'] ) ;
        $sheet->setCellValueByColumnAndRow(15, $i, "=N".$i."/K".$i)  ;

        $i++;
      }
      $resultado->close();
    }

    $consulta = "SELECT DISTINCT A.tela FROM urdido_engomado.urdido A WHERE A.fecha >= \"" .$newDate1. "\" AND A.fecha <= \"" .$newDate2 . "\"" ;

    if( $resultado2 = $mysqli->query($consulta) ) {
      $ii = $i ;

      $sheet->getStyle('I'.($i+2).':M'.($i+2) )->getFont()->setBold(true) ;
      $sheet->getStyle('I'.($i+2).':M'.($i+2) )->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
      $sheet->getStyle('I'.($i+2).':M'.($i+2) )->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
      $sheet->setCellValueByColumnAndRow(9, $i+2, "TELA" ) ;
      $sheet->setCellValueByColumnAndRow(10, $i+2, "NUMEROS" ) ;
      $sheet->setCellValueByColumnAndRow(11, $i+2, "NUMEROS X KILOMETRO" ) ;
      $sheet->setCellValueByColumnAndRow(12, $i+2, "ROTURAS" ) ;
      $sheet->setCellValueByColumnAndRow(13, $i+2, "PAROS POR KILOMETRO" ) ;

      while( $row = $resultado2->fetch_assoc() ){
        $sheet->setCellValueByColumnAndRow( 9, $i+3, $row['tela'] )  ;
        $sheet->setCellValueByColumnAndRow( 10, $i+3, "=SUMIF(I3:I" .$ii. ",\"" .$row['tela']. "\",J3:J".$ii.")" )  ;
        $sheet->setCellValueByColumnAndRow( 11, $i+3, "=SUMIF(I3:I" .$ii. ",\"" .$row['tela']. "\",K3:K".$ii.")" )  ;
        $sheet->setCellValueByColumnAndRow( 12, $i+3, "=SUMIF(I3:I" .$ii. ",\"" .$row['tela']. "\",N3:N".$ii.")" )  ;

        $sheet->setCellValueByColumnAndRow( 13, $i+3, "=L".($i+3)."/K".($i+3) )  ;
        $i++;
      }
      $sheet->getStyle('I'.($i+3).':M'.($i+3) )->getFont()->setBold(true) ;
      $sheet->setCellValueByColumnAndRow( 9, $i+3, "TOTAL: " )  ;
      $sheet->setCellValueByColumnAndRow( 10, $i+3, "=SUM(J" .($ii+3). ":J" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 11, $i+3, "=SUM(K" .($ii+3). ":K" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 12, $i+3, "=SUM(L" .($ii+3). ":L" .($i+2). ")" )  ;
      $sheet->setCellValueByColumnAndRow( 13, $i+3, "=SUM(M" .($ii+3). ":M" .($i+2). ")" )  ;
    }







    /////////////// Encabezados HILOS ///////////////
    $consulta = "SELECT A.hilo, C.descripcion, SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden, A.fecha, A.horas, sum(B.roturas) as roturas,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial, C.generico, C.h_practico as titulo, A.horas
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN articulo C ON A.hilo = C.hilo
    LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY C.generico
    ORDER BY A.fecha DESC" ;

    $sheet->getStyle('S2:W2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $sheet->getStyle('S2:W2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
    $sheet->setCellValueByColumnAndRow(19, 2, "TITULO HILO")
          ->setCellValueByColumnAndRow(20, 2, "NUMEROS URDIDOS")
          ->setCellValueByColumnAndRow(21, 2, "NUMEROS POR KILOMETRO")
          ->setCellValueByColumnAndRow(22, 2, "ROTURAS")
          ->setCellValueByColumnAndRow(23, 2, "PAROS POR KILOMETRO") ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        $sheet->setCellValueByColumnAndRow(19, $i, is_numeric( trim($row['generico']) ) ? trim($row['generico']).' ALG' : trim($row['generico']) ) ;
        $sheet->setCellValueByColumnAndRow(20, $i, $row['numeros'] ) ;
        $sheet->setCellValueByColumnAndRow(21, $i, "=T".$i."/100" ) ;
        $sheet->setCellValueByColumnAndRow(22, $i, $row['roturas'] ) ;
        $sheet->setCellValueByColumnAndRow(23, $i, "=V".$i."/U".$i ) ;
        $i++;
      }
      $resultado->close();
    }

    $sheet->getStyle('S'.$i.':W'.$i )->getFont()->setBold(true) ;
    $sheet->setCellValueByColumnAndRow(19, $i, "TOTAL: " )  ;
    $sheet->setCellValueByColumnAndRow(20, $i, "=SUM(T3:T" .($i-1). ")" )  ;
    $sheet->setCellValueByColumnAndRow(21, $i, "=SUM(U3:U" .($i-1). ")" )  ;
    $sheet->setCellValueByColumnAndRow(22, $i, "=SUM(V3:V" .($i-1). ")" )  ;
    $sheet->setCellValueByColumnAndRow(23, $i, "=SUM(W3:W" .($i-1). ")" )  ;


    $mysqli->close();

    $nombreDelDocumento = "Reporte_Urdido.xlsx";
    /**
     * Los siguientes encabezados son necesarios para que
     * el navegador entienda que no le estamos mandando
     * simple HTML
     * Por cierto: no hagas ningún echo ni cosas de esas; es decir, no imprimas nada
     */
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($documento, 'Xlsx');
    $writer->save('php://output');
    exit;
  }

  if(isset($_GET['function']) && !empty($_GET['function'])){
      $function = $_GET['function'];
      switch($function) {
          case 'guarda_urdido' : guarda_urdido(); break;
          case 'lista_urdido' : lista_urdido(); break;
          case 'ultima_orden' : ultima_orden(); break;
          case 'genera_excel' : genera_excel(); break;
      }
  }

?>
