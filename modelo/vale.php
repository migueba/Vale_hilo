<?php
function lista_vale() {
  include("bd.php") ;

  $consulta = "SELECT A.idvale_hilo as vale, A.hilo, A.fecha, A.turno,
    C.nombre+\" \"+C.apaterno+\" \"+C.amaterno as supervisor,
    SUM(B.Bobinas) as bobinas,
    SUM(B.kilos) as kilos,
    IF(A.estado=-1, \"PENDIENTE\", \"SURTIDO   \") as estado
  FROM vale_hilo A
    LEFT JOIN vale_hilo_detalle B ON A.idvale_hilo = B.idvale_hilo
    LEFT JOIN usuarios C ON A.supervisor = C.num_emp
    WHERE A.estado <> 0
  GROUP BY A.idvale_hilo  ";
}

if(isset($_GET['function']) && !empty($_GET['function'])){
    $function = $_GET['function'];
    switch($function) {
        case 'lista_vale' : lista_vale();break;
    }
}
?>
