var $hilos = new Array();
$.getJSON( "modelo/inventario.php?function=inventario_hilo",
  function( data ) {
    $.each( data, function( key, val ) {
      console.log(val.descripcion);
      console.log(val.pesoneto_cal);
    });
  }
);

var ctx = document.getElementById('myAreaChart').getContext('2d');
var config = {
   type: 'line',
   data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
         label: 'Graph Line',
         data: [12, 19, 3, 5, 2, 3],
         backgroundColor: 'rgba(0, 119, 204, 0.3)'
      }]
   }
};
var chart = new Chart(ctx, config);
