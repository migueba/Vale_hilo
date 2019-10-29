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
    include("bd.php") ;

    $date1 = str_replace('/', '-', $_GET['fecha1'] ) ;
    $newDate1 = date("Y/m/d", strtotime($date1)) ;

    $date2 = str_replace('/', '-', $_GET['fecha2'] ) ;
    $newDate2 = date("Y/m/d", strtotime($date2)) ;

    /////////////// ORDEN ///////////////
    $consulta = "SELECT SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden,
    A.fecha, SUM(B.roturas) as roturas, B.bobinas as bobinas, C.h_practico as titulo,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN urdido_engomado.articulo C ON A.hilo = C.hilo
    LEFT JOIN urdido_engomado.usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY A.orden, A.urdido_usuario
    ORDER BY A.orden, A.urdido_usuario DESC" ;
    $documento = new Spreadsheet() ;
    $documento->setActiveSheetIndex(0) ;
    $documento
        ->getProperties()
        ->setCreator("JuCeBaro")
        ->setLastModifiedBy('juCeBaRo') // última vez modificado por
        ->setTitle('Reporte de Urdido')
        ->setSubject('Reporte')
        ->setDescription('Este documento fue generado para obtener la informacion de Urdido') ;
    $sheet = $documento->getActiveSheet() ;
    $sheet->getStyle('2')->getFont()->setBold(true) ;
    $sheet->getStyle('A2:G2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $sheet->getStyle('A2:G2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
    $sheet->setTitle("Urdido")
          ->setCellValueByColumnAndRow(1, 2, "Orden")
          ->setCellValueByColumnAndRow(2, 2, "Tela")
          ->setCellValueByColumnAndRow(3, 2, "Oficial")
          ->setCellValueByColumnAndRow(4, 2, "Metros")
          ->setCellValueByColumnAndRow(5, 2, "Kilos")
          ->setCellValueByColumnAndRow(6, 2, "Roturas")
          ->setCellValueByColumnAndRow(7, 2, "Fecha") ;
    $sheet->getColumnDimension('A')->setWidth(8) ;
    $sheet->getColumnDimension('B')->setWidth(16) ;
    $sheet->getColumnDimension('C')->setWidth(34) ;
    $sheet->getColumnDimension('D')->setWidth(12) ;
    $sheet->getColumnDimension('E')->setWidth(12) ;
    $sheet->getColumnDimension('F')->setWidth(10) ;
    $sheet->getColumnDimension('G')->setWidth(12) ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      $orden_ = 0 ;
      $inicia_ = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        if($i==3){$orden_ =  $row['orden'];}
        if( $orden_ != $row['orden'] ){
          $sheet->getStyle($i)->getFont()->setBold(true) ;
          $sheet->setCellValueByColumnAndRow(3, $i, "Subtotal Orden ".$orden_." : " ) ;
          $sheet->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
          $sheet->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(9,D".$inicia_.":D".($i-1).")" ) ;
          $sheet->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
          $sheet->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(9,E".$inicia_.":E".($i-1).")" ) ;
          $sheet->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
          $sheet->setCellValueByColumnAndRow(6, $i, "=SUBTOTAL(9,F".$inicia_.":F".($i-1).")" ) ;
          $i += 2 ;
          $orden_ = $row['orden'] ;
          $inicia_ = $i ;
        }
        $sheet->setCellValueByColumnAndRow(1, $i, $row['orden'] ) ;
        $sheet->setCellValueByColumnAndRow(2, $i, $row['tela'] ) ;
        $sheet->setCellValueByColumnAndRow(3, $i, $row['oficial'] ) ;
        $sheet->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $sheet->setCellValueByColumnAndRow(4, $i, "=".$row['numeros']." * 10" ) ;
        $sheet->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $sheet->setCellValueByColumnAndRow(5, $i, "=".$row['julios']." * ".$row['bobinas']." * ".$row['numeros']." * 10 * .59 / (".$row['titulo']."*100)" ) ;
        $sheet->setCellValueByColumnAndRow(6, $i, $row['roturas'] ) ;
        $date = str_replace('-', '/', $row['fecha'] ) ;
        $newDate = date("d/m/Y", strtotime($date)) ;
        $sheet->setCellValueByColumnAndRow(7, $i, $newDate)  ;
        $i++;
      }
      $sheet->getStyle($i)->getFont()->setBold(true) ;
      $sheet->setCellValueByColumnAndRow(3, $i, "Subtotal Orden ".$orden_." : " ) ;
      $sheet->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(9,D".$inicia_.":D".($i-1).")" ) ;
      $sheet->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(9,E".$inicia_.":E".($i-1).")" ) ;
      $sheet->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(6, $i, "=SUBTOTAL(9,F".$inicia_.":F".($i-1).")" ) ;
      $i += 2 ;
      $sheet->getStyle($i)->getFont()->setBold(true) ;
      $sheet->setCellValueByColumnAndRow(3, $i, "TOTAL: " ) ;
      $sheet->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(109,D3:D".($i-1).")" ) ;
      $sheet->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(109,E3:E".($i-1).")" ) ;
      $sheet->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $sheet->setCellValueByColumnAndRow(6, $i, "=SUBTOTAL(109,F3:F".($i-1).")" ) ;

      $resultado->close();
    }





    /////////////// TELA ///////////////
    $consulta = "SELECT SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden,
    A.fecha, SUM(B.roturas) as roturas, B.bobinas as bobinas, C.h_practico as titulo,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN urdido_engomado.articulo C ON A.hilo = C.hilo
    LEFT JOIN urdido_engomado.usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY A.tela
    ORDER BY A.tela ASC" ;
    $documento->createSheet() ;
    $documento->setActiveSheetIndex(1) ;
    $documento->getActiveSheet()->setTitle('Telas')
              ->setCellValueByColumnAndRow(1, 2, "Tela")
              ->setCellValueByColumnAndRow(2, 2, "Metros Urdidos")
              ->setCellValueByColumnAndRow(3, 2, "Kilos")
              ->setCellValueByColumnAndRow(4, 2, "Roturas")
              ->setCellValueByColumnAndRow(5, 2, "Roturas Por Kilometro") ;
    $documento->getActiveSheet()->getColumnDimension('A')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('B')->setWidth(16) ;
    $documento->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('D')->setWidth(10) ;
    $documento->getActiveSheet()->getColumnDimension('E')->setWidth(21) ;

    $documento->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true) ;
    $documento->getActiveSheet()->getStyle('A2:F2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $documento->getActiveSheet()->getStyle('A2:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        $documento->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $row['tela'] ) ;
        $documento->getActiveSheet()->getStyle('B'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, "=".$row['numeros']." * 10" ) ;
        $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=".$row['julios']." * ".$row['bobinas']." * ".$row['numeros']." * 10 * .59 / (".$row['titulo']."*100)" ) ;
        $documento->getActiveSheet()->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $row['roturas'] ) ;
        $documento->getActiveSheet()->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=".$row['roturas']." / (".$row['numeros']."/100)" ) ;
        $i++;
      }
      $documento->getActiveSheet()->getStyle($i)->getFont()->setBold(true) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(1, $i, "TOTAL: " ) ;
      $documento->getActiveSheet()->getStyle('B'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, "=SUBTOTAL(9,B3:B".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=SUBTOTAL(9,C3:C".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(9,D3:D".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(9,E3:E".($i-1).")" ) ;

      $resultado->close();
    }





    /////////////// TITULO DE HILO ///////////////
    $consulta = "SELECT SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden,
    A.fecha, SUM(B.roturas) as roturas, B.bobinas as bobinas, C.h_practico as titulo, C.generico,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN urdido_engomado.articulo C ON A.hilo = C.hilo
    LEFT JOIN urdido_engomado.usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY C.generico
    ORDER BY C.generico ASC" ;
    $documento->createSheet() ;
    $documento->setActiveSheetIndex(2) ;
    $documento->getActiveSheet()->setTitle('HILO')
              ->setCellValueByColumnAndRow(1, 2, "Titulo Hilo")
              ->setCellValueByColumnAndRow(2, 2, "Metros Urdido")
              ->setCellValueByColumnAndRow(3, 2, "Kilos Urdido")
              ->setCellValueByColumnAndRow(4, 2, "Roturas")
              ->setCellValueByColumnAndRow(5, 2, "Roturas Por Km") ;
    $documento->getActiveSheet()->getColumnDimension('A')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('B')->setWidth(16) ;
    $documento->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('D')->setWidth(10) ;
    $documento->getActiveSheet()->getColumnDimension('E')->setWidth(21) ;

    $documento->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true) ;
    $documento->getActiveSheet()->getStyle('A2:F2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $documento->getActiveSheet()->getStyle('A2:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      while( $row = $resultado->fetch_assoc() ){
        $documento->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $row['generico'] ) ;
        $documento->getActiveSheet()->getStyle('B'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, "=".$row['numeros']." * 10" ) ;
        $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=".$row['julios']." * ".$row['bobinas']." * ".$row['numeros']." * 10 * .59 / (".$row['titulo']."*100)" ) ;
        $documento->getActiveSheet()->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $row['roturas'] ) ;
        $documento->getActiveSheet()->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=".$row['roturas']." / (".$row['numeros']."/100)" ) ;
        $i++;
      }
      $documento->getActiveSheet()->getStyle($i)->getFont()->setBold(true) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(1, $i, "TOTAL: " ) ;
      $documento->getActiveSheet()->getStyle('B'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, "=SUBTOTAL(9,B3:B".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=SUBTOTAL(9,C3:C".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(9,D3:D".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(9,E3:E".($i-1).")" ) ;

      $resultado->close();
    }






    /////////////// TITULO DE OFICIAL ///////////////
    $consulta = "SELECT SUM(B.julios) as julios, SUM(B.julios * B.numeros) as numeros, A.orden,
    A.fecha, SUM(B.roturas) as roturas, B.bobinas as bobinas, C.h_practico as titulo, C.generico,
    A.tela, concat(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial, A.horas
    FROM urdido_engomado.urdido A
    LEFT JOIN urdido_engomado.urdido_detalle B ON A.idurdido = B.id_urdido
    INNER JOIN urdido_engomado.articulo C ON A.hilo = C.hilo
    LEFT JOIN urdido_engomado.usuarios D ON A.urdido_usuario = D.idusuario
    WHERE A.fecha >= \"".$newDate1."\" AND A.fecha <= \"" .$newDate2. "\"
    GROUP BY A.fecha, A.urdido_usuario
    ORDER BY A.fecha, A.urdido_usuario DESC" ;
    $documento->createSheet() ;
    $documento->setActiveSheetIndex(3) ;
    $documento->getActiveSheet()->setTitle('OFICIAL')
              ->setCellValueByColumnAndRow(1, 2, "Fecha")
              ->setCellValueByColumnAndRow(2, 2, "Oficial")
              ->setCellValueByColumnAndRow(3, 2, "Metros")
              ->setCellValueByColumnAndRow(4, 2, "Kilos")
              ->setCellValueByColumnAndRow(5, 2, "Horas")
              ->setCellValueByColumnAndRow(6, 2, "Roturas")
              ->setCellValueByColumnAndRow(7, 2, "Metros x Hora")
              ->setCellValueByColumnAndRow(8, 2, "% Metros")
              ->setCellValueByColumnAndRow(9, 2, "% Horas") ;
    $documento->getActiveSheet()->getColumnDimension('A')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('B')->setWidth(32) ;
    $documento->getActiveSheet()->getColumnDimension('C')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('D')->setWidth(12) ;
    $documento->getActiveSheet()->getColumnDimension('E')->setWidth(10) ;
    $documento->getActiveSheet()->getColumnDimension('G')->setWidth(13) ;
    $documento->getActiveSheet()->getColumnDimension('H')->setWidth(11) ;
    $documento->getActiveSheet()->getColumnDimension('I')->setWidth(11) ;

    $documento->getActiveSheet()->getColumnDimension('K')->setWidth(32) ;
    $documento->getActiveSheet()->getColumnDimension('L')->setWidth(12) ;
    $documento->getActiveSheet()->getColumnDimension('M')->setWidth(13) ;
    $documento->getActiveSheet()->getColumnDimension('P')->setWidth(15) ;
    $documento->getActiveSheet()->getColumnDimension('Q')->setWidth(11) ;
    $documento->getActiveSheet()->getColumnDimension('R')->setWidth(11) ;

    $documento->getActiveSheet()->getStyle('A2:I2')->getFont()->setBold(true) ;
    $documento->getActiveSheet()->getStyle('A2:I2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
    $documento->getActiveSheet()->getStyle('A2:I2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;

    if ($resultado = $mysqli->query($consulta)) {
      $i = 3 ;
      $total = mysqli_num_rows($resultado) ;
      while( $row = $resultado->fetch_assoc() ){
        $date = str_replace('-', '/', $row['fecha'] ) ;
        $newDate = date("d/m/Y", strtotime($date)) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $newDate)  ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $row['oficial'] ) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=".$row['numeros']." * 10" ) ;
        $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, "=".$row['julios']." * ".$row['bobinas']." * ".$row['numeros']." * 10 * .59 / (".$row['titulo']."*100)" ) ;
        $documento->getActiveSheet()->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, $row['horas'] ) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(6, $i, $row['roturas'] ) ;
        $documento->getActiveSheet()->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(7, $i, "=C".$i." / E".$i ) ;
        $documento->getActiveSheet()->getStyle('G'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;

        $documento->getActiveSheet()->setCellValueByColumnAndRow(8, $i, "=C".$i."/C".($total+3) );
        $documento->getActiveSheet()->getStyle('H'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(9, $i, "=E".$i."/E".($total+3) ) ;
        $documento->getActiveSheet()->getStyle('I'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00) ;

        $i++;
      }
      $documento->getActiveSheet()->getStyle($i)->getFont()->setBold(true) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(2, $i, "TOTAL: " ) ;
      $documento->getActiveSheet()->getStyle('C'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(3, $i, "=SUBTOTAL(9,C3:C".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('D'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(4, $i, "=SUBTOTAL(9,D3:D".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('E'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(5, $i, "=SUBTOTAL(9,E3:E".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('F'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(6, $i, "=SUBTOTAL(9,F3:F".($i-1).")" ) ;
      $documento->getActiveSheet()->getStyle('G'.$i )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(7, $i, "=SUBTOTAL(9,G3:G".($i-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(8, $i, "=SUBTOTAL(9,H3:H".($i-1).")*100" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(9, $i, "=SUBTOTAL(9,I3:I".($i-1).")*100" ) ;

      $resultado->close();
    }
    // **************** RESUMEN OFICIAL **************** //
    $consulta = "SELECT DISTINCT A.urdido_usuario, CONCAT(trim(D.nombre),\" \",trim(D.apaterno),\" \",trim(D.amaterno)) as oficial
      FROM urdido_engomado.urdido A
      LEFT JOIN usuarios D ON A.urdido_usuario = D.idusuario
      WHERE A.fecha >= \"" .$newDate1. "\" AND A.fecha <= \"" .$newDate2 . "\"" ;

    if( $resultado2 = $mysqli->query($consulta) ) {
      $total = mysqli_num_rows($resultado2) ;

      $documento->getActiveSheet()->getStyle('K2:R2')->getFont()->setBold(true) ;
      $documento->getActiveSheet()->getStyle('K2:R2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE) ;
      $documento->getActiveSheet()->getStyle('K2:R2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 11, 2, "OFICIAL" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 12, 2, "METROS" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 13, 2, "KILOS" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 14, 2, "HORAS" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 15, 2, "ROTURAS" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 16, 2, "METROS X HORA" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 17, 2, "% METROS" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow( 18, 2, "% HORAS" ) ;

      $ii = 3 ;
      while( $row = $resultado2->fetch_assoc() ){
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 11, $ii, $row['oficial'] )  ;
        $documento->getActiveSheet()->getStyle('L'.$ii.':P'.$ii)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 12, $ii, "=SUMIF(B3:B" .($i-1). ",\"" .$row['oficial']. "\",C3:C".($i-1).")" )  ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 13, $ii, "=SUMIF(B3:B" .($i-1). ",\"" .$row['oficial']. "\",D3:D".($i-1).")" )  ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 14, $ii, "=SUMIF(B3:B" .($i-1). ",\"" .$row['oficial']. "\",E3:E".($i-1).")" )  ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 15, $ii, "=SUMIF(B3:B" .($i-1). ",\"" .$row['oficial']. "\",F3:F".($i-1).")" )  ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow( 16, $ii, "=L".$ii."/N".$ii )  ;
        $documento->getActiveSheet()->getStyle('Q'.$ii.':R'.$ii )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(17, $ii, "=L".$ii."/L$".($total+3) ) ;
        $documento->getActiveSheet()->setCellValueByColumnAndRow(18, $ii, "=N".$ii."/N$".($total+3) ) ;

        $ii++;
      }
      $documento->getActiveSheet()->getStyle($ii)->getFont()->setBold(true) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(11, $ii, "TOTAL: " ) ;
      $documento->getActiveSheet()->getStyle('L'.$ii.':P'.$ii )->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(12, $ii, "=SUBTOTAL(9,L3:L".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(13, $ii, "=SUBTOTAL(9,M3:M".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(14, $ii, "=SUBTOTAL(9,N3:N".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(15, $ii, "=SUBTOTAL(9,O3:O".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(16, $ii, "=SUBTOTAL(9,P3:P".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(17, $ii, "=SUBTOTAL(9,Q3:Q".($ii-1).")" ) ;
      $documento->getActiveSheet()->setCellValueByColumnAndRow(18, $ii, "=SUBTOTAL(9,R3:R".($ii-1).")" ) ;

      $resultado2->close();
    }


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
