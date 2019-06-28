$( function() {
  $('#sidebarToggle').click();

  // Busca el Nombre del Hilo usando su Clave
  $(".form-group").on('focusout', '#clave_hilo', function(event){
    event.preventDefault();

    var valida_fecha = $("#fecha").val() ;
    var fecha2 = valida_fecha.split("-") ;
    valida_fecha = fecha2[0]+fecha2[1]+fecha2[2] ;

    if (valida_fecha.length === 0 ){
      if ( $(this).val() != 0 ){
          $(this).val(0) ;
          $('input[id=hilos]').val("") ;
          alert("Es necesario Ingresar Primero la FECHA") ;
      }
    }else{
      $('span[data-key=clave_hilo]').html('') ;
      $("#msn-devolucion").html('') ;
      $("#contenido").html('') ;
      $( "#waiting" ).show( "slow" ) ;
      $('#guardavale').prop('disabled', true) ;
      var idhilo_var = parseFloat($('input[id=clave_hilo]').val()).toFixed(2) ;

      $.ajax({
        url: "modelo/hilo.php",
        method: "GET",
        data: { idhilo : idhilo_var , function : 'hilo_inf' },
        dataType: "json",
        success: function(data){
          $('input[id=hilos]').val(data.descripcion) ;
          $('input[id=tipo]').val(data.prod) ;
          $('input[id=generico]').val(data.generico) ;

          $.ajax({
            url: "modelo/hilo.php",
            method: "GET",
            data: {idhilo : idhilo_var , function : 'hilo_entradas', fecha_v : valida_fecha  },
            dataType: "json",
            success: function(data){
              $("#detalle").html('') ;
              $("#totales").html('') ;
              $("#contenido").html('');
              $('#guardavale').prop('disabled', true);

              if ($.trim($('input[id=tipo]').val()) === "COMPRADO"){
                $("#divcontenido").removeClass();
                $("#divdetalle").removeClass();
                $("#divcontenido").addClass("col-xl-8 col-lg-12 col-md-12 col-sm-12");
                $("#divdetalle").addClass("col-xl-4 col-lg-12 col-md-12 col-sm-12");

                $("#contenido").append("<thead class=\"thead-light\">"+
                  "<tr>"+
                    "<th scope=\"col\">Sel.</th>"+
                    "<th scope=\"col\">Fecha</th>"+
                    "<th scope=\"col\">#</th>"+
                    "<th scope=\"col\"></th>"+
                    "<th scope=\"col\">Kg. Neto</th>"+
                    "<th scope=\"col\">Conos</th>"+
                    "<th scope=\"col\"></th>"+
                    "<th scope=\"col\">Cantidad</th>"+
                    "<th scope=\"col\"></th>"+
                    "<th scope=\"col\">Bobinas</th>"+
                    "<th scope=\"col\"></th>"+
                    "<th scope=\"col\">Kgs</th>"+
                  "</tr>"+
                "</thead> "+
                "<tbody>");
              }else{
                $("#divcontenido").removeClass();
                $("#divdetalle").removeClass();
                $("#divcontenido").addClass("col-xl-6 col-lg-6 col-md-12 col-sm-12");
                $("#divdetalle").addClass("col-xl-6 col-lg-6 col-md-12 col-sm-12");

                $("#contenido").append("<thead class=\"thead-light\">"+
                  "<tr>"+
                    "<th scope=\"col\">Sel.</th>"+
                    "<th scope=\"col\">Fecha</th>"+
                    "<th scope=\"col\">Lote</th>"+
                    "<th scope=\"col\">Tarima</th>"+
                    "<th scope=\"col\"></th>"+
                    "<th scope=\"col\">Kg. Neto</th>"+
                    "<th scope=\"col\">Conos</th>"+
                  "</tr>"+
                "</thead> "+
                "<tbody>");
              }
              // Vemos que la respuesta no este vacía y sea una arreglo
              if(data != null && $.isArray(data)){
                var i = 0 ;
                $.each(data, function(key, value){
                  var parts = (value.Fecha).split("-") ;
                  // Vamos agregando a nuestra tabla las filas necesarias
                  if ($.trim($('input[id=tipo]').val()) === "COMPRADO"){
                    $("#contenido").append("<tr "+(value.entrada==="DEVOLUCION" ? 'class =\"bg-info text-dark\"':'')+" >"+
                      "<th scope=\"row\" class=\"text-center\">"+
                        "<input data-i=\""+i+"\" data-peso=\""+value.pesoneto+"\" data-bobina=\""+value.bobinas+"\" type=\"checkbox\" value="+value.id+" class=\"mycheck form-control\" name=\"detallecomprado["+i+"][id]\"> "+
                      "</th>"+
                      "<td>"+parts[2]+"/"+parts[1]+"/"+parts[0]+"</td>"+
                      "<td class=\"text-right font-weight-bold\">"+value.tarima + "</td>"+
                      "<td>"+value.presentacion+"</td>"+
                      "<td class=\"font-weight-bold\">"+value.pesoneto+"</td>"+
                      "<td class=\"text-center\">"+ value.bobinas +"</td>"+
                      "<td></td>"+
                      "<td>"+
                        "<input type=\"text\" data-P=\""+i+"\" data-maximo=\""+value.tarima+"\" id=\"CPresenta"+i+"\" class=\"form-control input-sm\" readonly />"+
                      "</td>"+
                      "<td class=\"text-left\">"+
                        "<input type=\"text\" id=\"Presenta"+i+"\" class=\"form-control\" value=\""+value.presentacion+"\" readonly />"+
                      "</td>"+
                      "<td>"+
                        "<input type=\"text\" data-B=\""+i+"\" data-bobinas=\""+value.bobinas+"\" data-kilos=\""+value.pesoneto+"\" value=\"0\" id=\"BPresenta"+i+"\" class=\"form-control\" readonly />"+
                      "</td>"+
                      "<td class=\"text-left\">Bobinas</td>"+
                      "<td>"+
                        "<input type=\"text\" data-K=\""+i+"\" id=\"KPresenta"+i+"\" value=\"0\" class=\"form-control\" readonly />"+
                      "</td>"+
                    "</tr>");
                  }else{
                    $("#contenido").append("<tr "+(value.entrada==="DEVOLUCION" ? 'class =\"bg-info text-dark\"':'')+" >"+
                      "<th scope=\"row\" class=\"text-center\">"+
                        "<input data-i=\""+i+"\" data-peso=\""+value.pesoneto+"\" data-bobina=\""+value.bobinas+"\" type=\"checkbox\" value="+value.id+" class=\"mycheck form-control\" name=\"id_ent["+i+"][id]\"> "+
                      "</th>"+
                      "<td>"+parts[2]+"/"+parts[1]+"/"+parts[0]+"</td>"+
                      "<td>"+(value.lote === "0" ? '' : value.lote)+"</td>"+
                      "<td>"+value.tarima + "</td>"+
                      "<td>"+value.presentacion+"</td>"+
                      "<td>"+value.pesoneto+"</td>"+
                      "<td>"+ value.bobinas +"</td>"+
                    " </tr>");
                  }
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

          if ($.trim($('input[id=tipo]').val()) === "COMPRADO"){
            $('#Presenta'+$(this).attr('data-i')).attr("name","detallecomprado["+$(this).attr('data-i')+"][Presenta]") ;
            $('#CPresenta'+$(this).attr('data-i')).attr("name","detallecomprado["+$(this).attr('data-i')+"][cantidadP]") ;
            $('#BPresenta'+$(this).attr('data-i')).attr("name","detallecomprado["+$(this).attr('data-i')+"][cantidadB]") ;
            $('#KPresenta'+$(this).attr('data-i')).attr("name","detallecomprado["+$(this).attr('data-i')+"][cantidadK]") ;

            $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadP\]"]').prop('readOnly', false);
            $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').prop('required', true);
            $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').prop('readOnly', false);

          }
          //return false; Sirve para salir el each
        }else if($.trim($('input[id=tipo]').val()) === "COMPRADO"){
          //$('input[data-P='+$(this).attr('data-i')+']').prop('required', false);
          resultado_pesott =$('input[id=pesototal]').val() - $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadK\]"]').val()
          resultado_bobitt = $('input[id=bobinatotal]').val() - $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').val()

          $('input[id=pesototal]').val( resultado_pesott.toFixed(2) ) ;
          $('input[id=bobinatotal]').val( resultado_bobitt.toFixed(2) ) ;

          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadP\]"]').prop('readOnly', true);
          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadP\]"]').val(0);

          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').prop('required', false);
          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').prop('readOnly', true);
          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadB\]"]').val(0);

          $('input[name="detallecomprado\['+$(this).attr('data-i')+'\]\[cantidadK\]"]').val(0);

          $('#Presenta'+$(this).attr('data-i')).attr("name","") ;
          $('#CPresenta'+$(this).attr('data-i')).attr("name","") ;
          $('#BPresenta'+$(this).attr('data-i')).attr("name","") ;
          $('#KPresenta'+$(this).attr('data-i')).attr("name","") ;

        }
      });

      if ($.trim($('input[id=tipo]').val()) === "COMPRADO"){
        if ($contador === 0){
          $('#guardavale').prop('disabled', true);
          $("#detalle").html('') ;
          $("#totales").html('') ;

        }else if( !$('input[id=pesototal]').length ){
          $('#guardavale').prop('disabled', true);
          menu_destino($totalpeso.toFixed(2), $totalbobinas, $.trim($('input[id=tipo]').val())) ;
        }
      }else{
        if ($contador === 0){
          $('#guardavale').prop('disabled', true);
          $("#detalle").html('') ;
          $("#totales").html('') ;

        }else if( !$("#existe_detalle0").length ) {
          $('#guardavale').prop('disabled', false);
          menu_destino($totalpeso.toFixed(2), $totalbobinas, $.trim($('input[id=tipo]').val())) ;
        }else {
           $('input[id=pesototal]').val($totalpeso.toFixed(2)) ;
           $('input[id=bobinatotal]').val($totalbobinas) ;

           $('input[name="detalle\[0\]\[bobinas\]"]').val($totalbobinas);
           $('input[name="detalle\[0\]\[bobinas\]"]').trigger("change");
        }
      }

  });

  //Funcion para poner el menu de detalle de el vale del hilo
  function menu_destino($tkgs, $tbobina, $tipo_){
    $("#detalle").append("<div id=\"existe_detalle0\" class=\"row no-gutters\">"+
      "<div class=\"col-lg-2\">"+
        "<div class=\"form-group\">" +
          "<label>Bobina</label>"+
          "<input type=\"text\" data-id=\"0\" name=\"detalle[0][bobinas]\" value=\""+($tipo_==="COMPRADO" ? 0 : $tbobina)+"\" class=\"form-control\" required/>"+
        "</div>"+
      "</div>"+
      "<div class=\"col-lg-3\">"+
        "<div class=\"form-group\">" +
          "<label>Kgs</label>"+
          "<input type=\"text\" data-id=\"0\" name=\"detalle[0][kgs]\" value=\""+($tipo_==="COMPRADO" ? 0 : $tkgs)+"\" class=\"detalle-kgs form-control\" readonly/>"+
        "</div>"+
      "</div>"+
      "<div class=\"col-lg-4\">" +
        "<div class=\"form-group\">"+
          "<label>Destino</label>"+
          "<select class=\"detalle-destino form-control\" data-id=\"0\" name=\"detalle[0][destino]\" required>"+
            "<option disabled selected value></option>"+
            "<option value=\"2\">Urdido</option>"+
            "<option value=\"3\">Tejido</option>"+
            "<option value=\"5\">Maquila</option>"+
            "<option value=\"7\">Torzal</option>"+
          "</select>"+
        "</div>"+
      "</div>"+
      "<div class=\"col-lg-3\">"+
        "<div class=\"form-group\">" +
          "<label>Tela</label>"+
          "<input type=\"text\" data-id=\"0\" name=\"detalle[0][tela]\" class=\"form-control \" onkeyup=\"javascript:this.value=this.value.toUpperCase();\" required />"+
        "</div>"+
      "</div>"+
    "</div>");

    // Pone los Totales
    $("#totales").append("<div class=\"row\">"+
        "<div class=\"col-lg-6\">"+
          "<div class=\"form-group\">" +
            "<label>Total Bobinas</label>"+
              "<input type=\"text\" id=\"bobinatotal\" name=\"bobinatotal\" value=\""+($tipo_ === "COMPRADO" ? 0 : $tbobina)+"\" class=\"form-control\"/ readonly>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-6\">"+
          "<div class=\"form-group\">" +
            "<label>Total Kgs</label>"+
            "<input type=\"text\" id=\"pesototal\" name=\"pesototal\" value=\""+($tipo_ === "COMPRADO" ? 0 : $tkgs)+"\" class=\"form-control\"/ readonly>"+
          "</div>"+
        "</div>"+
      "</div>");
  }

  //Funcion para validar que el el detallado de bobinas se cambio en Hilo PRODUCIDO
  $("#contenido").on('change', ':text[name^="detallecomprado["][name$="][cantidadB]"]', function(data) {
    var $bobi_sel  = parseInt($(this).val()) ;

    if($bobi_sel > $(this).attr('data-bobinas')){
      alert('No Puede poner una Cantidad de bobinas Mayor a la que Existe, Se pondra la Cantidad Maxima que se puede poner');
      $bobi_sel = parseInt($(this).attr('data-bobinas')) ;
    }

    if($bobi_sel < 0){
      alert('No puede Poner un Valor menor a 0, Por defecto se pondra 0');
      $bobi_sel = 0;
    }

    var $kgs_sel  = parseFloat(($bobi_sel*$(this).attr('data-kilos'))/ $(this).attr('data-bobinas'));
    $(':text[ name^="detallecomprado['+$(this).attr('data-B')+'][cantidadK]" ]').val($kgs_sel);

    var $lista_check = $('.mycheck');
    var $totalpeso = 0 ;
    var $totalbobinas = 0 ;

    $.each( $lista_check, function( key, val ) {
      var posic_ = $(this).attr('data-i') ;

      if ($(val).is(':checked')){
        $totalpeso = $totalpeso + parseFloat($(':text[ name^="detallecomprado['+posic_+'][cantidadK]" ]').val()) ;
        $totalbobinas = $totalbobinas + parseInt($(':text[ name^="detallecomprado['+posic_+'][cantidadB]" ]').val()) ;
      }
    });

    $('input[id=pesototal]').val($totalpeso.toFixed(2)) ;
    $('input[id=bobinatotal]').val($totalbobinas) ;

    $(':text[ name^="detalle[0][bobinas]" ]').val($totalbobinas);
    $(':text[ name^="detalle[0][kgs]" ]').val($totalpeso.toFixed(2)) ;

    $(':text[ name="detalle[0][bobinas]" ]').trigger("change");
  });

  //Funcion para validar que el el detallado de bobinas se cambio en Hilo PRODUCIDO
  $("#contenido").on('change', ':text[name^="detallecomprado["][name$="][cantidadP]"]', function(data) {
    var $presenta_sel  = parseInt($(this).val()) ;
    var $bobinas_max = parseInt($(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').attr('data-bobinas')) ;
    var $presenta_max = parseInt($(this).attr('data-maximo')) ;
    var $error = 0 ;

    if ( $presenta_sel > $(this).attr('data-maximo') ){
      alert('No Puede poner una Cantidad de Presentacion Mayor a la que Existe, Se pondra la Cantidad Maxima que se puede poner');
      $(this).val($(this).attr('data-maximo'));
      $(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').val($bobinas_max);
      $(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').trigger("change");
      $error = 1 ;
    }

    if ( $presenta_sel < 0 ){
      alert('No puede Poner un Valor menor a 0, Por defecto se pondra 0');
      $(this).val(0);
      $error = 1 ;
    }

    if ($error === 0){
      if ($presenta_max != 0){
        $(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').val(($presenta_sel*$bobinas_max)/$presenta_max);
      }else{
        $(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').val($bobinas_max);
      }
      $(':text[ name^="detallecomprado['+$(this).attr('data-P')+'][cantidadB]" ]').trigger("change");
    }

  });

  //Funcion para validar que el el detallado de bobinas se cambio en Hilo PRODUCIDO
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
        "<div class=\"col-lg-3\">"+
          "<div class=\"form-group\">" +
            "<label>Kgs</label>"+
            "<input type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][kgs]\" value=\"0\" class=\"detalle-kgs form-control\" readOnly required/>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-4\">" +
          "<div class=\"form-group\">"+
            "<label>Destino</label>"+
            "<select class=\"detalle-destino form-control\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" id=\"destino_detalle\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][destino]\" required>"+
              "<option disabled selected value></option>"+
              "<option value=\"2\">Urdido</option>"+
              "<option value=\"3\">Tejido</option>"+
              "<option value=\"5\">Maquila</option>"+
              "<option value=\"7\">Torzal</option>"+
            "</select>"+
          "</div>"+
        "</div>"+
        "<div class=\"col-lg-3\">"+
          "<div class=\"form-group\">" +
            "<label>Tela</label>"+
            "<input class=\"form-control\" type=\"text\" data-id=\""+(parseInt($(this).attr('data-id'))+1)+"\" name=\"detalle["+(parseInt($(this).attr('data-id'))+1)+"][tela]\" onkeyup=\"javascript:this.value=this.value.toUpperCase();\" required/>"+
          "</div>"+
        "</div>"+
      "</div>");

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

  // Complementos del AutoComplete
  $(".form-group").on('focusout', '#idsupervisor', function(event){
    var idemp_ = $(this).val() ;
    $.ajax({
        url: "modelo/usuarios.php",
        dataType: "json",
        data: { idemp : idemp_ , function : 'busca_id'},
        success: function(data){
          $('input[id=supervisor]').val(data.nombre) ;
        }
    });
  });

  // AutoComplete en la lista de Usuarios
  $("#supervisor").autocomplete({
    source: function( request, response ) {
      $.ajax( {
        url: "modelo/usuarios.php",
        dataType: "json",
        data: {
          term: request.term ,
          function : 'lista_usuarios'
        },
        success: function( data ) {
          response( data );
        }
      } );
    },
    minLength: 1,
    autoFocus: true,
    select: function( event, ui ) {
      $("#idsupervisor").val(ui.item.value);
      $("#idsupervisor").trigger("focusout");
    }
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
      $("#clave_hilo").trigger("focusout");
      //console.log( "Selected: " + ui.item.value + " aka " + ui.item.label );
    }
  });

  /////// Cuando preciona el Boton de Guardar al Formulario
  var form = $("#formulario");
  form.submit(function(){
      event.preventDefault();
      form.find('.badge-danger').text('');
      $.ajax({
          url: "modelo/vale.php",
          method: "GET",
          data: form.serialize() + "&function=validar_vale",
          dataType: "json",
          success: function(r){
            if(r.errors['id_vale'] != 0) {
              window.open('http://192.168.1.13/vale_hilo/modelo/ver_vale.html?id_vale='+r.errors['id_vale'], '_blank');
              location.reload();
            }

          },
          error: function(r){
            alert("El error ya se esta corrigiendo "+typeof r);
          }
      });
      return false;
  });

} );
