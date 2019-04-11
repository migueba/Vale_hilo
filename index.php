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
  <link rel="stylesheet" type="text/css" href="css/bootstrap3-3-7.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap3-3-7-theme.min.css">

  <link rel="stylesheet" type="text/css" href="css/padding.css">

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container">
        <h2 class="page-header">Vale de Hilo</h2>
        <form method="POST" id="formulario" action="modelo/procesa.php">
          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                  <label>Turno</label>
                  <select class="form-control" id="turno" name="turno" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                  </select>
                  <span data-key="turno" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="fecha" class="form-control" name="fecha" required/>
                  <span data-key="fecha" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-1">
              <div class="form-group">
                  <label>ID</label>
                  <input type="text" id="idsupervisor" class="form-control" name="idsupervisor" required/>
                  <span data-key="idsupervisor" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="form-group">
                  <label>Supervisor</label>
                  <input type="text" id="supervisor" name="supervisor" class="form-control" />
                  <span data-key="supervisor" class="label label-danger"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                <label>Clave</label>
                <input type="text" id="clave_hilo" name="clave_hilo" class="form-control" required/>
                <span data-key="clave_hilo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-5">
              <div class="form-group">
                <label>Hilo</label>
                <input type="text" id="hilos" name="hilos" class="form-control auto-widget" required/>
                <span data-key="hilos" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="form-group">
                <label>Titulo</label>
                <input type="text" id="titulo" name="titulo" class="form-control auto-widget" required/>
                <span data-key="titulo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="form-group" id="totales">
              </div>
            </div>
          </div>

          <div class="row no-pad" >
            <div class="col-xs-8">
              <div class="form-group" id="contenido_tabla" style="">
                <table id="contenido" class="table table-bordered table-hover table-sm"></table>
                <span data-key="id_ent" class="label label-danger"></span>
              </div>
            </div>

            <div class="col-xs-4">
              <!--<div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;"> -->
              <div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;">
              </div>
              <span data-key="detalle" class="label label-danger"></span>
            </div>
          </div>

          <div class="btn-group">
              <button type="submit" id="guardavale" class="btn btn-primary" disabled>Guardar</button>
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
                 url: "modelo/busca_hilo.php",
                 method: "POST",
                 data: { idhilo : idhilo_var },
                 dataType: "json",
                 success: function(r){
                   $('input[id=hilos]').val(r) ;
                  // Muestra la Tabla de los Hilos Disponibles
                   event.preventDefault();

                   $.ajax({
                    url: "modelo/tabla_hilos.php",
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
                              $("#contenido").append("<tr><td><input data-peso=\""+value.pesoneto+"\" data-bobina=\""+value.bobinas+"\" type=\"checkbox\" value="+value.id+" class=\"mycheck\" name=\"id_ent["+i+"]\"> </td><td>" +
                              value.clave + "</td><td>" + value.entrada + "</td><td>" + value.lote + "</td><td>" + value.tarima + "</td><td>"+
                              value.pesoneto +"</td><td>"+ value.bobinas +"</td><td>"+value.presentacion+"</td><td>"+value.tipo+"</td></tr>");
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
            var $contador = 0 ;
            var $totalpeso = 0 ;
            var $totalbobinas = 0 ;
            var $lista_check = $('.mycheck');

            $.each( $lista_check, function( key, val ) {
              if ($(val).is(':checked')){
                $totalpeso = $totalpeso + parseFloat($(val).attr('data-peso')) ;
                $totalbobinas = $totalbobinas + parseInt($(val).attr('data-bobina')) ;
                $contador++;
                //return false; Sirve para salir el each
              }
            });

            if ($contador === 0){
              document.getElementById("guardavale").disabled = true;
              $("#detalle").html('');
              $("#totales").html('');
            }else if( !$("#existe_detalle").length ) {
              document.getElementById("guardavale").disabled = false;
              menu_destino($totalpeso.toFixed(2),$totalbobinas);
            }else {
               $('input[id=pesototal]').val($totalpeso.toFixed(2)) ;
               $('input[id=bobinatotal]').val($totalbobinas) ;
            }
        });

        //Funcion para poner el menu de detalle de el vale del hilo
        function menu_destino($tkgs, $tbobina){

            for (var i = 0; i < 6; i++) {
              $("#detalle").append("<div id=\"existe_detalle\" class=\"row no-pad\">"+
                "<div class=\"col-xs-3\">"+
                  "<div class=\"form-group\">" +
                    "<label>Kgs</label>"+
                    "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][kgs]\" class=\"form-control\" />"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-3\">" +
                  "<div class=\"form-group\">"+
                    "<label>Destino</label>"+
                    "<select class=\"form-control\" data-id=\""+i+"\" id=\"destino_detalle\" name=\"detalle["+i+"][destino]\">"+
                      "<option value=\"0\"></option>"+
                      "<option value=\"1\">Urdido</option>"+
                      "<option value=\"2\">Tejido</option>"+
                      "<option value=\"3\">Maquila</option>"+
                    "</select>"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-4\">"+
                  "<div class=\"form-group\">" +
                    "<label>Tela</label>"+
                    "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][tela]\" class=\"form-control\" />"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-xs-2\">"+
                  "<div class=\"form-group\">" +
                    "<label>Bobina</label>"+
                    "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][bobinas]\" class=\"form-control\" readonly/>"+
                  "</div>"+
                "</div>"+
              "</div>");
          }

          // Pone los Totales
          $("#totales").append("<div class=\"row\">"+
              "<div class=\"col-xs-6\">"+
                "<div class=\"form-group\">" +
                  "<label>Total Kgs</label>"+
                  "<input type=\"text\" id=\"pesototal\" name=\"pesototal\" value=\""+$tkgs+"\" class=\"form-control\"/ readonly>"+
                "</div>"+
              "</div>"+
              "<div class=\"col-xs-6\">"+
                "<div class=\"form-group\">" +
                  "<label>Total Bobinas</label>"+
                  "<input type=\"text\" id=\"bobinatotal\" name=\"bobinatotal\" value=\""+$tbobina+"\" class=\"form-control\"/ readonly>"+
                "</div>"+
              "</div>"+
            "</div>");
        }

        //Funcion para validar que el el detallado de KGS se cambio
        $("#detalle").on('change', ':text[name^="detalle["][name$="][kgs]"]', function(data) {
          var $kgs_sel  = parseFloat($('input[id=pesototal]').val()) ;
          var $bobi_sel = parseInt($('input[id=bobinatotal]').val()) ;
          var $puso_peso = parseFloat($(this).val()) ;

          $(':text[ name^="detalle['+$(this).attr('data-id')+'][bobinas]" ]').val(($puso_peso*$bobi_sel)/$kgs_sel) ;
          //console.log($(this).attr('data-id')) ;
        });

        //function valida_detalle($tkgs, $tbobina){
        //}

        // Escrip de Autocompletar
        // retrieve JSon from external url and load the data inside an array :
        var lista_hilos = [];
        $.getJSON( "modelo/autocompletar.php", function( data ) {
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

        /*
        /////// Cuando preciona el Boton de Guardar al Formulario
        var form = $("#formulario");
        form.submit(function(){
            form.find('.label-danger').text('');

            $.ajax({
                url: "modelo/guardar_vale.php",
                method: "POST",
                data: form.serialize(),
                dataType: "json",
                success: function(r){
                    if(!r.response) {
                        for(var k in r.errors){
                            $("span[data-key='" + k + "']").text(r.errors[k]);
                        }
                    }
                },
                error: function(r) {
                  alert("Error");
                }
            });
            return false;
        });
      */

      } );
    </script>
</body>
</html>
