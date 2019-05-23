var hilos = new Array();
var peso = new Array();
$.getJSON( "modelo/inventario.php?function=inventario_hilo",
  function( data ) {
    $.each( data, function( key, val ) {
      hilos.push(val.descripcion.trim());
      peso.push(parseFloat(val.pesoneto_cal));
    });

    var ctx = document.getElementById('myAreaChart').getContext('2d');
    var config = {
       type: 'bar',
       data: {
          labels: hilos ,
          datasets: [{
             label: 'Inventario del Almacen de Hilo',
             data: peso ,
             backgroundColor: 'rgba(0, 119, 204, 0.3)',
             borderWidth: 1
          }]
       },
       options: {
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                }
            }
        }
      }
    };
    var chart = new Chart(ctx, config);
  }
);
