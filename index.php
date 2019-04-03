<!DOCTYPE html>

<html lang="es">

<head>
<title>Vale de Hilo</title>
<meta charset="utf-8" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script >
      $( function() {

        var lista_hilos = [];
        // retrieve JSon from external url and load the data inside an array :
        $.getJSON( "autocompletar.php", function( data ) {
          $.each( data, function( key, val ) {
            lista_hilos.push(val);
          });
        });

        $( "#hilos" ).autocomplete({
          source: lista_hilos
        });
     } );
    </script>
</head>

<body>
    <div class="container">
        <h1 class="page-header">Vale de Hilo</h1>
        <form method="POST" action="procesa.php">

          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                  <label>Turno</label>
                  <select class="form-control" id="turno">
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                  </select>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="fecha" class="form-control" />
              </div>
            </div>
            <div class="col-xs-1">
              <div class="form-group">
                  <label>ID</label>
                  <input type="text" id="idsupervisor" class="form-control" />
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                  <label>Supervisor</label>
                  <input type="text" id="supervisor" class="form-control" />
              </div>
            </div>
            <div class="col-xs-2">
              <div class="form-group">
                  <label>Destino</label>
                  <select class="form-control" id="destino">
                      <option value="1">Urdido</option>
                      <option value="2">Tejido</option>
                      <option value="3">Maquila</option>
                  </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-1">
              <label>ID</label>
              <input type="text" id="clave_hilo" value="0" class="form-control" disabled/>
            </div>
            <div class="col-xs-7">
              <label>Hilo</label>
              <input type="text" id="hilos" class="form-control" />
            </div>
          </div>

          <div class="btn-group">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </div>

        </form>
    </div>
</body>
</html>
