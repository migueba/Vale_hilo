var hilos = new Array();
var peso = new Array();
var bobinas = new Array();

$.getJSON( "modelo/inventario.php?function=inventario_hilo",
  function( data ) {
    $.each( data, function( key, val ) {
      hilos.push(val.descripcion.trim());
      peso.push(parseFloat(val.pesoneto_cal));
      bobinas.push(parseInt(val.bobinas));
    });

    var ctx = document.getElementById('myAreaChart').getContext('2d');
    var config = {
       type: 'bar',
       data: {
          labels: hilos ,
          datasets: [{
             data: peso ,
             label: 'Peso',
             backgroundColor: 'rgba(0, 119, 204, 0.3)',
             fill: false
          },{
            data: bobinas,
            label: "Bobinas",
            backgroundColor: '#CEDBB4',
            fill: false
          }
        ]
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
