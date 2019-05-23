$(document).ready(function() {
    $('#contenido"').DataTable( {
        "ajax": "modelo/vale.php?function=lista_vale",
        "columns": [
            { "data": "vale" },
            { "data": "hilo" },
            { "data": "fecha" },
            { "data": "turno" },
            { "data": "supervisor" },
            { "data": "estado" }
        ]
    } );
} );
