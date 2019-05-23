$( function() {
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
  }

  $(".row").on('change', '#julios', function(data) {
    var comprobar = isNaN(parseInt($("#numeros").val())) ? 0 : parseInt($("#numeros").val());
    var bobina = isNaN(parseInt($("#bobinas").val())) ? 0 : parseInt($("#bobinas").val());

    if(comprobar !== 0){
      mostrar_lista(parseInt($(this).val()), comprobar, bobina);
    }
  });

  $(".row").on('change', '#numeros', function(data) {
    var comprobar = isNaN(parseInt($("#julios").val())) ? 0 : parseInt($("#julios").val());
    var bobina = isNaN(parseInt($("#bobinas").val())) ? 0 : parseInt($("#bobinas").val());

    if(comprobar !== 0){
      mostrar_lista(comprobar, parseInt($(this).val()), bobina);
    }
  });

  $(".row").on('change', '#bobinas', function(data) {
    $(".detabobina").val($(this).val());
  });

  $(".row").on('change', '.detanumero', function(data) {
  });

});
