$( function() {
  /////// Cuando preciona el Boton de Guardar al Formulario
  var form = $("#formulario");
  form.submit(function(){
      event.preventDefault();
      $( "#waiting" ).show( "slow" ) ;
      $.ajax({
          url: "modelo/inf_construccion.php",
          method: "GET",
          data: form.serialize(),
          dataType: "json",
          success: function(r){
            if(r.length > 0) {
              $.each(r, function(key, value){
                $("#inf-vale").html('') ;
                $("#inf-vale").append("<div class=\"row\">"+
                    "<div class=\"col-xl-2 col-lg-2 col-md-2 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>CLAVE TG</label>"+
                        "<input type=\"text\" id=\"clave_tg\" class=\"form-control\" name=\"clave_tg\" value=\""+value.clave+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-6 col-lg-6 col-md-10 col-sm-9\">"+
                      "<div class=\"form-group\">"+
                        "<label>TELA</label>"+
                        "<input type=\"text\" id=\"tela\" class=\"form-control\" name=\"tela\" value=\""+value.tela+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                  "</div>"+
                  "<div class=\"row\">"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Hilo PIE</label>"+
                        "<input type=\"text\" id=\"h_pie\" class=\"form-control\" name=\"h_pie\" value=\""+value.pie+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Hilo TRAMA</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.trama+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Ancho PEINE</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.anchopeine+"\" readonly  />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Hilos PEINE</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.hilopeine+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                  "</div>"+
                  "<div class=\"row\">"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Luchas x Pulg.</label>"+
                        "<input type=\"text\" id=\"h_pie\" class=\"form-control\" name=\"h_pie\" value=\""+value.luchaxpulgada+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Ancho en CM.</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.anchocm+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Pie-Trama-Apre</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.pesopta+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                    "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                      "<div class=\"form-group\">"+
                        "<label>Peso g/m2</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.pesogm2+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                  "</div>"+
                    "<div class=\"row\">"+
                      "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                        "<label>Hilos X Pulg.</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.hxpulg+"\" readonly />"+
                      "</div>"+
                      "<div class=\"col-xl-3 col-lg-3 col-md-3 col-sm-3\">"+
                        "<label>Hilos X Pulg. 2</label>"+
                        "<input type=\"text\" id=\"h_trama\" class=\"form-control\" name=\"h_trama\" value=\""+value.hxpulg2+"\" readonly />"+
                      "</div>"+
                    "</div>"+
                  "</div>");
              });
              $( "#waiting" ).hide( "slow" );
            }else{
              $("#waiting" ).hide( "slow" );
              $("#inf-vale").html('') ;
              $("#inf-vale").append("<span class=\"badge badge-danger\">La tela Ingresada No Existe</span>");
            }
          },
          error: function(r){
            $("#waiting" ).hide( "slow" );
            $("#inf-vale").html('') ;
            alert("No se puede Mostrar la informacion de la Tela");
          }
      });
      return false;
  });
});
