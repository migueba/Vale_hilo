<!DOCTYPE html>

<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Vale de Hilo</title>
  </style>
  <meta charset="utf-8" />
  <script src="js/jquery.js"></script>

  <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
  <script src="js/bootstrap.js"></script>

  <script src="js/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">


</head>

<body>
    <div class="container">
        <h2 class="page-header">Vale de Hilo</h2>
        <form method="POST" id="formulario" action="modelo/procesa.php">
          <div class="row">
            <div class="col-lg-1">
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
            <div class="col-lg-3">
              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="fecha" class="form-control" name="fecha" required/>
                  <span data-key="fecha" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-lg-1">
              <div class="form-group">
                  <label>ID</label>
                  <input type="text" id="idsupervisor" class="form-control" name="idsupervisor" required/>
                  <span data-key="idsupervisor" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-lg-7">
              <div class="form-group">
                  <label>Supervisor</label>
                  <input type="text" id="supervisor" name="supervisor" class="form-control" />
                  <span data-key="supervisor" class="label label-danger"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-1">
              <div class="form-group ui-widget">
                <label>Clave</label>
                <input type="text" id="clave_hilo" name="clave_hilo" class="form-control" required/>
                <span data-key="clave_hilo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="form-group">
                <label>Hilo</label>
                <input type="text" id="hilos" name="hilos" class="form-control auto-widget" required/>
                <span data-key="hilos" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-lg-2">
              <div class="form-group">
                <label>Tipo</label>
                <input type="text" id="tipo" name="tipo" class="form-control auto-widget" required readonly/>
                <span data-key="tipo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group" id="totales">
              </div>
            </div>
          </div>

          <div class="row no-gutters" >
            <div class="col-lg-7">
              <div class="form-group" id="contenido_tabla" style="">
                <table id="contenido" class="table table-sm"></table>
                <span data-key="id_ent" class="label label-danger"></span>
              </div>
            </div>

            <div class="col-lg-5">
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
        $(".form-group").on('change', '#clave_hilo', function(data) {
          event.preventDefault();
          var idhilo_var = parseFloat($('input[id=clave_hilo]').val()).toFixed(2) ;
          $.ajax({
              url: "modelo/busca_hilo.php",
              method: "POST",
              data: { idhilo : idhilo_var },
              dataType: "json",
              success: function(data){

                $('input[id=hilos]').val(data.descripcion) ;
                $('input[id=tipo]').val(data.prod) ;
                event.preventDefault();

                $.ajax({
                 url: "modelo/tabla_hilos.php",
                 method: "POST",
                 data: {idhilo : idhilo_var },
                 dataType: "json",
                 success: function(data){
                   $("#contenido").html('');
                   $("#contenido").append("<thead class=\"thead-light\"><tr><th scope=\"col\">Sel.</th><th scope=\"col\">Clave</th><th scope=\"col\">Entrada</th><th scope=\"col\">Lote</th><th scope=\"col\"></th><th scope=\"col\"></th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Conos</th><th scope=\"col\">Tipo</th></tr></thead> <tbody>");
                   /* Vemos que la respuesta no este vacía y sea una arreglo */
                   if(data != null && $.isArray(data)){
                       /* Recorremos tu respuesta con each */
                       var i = 0 ;
                       $.each(data, function(key, value){
                           /* Vamos agregando a nuestra tabla las filas necesarias */
                           $("#contenido").append("<tr><th scope=\"row\" class=\"text-center\"><input data-peso=\""+value.pesoneto+"\" data-bobina=\""+value.bobinas+"\" type=\"checkbox\" value="+value.id+" class=\"mycheck \" name=\"id_ent["+i+"]\"> </th><td>" +
                           value.clave + "</td><td>" + value.entrada + "</td><td>" + value.lote + "</td><td class=\"table-info\">" + value.tarima + "</td><td class=\"table-info\">"+value.presentacion+"</td><td>"+
                           value.pesoneto +"</td><td>"+ value.bobinas +"</td><td>"+value.tipo+"</td></tr>");
                           i++;
                       });
                       $("#contenido").append("</tbody>") ;
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
                alert("No Existe la Clave de Hilo/ Solo debe ingresar Claves de Hilo Producido");
              },
          });
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
              document.getElementById("guardavale").disabled = true ;
              $("#detalle").html('') ;
              $("#totales").html('') ;
            }else if( !$("#existe_detalle0").length ) {
              document.getElementById("guardavale").disabled = false ;
              menu_destino($totalpeso.toFixed(2),$totalbobinas) ;
            }else {
               $('input[id=pesototal]').val($totalpeso.toFixed(2)) ;
               $('input[id=bobinatotal]').val($totalbobinas) ;
            }
        });

        //Funcion para poner el menu de detalle de el vale del hilo
        function menu_destino($tkgs, $tbobina){

            for (var i = 0; i < 1; i++) {
              $("#detalle").append("<div id=\"existe_detalle"+i+"\" class=\"row no-gutters\">"+
              "<div class=\"col-lg-2\">"+
                "<div class=\"form-group\">" +
                  "<label>Bobina</label>"+
                  "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][bobinas]\" value=\""+$tbobina+"\" class=\"form-control\" required/>"+
                "</div>"+
              "</div>"+
                "<div class=\"col-lg-3\">" +
                  "<div class=\"form-group\">"+
                    "<label>Destino</label>"+
                    "<select class=\"form-control\" data-id=\""+i+"\" id=\"destino_detalle\" name=\"detalle["+i+"][destino]\" required>"+
                      "<option value=\"0\"></option>"+
                      "<option value=\"1\">Urdido</option>"+
                      "<option value=\"2\">Tejido</option>"+
                      "<option value=\"3\">Maquila</option>"+
                      "<option value=\"4\">Torzal</option>"+
                    "</select>"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-lg-4\">"+
                  "<div class=\"form-group\">" +
                    "<label>Tela</label>"+
                    "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][tela]\" class=\"form-control\" required />"+
                  "</div>"+
                "</div>"+
                "<div class=\"col-lg-3\">"+
                  "<div class=\"form-group\">" +
                    "<label>Kgs</label>"+
                    "<input type=\"text\" data-id=\""+i+"\" name=\"detalle["+i+"][kgs]\" value=\""+$tkgs+"\" class=\"form-control\" readonly/>"+
                  "</div>"+
                "</div>"+
              "</div>");
          }

          // Pone los Totales
          $("#totales").append("<div class=\"row\">"+
              "<div class=\"col-lg-6\">"+
                "<div class=\"form-group\">" +
                  "<label>Total Bobinas</label>"+
                    "<input type=\"text\" id=\"bobinatotal\" name=\"bobinatotal\" value=\""+$tbobina+"\" class=\"form-control\"/ readonly>"+
                "</div>"+
              "</div>"+
              "<div class=\"col-lg-6\">"+
                "<div class=\"form-group\">" +
                  "<label>Total Kgs</label>"+
                  "<input type=\"text\" id=\"pesototal\" name=\"pesototal\" value=\""+$tkgs+"\" class=\"form-control\"/ readonly>"+
                "</div>"+
              "</div>"+
            "</div>");
        }

        //Funcion para validar que el el detallado de KGS se cambio
        $("#detalle").on('change', ':text[name^="detalle["][name$="][bobinas]"]', function(data) {
          var $kgs_sel  = parseFloat($('input[id=pesototal]').val()) ;
          var $bobi_sel = parseInt($('input[id=bobinatotal]').val()) ;
          var $puso_bobina = parseFloat($(this).val()) ;

          document.getElementById("guardavale").disabled = true;

          $(':text[ name^="detalle['+$(this).attr('data-id')+'][kgs]" ]').val(($puso_bobina*$kgs_sel)/$bobi_sel) ;

          var $totalagregado = 0 ;
          var $haybobinas_vacias = 0 ;
          var $lista_kgs = $(':text[name^="detalle["][name$="][bobinas]"]') ;
          var $eliminar_detalles = 0 ;

          $.each( $lista_kgs, function( key, val ) {
              $valor_contiene = parseFloat($('input[name="detalle\['+key+'\]\[bobinas\]"]').val())

              if ($eliminar_detalles === 1){
                $("#existe_detalle"+key).remove() ;
              }else{
                $totalagregado = $totalagregado  + $valor_contiene  ;
                // Verifico si uno de los campos de kgs tiene un 0 para no agregar otro campo detalle
                if ( $valor_contiene === 0){
                  $haybobinas_vacias = key ;
                }else if($totalagregado === $bobi_sel){
                  // Verifica si ya se llego al total de kilos
                  $eliminar_detalles = 1 ;
                  document.getElementById("guardavale").disabled = false;
                }else if($totalagregado > $bobi_sel){
                  alert("No se puede Exceder de mas de "+$bobi_sel+" Bobinas");
                  $('input[name="detalle\['+key+'\]\[bobinas\]"]').val($valor_contiene - ($totalagregado-$bobi_sel))
                  $haybobinas_vacias = key ;
                  $eliminar_detalles = 1 ;
                }
              }
          });

          if( ($totalagregado < $bobi_sel) && ($haybobinas_vacias === 0) ){
            $("#detalle").append("<div id=\"existe_detalle"+(parseInt($(this).attr('data-id'))+1)+"\" class=\"row no-gutters\" >"+
              "<div class=\"col-lg-2\">"+
                "<div class=\"form-group\">" +
                  "<label>Bobina</label>"+
                  "<input type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][bobinas]\"  class=\"form-control\" required/>"+
                "</div>"+
              "</div>"+
              "<div class=\"col-lg-3\">" +
                "<div class=\"form-group\">"+
                  "<label>Destino</label>"+
                  "<select class=\"form-control\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" id=\"destino_detalle\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][destino]\" required>"+
                    "<option value=\"0\"></option>"+
                    "<option value=\"1\">Urdido</option>"+
                    "<option value=\"2\">Tejido</option>"+
                    "<option value=\"3\">Maquila</option>"+
                    "<option value=\"4\">Torzal</option>"+
                  "</select>"+
                "</div>"+
              "</div>"+
              "<div class=\"col-lg-4\">"+
                "<div class=\"form-group\">" +
                  "<label>Tela</label>"+
                  "<input type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][tela]\" class=\"form-control\" required/>"+
                "</div>"+
              "</div>"+
              "<div class=\"col-lg-3\">"+
                "<div class=\"form-group\">" +
                  "<label>Kgs</label>"+
                  "<input type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][kgs]\" value=\"0\" class=\"detalle-kgs form-control\" readonly required/>"+
                "</div>"+
              "</div>"+
            "</div>");
          }else if($totalagregado === $bobi_sel){
            $("#existe_detalle"+$haykgs_vacio).remove();
          }

        });


        // Escrip de Autocompletar
        // retrieve JSon from external url and load the data inside an array :
        /* var lista_hilos = [];
        $.getJSON( "modelo/autocompletar.php", function( data ) {
          $.each( data, function( key, val ) {
            lista_hilos.push(val.label);
          });
        });

        $("#hilos").autocomplete({
          source: lista_hilos,
          select: function( event, ui ) {
            event.preventDefault();
            alert(ui.item.label);
          }
        });
        */


        $( "#hilos" ).autocomplete({
          source: function( request, response ) {
            console.log(request.term);
            $.ajax( {
              url: "modelo/autocompletar.php",
              dataType: "json",
              data: {
                term: request.term
              },
              success: function( data ) {
                response( data );
              }
            } );
          },
          minLength: 4,
          autoFocus: true,
          select: function( event, ui ) {
            $("#clave_hilo").val(ui.item.value);
            $("#clave_hilo").trigger("change");
            //console.log( "Selected: " + ui.item.value + " aka " + ui.item.label );
          }
        } );



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
