$( function() {

  $( "#waiting" ).show( "slow" );
  $.ajax({
      url: "modelo/vale.php",
      method: "GET",
      data: { function : 'lista_vale'},
      dataType: "json",
    success: function(data){
      $("#contenido").html('');
      $("#contenido").append("<thead class=\"thead-light\">"+
        "<tr>"+
          "<th scope=\"col\">Sel.</th>"+
          "<th scope=\"col\">VALE</th>"+
          "<th scope=\"col\">Hilo</th>"+
          "<th scope=\"col\"></th>"+
          "<th scope=\"col\">Fecha</th>"+
          "<th scope=\"col\">Turno</th>"+
          "<th scope=\"col\">Supervisor</th>"+
          "<th scope=\"col\">Bobinas</th>"+
          "<th scope=\"col\">Kilos</th>"+
          "<th scope=\"col\">Estado</th>"+
          "<th scope=\"col\"></th>"+
          "<th scope=\"col\"></th>"+
        "</tr>"+
      "</thead> <tbody>");
      // Vemos que la respuesta no este vacía y sea una arreglo
      if(data != null && $.isArray(data)){
        var i = 0 ;
        $.each(data, function(key, value){
          // Vamos agregando a nuestra tabla las filas necesarias
          var $fechapre = (value.fecha).split('-');
          $("#contenido").append("<tr>"+
              "<td>"+
                "<input data-i=\""+i+"\" type=\"checkbox\" value="+value.vale+" class=\"mycheck form-control\" name=\"vale_sel["+i+"][id]\"> "+
              "</td>"+
              "<td>"+value.vale+"</td>"+
              "<td>"+value.hilo+"</td>"+
              "<td>"+$.trim(value.nombre)+"</td>"+
              "<td>"+$fechapre[2]+"/"+$fechapre[1]+"/"+$fechapre[0]+"</td>"+
              "<td>"+value.turno+"</td>"+
              "<td>"+$.trim(value.supervisor)+"</td>"+
              "<td>"+value.bobinas+"</td>"+
              "<td>"+value.kilos + "</td>"+
              "<td>"+value.estado+"</td>"+
              "<td><img id-vale=\""+value.vale+"\" class=\"clickojo\" src=\"images/ojo.png\" /></td>"+
              "<td><img id-vale=\""+value.vale+"\" class=\"clickcancel\" src=\"images/cancelar.png\"/></td>"+
            "</tr>");
            i++;
        });
        $("#contenido").append("</tbody>") ;
        //$("#contenido_tabla").css({"max-height":"350px", "overflow-y":"scroll"});
      }
      $( "#waiting" ).hide( "slow" );

      $('#contenido').DataTable({
          responsive: true,
          language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
          }
      });
    },
    error: function(r) {
      alert("Ocurrio un Incoveniente con la BD");
      $( "#waiting" ).hide( "slow" );
    },
  });

  $("#contenido").on('click', '.clickojo', function(data) {
    var id_vale = $(this).attr('id-vale') ;
    window.open('modelo/ver_vale.html?id_vale='+id_vale, '_blank');
  });

  $("#contenido").on('click', '.clickcancel', function(data) {
    var id_vale = $(this).attr('id-vale') ;
    var r = confirm("Esta seguro de eliminar el Vale Nº "+id_vale);
    if (r == true) {
      $.ajax({
          url: "modelo/vale.php",
          method: "GET",
          data: { function : 'cancelar_vale', id_vale: id_vale},
          dataType: "text",
        success: function(data){
          alert(data);
          location.reload();
        },
      });
    }
  });



});
