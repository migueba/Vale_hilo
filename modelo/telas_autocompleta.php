<?php
  session_start();
  // Verifico que Exista la variable
  if(isset($_SESSION["telas"])){
    $arreglo_tela = $_SESSION["telas"];
    $Telasdata = array() ;

    for($i=0; $i < count($arreglo_tela); $i++){
      if($_GET['destino'] == '2'){
        if( strnatcasecmp($arreglo_tela[$i]['pie'], $_GET['general'] ) === 0) {
          $Telasdata[] = $arreglo_tela[$i]['clave'] ;
        }
      }else if($_GET['destino'] == '3'){
        if( strnatcasecmp($arreglo_tela[$i]['trama'], $_GET['general'] ) === 0 ){
          $Telasdata[] = $arreglo_tela[$i]['clave'] ;
        }
      }
    }
    echo json_encode($Telasdata,JSON_UNESCAPED_UNICODE);
  }
?>
