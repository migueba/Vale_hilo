<?php
  require '../vendor/autoload.php' ;

  use PhpOffice\PhpSpreadsheet\Spreadsheet;
  use PhpOffice\PhpSpreadsheet\IOFactory;

  // Copio el Archivo de Excel para que no ponga solod e lectura al archivo Original
  $from = '\\\SERVIDORP\Planeacion de Hilo\PRONOSTICOS TELARES.xlsx' ;
  /*$to = '\\\servidorp\e$\sistemas\CONSTRUCCION.xls' ;
  copy($from,$to)

  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
  $spreadsheet = $reader->load("05featuredemo.xlsx");
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
  */
  $TelasArray = array() ;
  $inputFileType = 'Xlsx';
  $inputFileName = '\\\SERVIDORP\Planeacion de Hilo\PRONOSTICOS TELARES.xlsx';

  /**  Create a new Reader of the type defined in $inputFileType  **/
  $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
  /**  Advise the Reader that we only want to load cell data  **/
  $reader->setReadDataOnly(true);
  /**  Load $inputFileName to a Spreadsheet Object  **/
  $spreadsheet = $reader->load($inputFileName) ;
  $sheet = $spreadsheet->getSheetByName("2013") ;

  echo '<table border="1" cellpadding="8" >' ;
  foreach ($sheet->getRowIterator() as $row) {
    $CellIterator = $row->getCellIterator("B","F") ;
    $CellIterator->setIterateOnlyExistingCells(false) ;
    echo '<tr>';
    foreach ($CellIterator as $Cell) {
      if(!is_null($Cell)){
        $value = $Cell->getValue() ;
        echo '<td> '. $Cell .' '.$value . '</td>' ;
        //$data['clave'] = $row['descripcion'];
        //$data['pie'] = $row['hilo'];
        //$data['trama'] = $row['hilo'];
        //array_push($HilosData, $data);
      }
    }
    echo '</tr>' ;
  }
  echo '</table>' ;
?>
