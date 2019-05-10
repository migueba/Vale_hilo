$( function() {
  // Busca el Nombre del Hilo usando su Clave
  $(".form-group").on('change', '#clave_hilo', function(event){
    //if (event.which == 13 ) {
      event.preventDefault();
      $('span[data-key=clave_hilo]').html('');
      $("#msn-devolucion").html('');

      $("#contenido").html('');
      $( "#waiting" ).show( "slow" );
      $('#guardavale').prop('disabled', true);
      var idhilo_var = parseFloat($('input[id=clave_hilo]').val()).toFixed(2) ;
      $.ajax({
          url: "modelo/hilo.php",
          method: "GET",
          data: { idhilo : idhilo_var , function : 'hilo_inf'},
          dataType: "json",
        success: function(data){
          $('input[id=hilos]').val(data.descripcion) ;
          $('input[id=tipo]').val(data.prod) ;
          $('input[id=generico]').val(data.generico) ;

          $.ajax({
              url: "modelo/hilo.php",
              method: "GET",
              data: {idhilo : idhilo_var , function : 'hilo_entradas' },
              dataType: "json",
            success: function(data){
              $("#detalle").html('') ;
              $("#totales").html('') ;
              $('#guardavale').prop('disabled', true);

              $("#contenido").html('');
              if ($.trim($('input[id=tipo]').val()) === "COMPRADO"){
                $("#divcontenido").removeClass();
                $("#divcontenido").addClass("col-lg-12 col-lg-12 col-md-12 col-sm-12");

                $("#contenido").append("<thead class=\"thead-light\"><tr><th scope=\"col\">Sel.</th><th scope=\"col\">Clave</th><th scope=\"col\">Fecha</th><th scope=\"col\"></th><th scope=\"col\">Numero</th><th scope=\"col\">Presentacion</th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Conos</th></tr></thead> <tbody>");
              }else{
                $("#divcontenido").removeClass();
                $("#divcontenido").addClass("col-lg-6 col-lg-6 col-md-12 col-sm-12");

                $("#contenido").append("<thead class=\"thead-light\"><tr><th scope=\"col\">Sel.</th><th scope=\"col\">Clave</th><th scope=\"col\">Fecha</th><th scope=\"col\">Lote</th><th scope=\"col\">Tarima</th><th scope=\"col\"></th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Conos</th></tr></thead> <tbody>");
              }
              // Vemos que la respuesta no este vacía y sea una arreglo
              if(data != null && $.isArray(data)){
                // Recorremos tu respuesta con each
                var i = 0 ;

                $.each(data, function(key, value){
                  var $parts = (value.Fecha).split("-") ;
                  // Vamos agregando a nuestra tabla las filas necesarias
                  $("#contenido").append("<tr"+(value.entrada==="DEVOLUCION" ? 'class =\"bg-info text-dark\"':'')+">"+
                    "<th scope=\"row\" class=\"text-center\">"+
                    "<input data-peso=\""+value.pesoneto+"\" data-bobina=\""+value.bobinas+"\" type=\"checkbox\" value="+value.id+" class=\"mycheck \" name=\"id_ent["+i+"]\"> </th>"+
                    "<td>"+value.clave + "</td>"+
                    "<td>"+$parts[2]+"/"+$parts[1]+"/"+$parts[0]+"</td>"+
                    "<td>"+(value.lote === "0" ? '' : value.lote)+"</td>"+
                    "<td>"+value.tarima + "</td>"+
                    "<td>"+value.presentacion+"</td>"+
                    "<td>"+value.pesoneto+"</td>"+
                    "<td>"+ value.bobinas +"</td>"+
                  "</tr>");
                  i++;
                });
                $("#contenido").append("</tbody>") ;
                $("#contenido_tabla").css({"max-height":"350px", "overflow-y":"scroll"});
                $("#msn-devolucion").append("INDICA QUE ES DEVOLUCION") ;
              }
              $( "#waiting" ).hide( "slow" );
            },
            error: function(r) {
              alert("No se puedo establecer Conexión a la Base de Datos");
              $("#waiting").hide("slow");
            },
          });
        },
        error: function(r) {
          $('#clave_hilo').val('');
          $('span[data-key=clave_hilo]').append("No Existe la Clave de Hilo");

          $('input[id=hilos]').val("") ;
          $('input[id=tipo]').val("") ;
          $('input[id=generico]').val("") ;

          $("#contenido").html('');
          $("#contenido_tabla").css({"max-height":"", "overflow-y":""});
          $( "#waiting" ).hide( "slow" );
        },
      });

  //  }
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
        $('#guardavale').prop('disabled', true);
        $("#detalle").html('') ;
        $("#totales").html('') ;
      }else if( !$("#existe_detalle0").length ) {
        $('#guardavale').prop('disabled', false);
        menu_destino($totalpeso.toFixed(2), $totalbobinas, $.trim($('input[id=tipo]').val())) ;

        $('input[name="detalle\[0\]\[cantidad\]"]').val($contador);
      }else {
         $('input[id=pesototal]').val($totalpeso.toFixed(2)) ;
         $('input[id=bobinatotal]').val($totalbobinas) ;

         $('input[name="detalle\[0\]\[cantidad\]"]').val($contador);
         $('input[name="detalle\[0\]\[bobinas\]"]').val($totalbobinas);
         $('input[name="detalle\[0\]\[bobinas\]"]').trigger("change");
      }


  });

  //Funcion para poner el menu de detalle de el vale del hilo
  function menu_destino($tkgs, $tbobina, $tipo_){
        $("#detalle").append("<div id=\"existe_detalle0\" class=\"row no-gutters\">"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Bobina</label>"+
            "<input type=\"text\" data-id=\"0\" name=\"detalle[0][bobinas]\" value=\""+$tbobina+"\" class=\"form-control\" required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Kgs</label>"+
            "<input type=\"text\" data-id=\"0\" name=\"detalle[0][kgs]\" value=\""+$tkgs+"\" class=\"detalle-kgs form-control\" />"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">" +
          "<div class=\"form-group\">"+
            "<label>Destino</label>"+
            "<select class=\"detalle-destino form-control\" data-id=\"0\" name=\"detalle[0][destino]\" required>"+
              "<option disabled selected value></option>"+
              "<option value=\"1\">Urdido</option>"+
              "<option value=\"2\">Tejido</option>"+
              "<option value=\"3\">Maquila</option>"+
              "<option value=\"4\">Torzal</option>"+
            "</select>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Tela</label>"+
            "<input type=\"text\" data-id=\"0\" name=\"detalle[0][tela]\" class=\"form-control \" required />"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Cant. P.</label>"+
            "<input class=\"detalle-cantidad form-control\" type=\"text\" data-id=\"0\" name=\"detalle[0][cantidad]\" required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">" +
          "<div class=\"form-group\">"+
            "<label>Present.</label>"+
            "<select class=\"form-control presenta_detalle\" data-id=\"0\" name=\"detalle[0][presenta]\" required>"+
              "<option disabled selected value></option>"+
              "<option value=\"1\">TARIMA</option>"+
              "<option value=\"2\">BOLSA</option>"+
              "<option value=\"3\">CAJA</option>"+
              "<option value=\"4\">PALET</option>"+
            "</select>"+
          "</div>"+
        "</div>"+
      "</div>");

    if($tipo_ === "COMPRADO"){
      $('.detalle-kgs').prop('readonly', false);
    }else{
      // Si el hilo no es comprado se desabilitan las sigueintes opciones
      $('select option:contains("TARIMA")').prop('selected',true);
      $('.presenta_detalle').prop('readonly', true);
      $('.detalle-cantidad').prop('readonly', true);
      $('.detalle-kgs').prop('readonly', true);
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

  //Funcion para validar que el el detallado de bobinas se cambio
  $("#detalle").on('change', ':text[name^="detalle["][name$="][bobinas]"]', function(data) {

    var $kgs_sel  = parseFloat($('input[id=pesototal]').val()) ;
    var $bobi_sel = parseInt($('input[id=bobinatotal]').val()) ;
    var $puso_bobina =  parseInt($(this).val()) ;
    var $tipo_hilo = $.trim($('input[id=tipo]').val()) ;

    $('#guardavale').prop('disabled', true);

    $(':text[ name^="detalle['+$(this).attr('data-id')+'][kgs]" ]').val( (($puso_bobina*$kgs_sel)/$bobi_sel).toFixed(2) ) ;

    var $totalagregado = 0 ;
    var $haybobinas_vacias = 0 ;
    var $lista_bobinas = $(':text[name^="detalle["][name$="][bobinas]"]') ;
    var $eliminar_detalles = 0 ;

    $.each( $lista_bobinas, function( key, val ) {
        $valor_contiene = parseInt($('input[name="detalle\['+key+'\]\[bobinas\]"]').val())

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
            $('#guardavale').prop('disabled', false);
          }else if($totalagregado > $bobi_sel){
            $('input[name="detalle\['+key+'\]\[bobinas\]"]').val($valor_contiene - ($totalagregado-$bobi_sel)) ;

            $('input[name="detalle\['+key+'\]\[kgs\]"]').val( ((parseInt($('input[name="detalle\['+key+'\]\[bobinas\]"]').val())*$kgs_sel)/$bobi_sel).toFixed(2) ) ;
            $('#guardavale').prop('disabled', false);

            alert("No se puede Exceder de mas de "+$bobi_sel+" Bobinas");

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
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Kgs</label>"+
            "<input type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][kgs]\" value=\"0\" class=\"detalle-kgs form-control\" required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">" +
          "<div class=\"form-group\">"+
            "<label>Destino</label>"+
            "<select class=\"detalle-destino form-control\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" id=\"destino_detalle\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][destino]\" required>"+
              "<option disabled selected value></option>"+
              "<option value=\"1\">Urdido</option>"+
              "<option value=\"2\">Tejido</option>"+
              "<option value=\"3\">Maquila</option>"+
              "<option value=\"4\">Torzal</option>"+
            "</select>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Tela</label>"+
            "<input class=\"form-control\" type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][tela]\" required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">"+
          "<div class=\"form-group\">" +
            "<label>Cant. P.</label>"+
            "<input class=\"detalle-cantidad form-control\" type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][cantidad]\" required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-2\">" +
          "<div class=\"form-group\">"+
            "<label>Present.</label>"+
            "<select data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][presenta]\" class=\"form-control presenta_detalle\" required>"+
              "<option disabled selected value></option>"+
              "<option value=\"1\">TARIMA</option>"+
              "<option value=\"2\">BOLSA</option>"+
              "<option value=\"3\">CAJA</option>"+
              "<option value=\"4\">PALET</option>"+
            "</select>"+
          "</div>"+
        "</div>"+
      "</div>");

      // Pone como Solo lectura a los Kilos cuando es Producido
      if($.trim($('input[id=tipo]').val()) === "COMPRADO"){
        $('.detalle-kgs').prop('readonly', false);
        $('.detalle-cantidad').prop('readonly', false);
      }else{
        $('select option:contains("TARIMA")').prop('selected',true);

        $('.presenta_detalle').prop('readonly', true);
        $('.detalle-kgs').prop('readonly', true);

        $('.detalle-cantidad').val( $('input[name="detalle\[0\]\[cantidad\]"]').val() );
        $('.detalle-cantidad').prop('readonly', true);
      }

    }else if(($totalagregado === $bobi_sel) && ($haybobinas_vacias != 0 )){
      $("#existe_detalle"+$haybobinas_vacias).remove();
    }
  });

  // Funcion para el autocompletar de la tela
  $("#detalle").on('change', '.detalle-destino', function(data) {
    var $posicion_ = $(this).attr('data-id') ;
    var $destino_sel = $(this).val() ;
    var $general_hilo = $.trim($('#generico').val()) ;

    $('#telasdestino'+$posicion_).remove();
    $('#existe_detalle'+$posicion_).append('<datalist id="telasdestino'+$posicion_+'"></datalist>');
    $.getJSON( "modelo/telas_autocompleta.php",{destino : $destino_sel, general : $general_hilo},
      function( data ) {
        $.each( data, function( key, val ) {
          $('#telasdestino'+$posicion_).append('<option>'+val+'</option>');
        });
      }
    );
    $('input[name="detalle\['+$posicion_+'\]\[tela\]"]').attr("list", "telasdestino"+$posicion_);
  });


  // AutoComplete en la lista de telas
  $("#hilos").autocomplete({
    source: function( request, response ) {
      $.ajax( {
        url: "modelo/hilo.php",
        dataType: "json",
        data: {
          term: request.term ,
          function : 'hilo_nombre'
        },
        success: function( data ) {
          response( data );
        }
      } );
    },
    minLength: 3,
    autoFocus: true,
    select: function( event, ui ) {
      $("#clave_hilo").val(ui.item.value);
      $("#clave_hilo").trigger("change");
      //console.log( "Selected: " + ui.item.value + " aka " + ui.item.label );
    },
    change: function (event, ui) {
      if (ui.item === null) {
        $(this).val('');
      }
    }
  } );

  /////// Cuando preciona el Boton de Guardar al Formulario
  var form = $("#formulario");
  form.submit(function(){
      event.preventDefault();
      form.find('.badge-danger').text('');
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
            window.open('http://192.168.1.13/vale_hilo/modelo/ver_vale.php?id_vale='+r.errors['id_vale'], '_blank');
            location.reload();
          },
          error: function(xhr, textStatus, errorThrown){
            alert(xhr.status);
            console.log(textStatus);
            console.log(errorThrown);
          }
      });
      return false;
  });

} );