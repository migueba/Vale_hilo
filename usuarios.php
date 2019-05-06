<!DOCTYPE html>
<?php session_start();
  if(!isset($_SESSION["telas"])){
    include("modelo/inf_tela.php") ;
  }
?>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Vale de Hilo</title>

  <script src="js/jquery.js"></script>

  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <script src="js/bootstrap.js"></script>

  <script src="js/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
</head>
<body>
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light bg-light bg-primary" style ="margin-bottom: 30px;">
        <img style="height: 50px; width: 110px; display: block; margin-right:30px;" src="images/FABRICA MARÍA SIN FONDO_negro_corta.png" />

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarColor01">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="index.php"><b>Crear Vale </b><span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="vales.php"><b>Vales</b></a>
            </li>
            <li class="nav-item bg-light">
              <a class="nav-link" href="usuarios.php"><b>Usuarios</b></a>
            </li>
          </ul>
        </div>
      </nav>

        <h2 class="page-header">Supervisores y/o Usuarios</h2>
          <div class="row">
            <div class="col-lg-12" >
              <div id="waiting" style="display: none;"> <img width="200" src="images/loading.gif" /><p>Cargando contenido</p></div>
              <div class="form-group" id="contenido_tabla" style="">
                <table id="contenido" class="table table-sm">
                </table>
                <span data-key="error" class="badge badge-danger"></span>
              </div>
            </div>
          </div>
    </div>

    <script >
      $( function() {

        $( "#waiting" ).show( "slow" );
        $.ajax({
            url: "modelo/lista_vales.php",
            method: "POST",
            dataType: "json",
          success: function(data){
            $("#contenido").html('');
            $("#contenido").append("<thead class=\"thead-light\"><tr><th scope=\"col\">ID VALE</th><th scope=\"col\">Hilo</th><th scope=\"col\">Fecha</th><th scope=\"col\">Turno</th><th scope=\"col\">Superv.</th><th scope=\"col\">Bobinas</th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Estado</th><th scope=\"col\"></th><th scope=\"col\"></th></tr></thead> <tbody>");
            // Vemos que la respuesta no este vacía y sea una arreglo
            if(data != null && $.isArray(data)){
              // Recorremos tu respuesta con each
              var i = 0 ;
              $.each(data, function(key, value){
              // Vamos agregando a nuestra tabla las filas necesarias
              var $fechapre = (value.fecha).split('-');
              $("#contenido").append("<tr><td>"+value.id+"</td><td>"+value.clave_hilo + "</td>"+
                "<td>"+$fechapre[2]+"/"+$fechapre[1]+"/"+$fechapre[0]+"</td><td>" + value.turno + "</td><td>" + value.supervisor +"</td>"+
                "<td>"+value.Bobinas+"</td><td>" + value.Kilos + "</td><td>" + value.estado + "</td>" +
                "<td><img id-vale=\""+value.id+"\" class=\"clickojo\" src=\"images/ojo.png\" /></td>"+
                "<td><img class=\"clickcarta\" src=\"images/escritura.png\"/></td>"+
                "</tr>");
                i++;
              });
              $("#contenido").append("</tbody>") ;
              //$("#contenido_tabla").css({"max-height":"350px", "overflow-y":"scroll"});
            }
            $( "#waiting" ).hide( "slow" );
          },
          error: function(r) {
            alert("Ocurrio un Incoveniente con la BD");
            $( "#waiting" ).hide( "slow" );
          },
        });

        $("#contenido").on('click', '.clickojo', function(data) {
          var id_vale = $(this).attr('id-vale') ;
          window.open('modelo/ver_vale.php?id_vale='+id_vale, '_blank');
        });

      });
    </script>
</body>
</html>
