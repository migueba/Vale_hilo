<?php
  require '../vendor/autoload.php' ;

  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\IOFactory;

  $TelasArray = array() ;
  $inputFileType = 'Xlsx';
  $inputFileName = '\\\SERVIDORP\Planeacion de Hilo\PRONOSTICOS TELARES.xlsx';

  if (file_exists($inputFileName)){
    //$inputFileName = 'F:\PRONOSTICOS TELARES.xlsx' ;
    /**  Create a new Reader of the type defined in $inputFileType  **/
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
    /**  Advise the Reader that we only want to load cell data  **/
    $reader->setReadDataOnly(true);
    /**  Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = $reader->load($inputFileName) ;
    $sheet = $spreadsheet->getSheetByName("2013") ;

    // Get the highest row and column numbers referenced in the worksheet
    $highestRow = $sheet->getHighestRow(); // e.g. 10

    $TelasArray = array() ;

    for ($row = 6; $row <= $highestRow; ++$row) { // Comienza a leer desde el 6
      if ( $sheet->getCellByColumnAndRow(2, $row)->getValue() === strtoupper($_GET['clave']) ){
        $data['clave'] = $sheet->getCellByColumnAndRow(2, $row)->getValue() ;
        $data['tela'] = strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue()) ;
        $data['pie'] = $sheet->getCellByColumnAndRow(4, $row)->getValue() ;
        $data['trama'] = $sheet->getCellByColumnAndRow(6, $row)->getValue() ;
        $data['anchopeine'] = $sheet->getCellByColumnAndRow(8, $row)->getValue() ;
        $data['hilopeine'] = $sheet->getCellByColumnAndRow(10, $row)->getValue() ;
        $data['luchaxpulgada'] = $sheet->getCellByColumnAndRow(14, $row)->getValue() ;
        $data['anchocm'] = $sheet->getCellByColumnAndRow(15, $row)->getValue() ;
        $data['pesopta'] = $sheet->getCellByColumnAndRow(20, $row)->getCalculatedValue() ;
        $data['pesogm2'] = round($sheet->getCellByColumnAndRow(24, $row)->getCalculatedValue()) ;

        $data['hxpulg'] = round($sheet->getCellByColumnAndRow(12, $row)->getCalculatedValue()) ;
        $data['hxpulg2'] = round($sheet->getCellByColumnAndRow(13, $row)->getCalculatedValue()) ;

        array_push($TelasArray, $data);
        break;
      }
    }
    header('Content-Type: application/json');
    echo json_encode($TelasArray,JSON_UNESCAPED_UNICODE);
  }

?>
