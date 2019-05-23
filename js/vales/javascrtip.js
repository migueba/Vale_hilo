$( function() {

  $( "#waiting" ).show( "slow" );
  $.ajax({
      url: "modelo/vale.php",
      method: "GET",
      data: { function : 'lista_vale'},
      dataType: "json",
    success: function(data){
      $("#contenido").html('');
      $("#contenido").append("<thead class=\"thead-light\"><tr><th scope=\"col\">ID VALE</th><th scope=\"col\">Hilo</th><th scope=\"col\">Fecha</th><th scope=\"col\">Turno</th><th scope=\"col\">Superv.</th><th scope=\"col\">Bobinas</th><th scope=\"col\">Peso Neto</th><th scope=\"col\">Estado</th><th scope=\"col\"></th><th scope=\"col\"></th></th><th scope=\"col\"></th></tr></thead> <tbody>");
      // Vemos que la respuesta no este vacía y sea una arreglo
      if(data != null && $.isArray(data)){
        // Recorremos tu respuesta con each
        var i = 0 ;
        $.each(data, function(key, value){
        // Vamos agregando a nuestra tabla las filas necesarias
        var $fechapre = (value.fecha).split('-');
        $("#contenido").append("<tr>"+
            "<td>"+value.vale+"</td>"+
            "<td>"+value.hilo+"</td>"+
            "<td>"+$fechapre[2]+"/"+$fechapre[1]+"/"+$fechapre[0]+"</td>"+
            "<td>"+value.turno+"</td>"+
            "<td>"+value.supervisor+"</td>"+
            "<td>"+value.bobinas+"</td>"+
            "<td>"+value.kilos + "</td>"+
            "<td>"+value.estado+"</td>"+
            "<td><img id-vale=\""+value.vale+"\" class=\"clickojo\" src=\"images/ojo.png\" /></td>"+
            "<td><img class=\"clickcarta\" src=\"images/escritura.png\"/></td>"+
            "<td><img class=\"clickcancel\" src=\"images/cancelar.png\"/></td>"+
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
    window.open('modelo/ver_vale.html?id_vale='+id_vale, '_blank');
  });

});
