<?php
  require 'vendor/autoload.php' ;

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
    $highestColumn = 'F'; // e.g 'F'
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5

    for ($row = 6; $row <= $highestRow; ++$row) {
        //for ($col = 2; $col <= $highestColumnIndex; ++$col) {
            $clave = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $tela =  strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $pie =  $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $trama =  $sheet->getCellByColumnAndRow(6, $row)->getValue();

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
              array_push($TelasArray, $data);
            }
        //}
    }
    asort($TelasArray);
  }
  $_SESSION["telas"] = $TelasArray;
  var_dump ($TelasArray);
  //echo json_encode($TelasArray,JSON_UNESCAPED_UNICODE) ;
?>
