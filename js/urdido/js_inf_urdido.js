$(function() {

/////// Cuando preciona el Boton de Guardar al Formulario
  var form = $("#formulario");
  form.submit(function(){
      event.preventDefault();
      $.ajax({
          url: "modelo/urdido.php",
          method: "GET",
          data: form.serialize() + "&function=modificar_urdido",
          dataType: "text",
          success: function(r){
             alert(r);
          },
          error: function(xhr, status, error) {
            alert("Entro a error");
            alert(xhr.responseText);
          }
      });
    return false;
  });

});