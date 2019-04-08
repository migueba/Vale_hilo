<!DOCTYPE html>

<html lang="es">

<head>
<title>Vale de Hilo</title>
<style>
td input[type="checkbox"] {
    float: left;
    margin: 0 auto;
    width: 100%;
}
</style>
<meta charset="utf-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="padding.css">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container">
        <h2 class="page-header">Vale de Hilo</h2>
        <form method="POST" action="procesa.php">
          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                  <label>Turno</label>
                  <select class="form-control" id="turno" name="turno">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                  </select>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="fecha" class="form-control" name="fecha" required/>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="form-group">
                  <label>ID</label>
                  <input type="text" id="idsupervisor" class="form-control" name="idsupervisor" required/>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                  <label>Supervisor</label>
                  <input type="text" id="supervisor" name="supervisor" class="form-control" />
              </div>
            </div>
            <!---
            <div class="col-xs-2">
              <div class="form-group">
                  <label>Destino</label>
                  <select class="form-control" id="destino" name="destino" required>
                      <option value="1">Urdido</option>
                      <option value="2">Tejido</option>
                      <option value="3">Maquila</option>
                  </select>
                  <small id="DestinoHelp" class="form-text text-muted">Destino del hilo.</small>
              </div>
            </div>-->
          </div>

          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                <label>Clave</label>
                <input type="text" id="clave_hilo" name="clave_hilo" class="form-control" />
              </div>
            </div>
            <div class="col-xs-7">
              <div class="form-group">
                <label>Hilo</label>
                <input type="text" id="hilos" name="hilos" class="form-control auto-widget" />
              </div>
            </div>
            <div class="col-xs-4">
              <div class="form-group" id="totales">
              </div>
            </div>
          </div>

          <div class="row " >
            <div class="col-xs-8">
              <div class="form-group" id="contenido_tabla" style="">
                <table id="contenido" class="table table-bordered table-hover table-sm"></table>
              </div>
            </div>

            <div class="col-xs-4">
              <!--<div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;"> -->
              <div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;">
              </div>
            </div>
          </div>

          <div class="btn-group">
              <button type="submit" class="btn btn-primary">Guardar</button>
          </div>

        </form>
    </div>


    <script >
      // Busca el Nombre del Hilo usando su Clave
      $( function() {

        $( "#clave_hilo" ).keypress(function( event ) {
          if ( event.which == 13 ) {
             event.preventDefault();

             var idhilo_var = parseFloat($('input[id=clave_hilo]').val()).toFixed(2);
             $.ajax({
                 url: "busca_hilo.php",
                 method: "POST",
                 data: { idhilo : idhilo_var },
                 dataType: "json",
                 success: function(r){
                   $('input[id=hilos]').val(r) ;
                  // Muestra la Tabla de los Hilos Disponibles
                   event.preventDefault();

                   $.ajax({
                    url: "tabla_hilos.php",
                    method: "POST",
                    data: {idhilo : idhilo_var},
                    dataType: "json",
                    success: function(data){
                      $("#contenido").html('');
                      $("#contenido").append("<thead class=\"thead-dark\"><tr><th>Sel.</th><th scope=\"col\">Clave</th><th scope=\"col\">Entrada</th><th scope=\"col\">Lote</th><th scope=\"col\">Tarima</th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Bobinas</th><th scope=\"col\">Presentacion</th><th scope=\"col\">Tipo</th></th></tr></thead>");
                      /* Vemos que la respuesta no este vacía y sea una arreglo */
                      if(data != null && $.isArray(data)){
                          /* Recorremos tu respuesta con each */
                          var i = 0 ;
                          $.each(data, function(key, value){
                              /* Vamos agregando a nuestra tabla las filas necesarias */
                              $("#contenido").append("<tr><td><input type=\"checkbox\" value="+value.id+" class=\"mycheck\" name=\"id_ent["+i+"]\"> </td><td>" + value.clave + "</td><td>" + value.entrada + "</td><td>" + value.lote + "</td><td>" + value.tarima + "</td><td>"+ value.pesoneto +"</td><td>"+value.bobinas +"</td><td>"+value.presentacion+"</td><td>"+value.tipo+"</td></tr>");
                              i++;
                          });
                          $("#contenido_tabla").css({"max-height":"350px", "overflow-y":"scroll"});
                      }else{
                        alert("Ocurrio un Incoveniente con la BD");
                      }
                    }
                   });
                  //////////////////////////
                 },
                 error: function(r) {
                   $('input[id=hilos]').val("") ;
                   $("#contenido").html('');
                   $("#contenido_tabla").css({"max-height":"", "overflow-y":""});
                   alert("No Existe la Clave de Hilo");
                 },
             });
          }
        });

        //script cuando le da click a un chebock
        $("#contenido").on('click', '.mycheck', function(data) {
            var $contador = 0;
            var $lista_check = $('.mycheck');
            $.each( $lista_check, function( key, val ) {
              if ($(val).is(':checked')){
                $contador++;
                return false;
              }
            });
            if ($contador === 0){
              $("#detalle").html('');
            }else if( !$("#existe_detalle").length ) {
              menu_destino();
            }

        });

        //Funcion para poner el menu de detalle de el vale del hilo
        function menu_destino(){
            for (var i = 0; i < 6; i++) {
              $("#detalle").append("<div id=\"existe_detalle\" class=\"row no-pad\">"+
                "<div class=\"col-xs-3\">"+
                  "<div class=\"form-group\">" +
                    "<label>Kgs</label>"+
                    "<input type=\"text\" name=\"detalle["+i+"][kgs]\" class=\"form-control\" />"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-3\">" +
                  "<div class=\"form-group\">"+
                    "<label>Destino</label>"+
                    "<select class=\"form-control\" id=\"destino_detalle\" name=\"detalle["+i+"][destino]\">"+
                      "<option value=\"1\">Urdido</option>"+
                      "<option value=\"2\">Tejido</option>"+
                      "<option value=\"3\">Maquila</option>"+
                    "</select>"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-4\">"+
                  "<div class=\"form-group\">" +
                    "<label>Tela</label>"+
                    "<input type=\"text\" name=\"detalle["+i+"][tela]\" class=\"form-control\" />"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-2\">"+
                  "<div class=\"form-group\">" +
                    "<label>Bobina</label>"+
                    "<input type=\"text\" name=\"detalle["+i+"][bobinas]\" class=\"form-control\" disabled/>"+
                  "</div>"+
                "</div>"+
              "</div>");
          }
        }

        //Funcion para los totales
        function mostrar_totales(){
        }

        // Escrip de Autocompletar
        $( function() {
          var lista_hilos = [];
          // retrieve JSon from external url and load the data inside an array :
          $.getJSON( "autocompletar.php", function( data ) {
            $.each( data, function( key, val ) {
              lista_hilos.push(val.label);
            });
          });
          $( "#hilos" ).autocomplete({
            source: lista_hilos,
            select: function( event, ui ) {
              event.preventDefault();
              alert(ui.item.label);
            }
          });
        } );

      } );
    </script>
</body>
</html>
