$( function() {

  // Complementos del AutoComplete
  $(".form-group").on('focusout', '#n_oficial', function(event){
    var idemp_ = $(this).val() ;
    $.ajax({
        url: "modelo/usuarios.php",
        dataType: "json",
        data: { idemp : idemp_ , tipoemp : 'OFI' ,  function : 'busca_id'},
        success: function(data){
          $('input[id=oficial]').val(data.nombre) ;
        }
    });
  });

  // AutoComplete en la lista de Usuarios
  $("#oficial").autocomplete({
    source: function( request, response ) {
      $.ajax( {
        url: "modelo/usuarios.php",
        dataType: "json",
        data: {
          term: request.term ,
          tipoemp : 'OFI' ,
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
      $("#n_oficial").val(ui.item.value);
      $("#n_oficial").trigger("focusout");
    }
  });

  // Funcion para Buscar el nombre de un hilo poniendo su CLAVE
  $(".form-group").on('focusout', '#clave_hilo', function(event){
    var idhilo_var = parseFloat($(this).val()).toFixed(2) ;

    $.ajax({
      url: "modelo/hilo.php",
      method: "GET",
      data: { idhilo : idhilo_var, function : 'hilo_inf' },
      dataType: "json",
      success: function(data){
        $('input[id=hilo]').val(data.descripcion) ;
        $('input[id=titulo]').val(data.generico);
        $('input[id=tela]').prop('readOnly', false);

        $('#telasdestino').remove();
        $('#detalle_hilo').append('<datalist id="telasdestino"></datalist>');

        var $general_hilo = $.trim($('#titulo').val()) ;

        $.getJSON( "modelo/telas_autocompleta.php",{destino : 3, general : $general_hilo },
          function( data ) {
            $.each( data, function( key, val ) {
              $('#telasdestino').append('<option>'+val+'</option>');
            });
          }
        );
        $('#tela').attr("list", "telasdestino");
      },
      error: function(r) {
        $('input[id=hilo]').val('') ;
        $('input[id=titulo]').val('');
        $('input[id=tela]').prop('readOnly', true);
        alert('Clave de hilo Incorrecta');

        $('#telasdestino').remove();
      }
    });
  });

  // AutoComplete en la lista de telas
  $("#hilo").autocomplete({
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
      });
    },
    minLength: 3,
    autoFocus: true,
    select: function( event, ui ) {
      $("#clave_hilo").val(ui.item.value);
      $("#clave_hilo").trigger("focusout");
    }
  });

  // Funcion para el autocompletar de la tela
  $("#tela").on('change', '.form-group', function(data) {
    var $destino_sel = 2 ;
    var $general_hilo = $.trim($('#generico').val()) ;

    $('#telasdestino'+$posicion_).remove();
    $('#existe_detalle'+$posicion_).append('<datalist id="telasdestino'+$posicion_+'"></datalist>');
    $.getJSON( "modelo/telas_autocompleta.php",{destino : $destino_sel, general : $general_hilo},
      function(data) {
        $.each( data, function( key, val ) {
          $('#telasdestino'+$posicion_).append('<option>'+val+'</option>');
        });
      }
    );
    $('input[name="detalle\['+$posicion_+'\]\[tela\]"]').attr("list", "telasdestino"+$posicion_);
  });

  // Muestra la Lista de no se que
  function mostrar_lista(julios, numeros, bobinas){
    $("#tablaurdido").html('');

    $("#tablaurdido").append("<thead class=\"thead-light\">"+
      "<tr>"+
        "<th scope=\"col\">Nº Julio</th>"+
        "<th scope=\"col\">Numero</th>"+
        "<th scope=\"col\">Bobina</th>"+
      "</tr>"+
    "</thead> "+
    "<tbody>");

    for(var i = 1; i <= julios; i++){
      $("#tablaurdido").append("<tr>"+
          "<td>"+
            i+
          "</td>"+
          "<td>"+
            "<input type=\"number\" name=\"detalle["+i+"]['numero']\" class=\"form-control detanumero\" value=\""+numeros+"\" required/>"+
          "</td>"+
          "<td>"+
            "<input type=\"number\" name=\"detalle["+i+"]['bobina']\" class=\"form-control detabobina\" value=\""+bobinas+"\" required/>"+
          "</td>"+
        "</tr>");
    }

    $("#tablaurdido").append("</tbody>") ;

    var n_titulo = isNaN(parseInt($("#titulo").val())) ? 0 : parseInt($("#titulo").val());

    // Si alguno de los valores es igual a 0 no entra
    if(julios !== 0 && bobinas !== 0 && numeros !== 0 && n_titulo !== 0){
      var peso_total = (julios * bobinas * numeros * 10 * .59 / n_titulo / 1000);
      $("#ktotales").val(peso_total);
    }

    if(numeros !== 0 && julios !== 0){
      var numertos_t = (numeros*julios);
      $("#ntotales").val(numertos_t);
    }

  }

  // PAra que genere la Tabla cuando cambie los valores especificados
  $(".form-group").on('change', ['#julios', '#numeros', '#bobinas'], function(data) {
    var numero = isNaN(parseInt($("#numeros").val())) ? 0 : parseInt($("#numeros").val());
    var bobina = isNaN(parseInt($("#bobinas").val())) ? 0 : parseInt($("#bobinas").val());
    var julio = isNaN(parseInt($("#julios").val())) ? 0 : parseInt($("#julios").val());

    mostrar_lista(julio, numero, bobina);
  });

  /////// Cuando preciona el Boton de Guardar al Formulario
  var form = $("#formulario");
  form.submit(function(){
      event.preventDefault();
      form.find('.badge-danger').text('');
      $.ajax({
          url: "modelo/urdido.php",
          method: "GET",
          data: form.serialize() + "&function=guarda_urdido",
          dataType: "json",
          success: function(r){
            if(r.errors['id_vale'] != 0) {
              window.open('vale_hilo/modelo/ver_vale.html?id_vale='+r.errors['id_vale'], '_blank');
              location.reload();
            }

          },
          error: function(r){
            alert("No se puedo guardar la Información");
          }
      });
      return false;
  });

});
