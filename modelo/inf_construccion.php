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

    for ($row = 6; $row <= $highestRow; ++$row) { // Comienza a leer desde el 6
            $clave = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $tela =  strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $pie =  $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $trama =  $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $Anchopeine = $sheet->getCellByColumnAndRow(8, $row)->getValue();
            $hilopeine = $sheet->getCellByColumnAndRow(10, $row)->getValue();
            $luchapulgada = $sheet->getCellByColumnAndRow(14, $row)->getValue();
            $anchocm = $sheet->getCellByColumnAndRow(15, $row)->getValue();
            $pesoPTA =  $sheet->getCellByColumnAndRow(20, $row)->getCalculatedValue();
            $pesogm2 =  $sheet->getCellByColumnAndRow(24, $row)->getCalculatedValue();

            if(!is_null($clave) && !is_null($tela) && !is_null($pie) && !is_null($trama) ){
              if (gettype($pie) != 'string' ){
                $pie = strval($pie) ;
              }
              if (gettype($trama) != 'string' ){
                $trama = strval($trama) ;
              }

              $data['clave'] = $clave ;
              $data['tela'] = $tela ;
              $data['pie'] = $pie ;
              $data['trama'] = $trama ;
              $data['anchopeine'] = $Anchopeine ;
              $data['hilopeine'] = $hilopeine ;
              $data['luchaxpulgada'] = $luchapulgada ;
              $data['anchocm'] = $anchocm ;
              $data['pesopta'] = $pesoPTA ;
              $data['pesogm2'] = round($pesogm2) ;

              array_push($TelasArray, $data);
            }
    }
    asort($TelasArray);
    var_dump($TelasArray);
  }

?>
