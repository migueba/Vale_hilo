<!DOCTYPE html>

<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Vale de Hilo</title>
  </style>
  <meta charset="utf-8" />
  <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
  <script src="bootstrap.min.js"></script>
  <!--<link rel="stylesheet" type="text/css" href="css/bootstrap3-3-7-theme.min.css">
  <link rel="stylesheet" type="text/css" href="css/padding.css">-->

  <script src="jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
  <link rel="stylesheet" type="text/css" href="css/jquery-ui.structure.min.css">

  <script src="js/jquery-3.4.0.js"></script>
</head>

<body>
    <div class="container">
        <h2 class="page-header">Vale de Hilo</h2>
        <form method="POST" id="formulario" action="modelo/procesa.php">
          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                  <label>Turno</label>
                  <select class="form-control" id="turno" name="turno" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                  </select>
                  <span data-key="turno" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-3">
              <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" id="fecha" class="form-control" name="fecha" required/>
                  <span data-key="fecha" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-1">
              <div class="form-group">
                  <label>ID</label>
                  <input type="text" id="idsupervisor" class="form-control" name="idsupervisor" required/>
                  <span data-key="idsupervisor" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-7">
              <div class="form-group">
                  <label>Supervisor</label>
                  <input type="text" id="supervisor" name="supervisor" class="form-control" />
                  <span data-key="supervisor" class="label label-danger"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-1">
              <div class="form-group">
                <label>Clave</label>
                <input type="text" id="clave_hilo" name="clave_hilo" class="form-control" required/>
                <span data-key="clave_hilo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-5">
              <div class="form-group">
                <label>Hilo</label>
                <input type="text" id="hilos" name="hilos" class="form-control auto-widget" required/>
                <span data-key="hilos" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-2">
              <div class="form-group">
                <label>Titulo</label>
                <input type="text" id="titulo" name="titulo" class="form-control auto-widget" required/>
                <span data-key="titulo" class="label label-danger"></span>
              </div>
            </div>
            <div class="col-xs-4">
              <div class="form-group" id="totales">
              </div>
            </div>
          </div>

          <div class="row no-pad" >
            <div class="col-xs-7">
              <div class="form-group" id="contenido_tabla" style="">
                <table id="contenido" class="table table-bordered table-hover table-sm"></table>
                <span data-key="id_ent" class="label label-danger"></span>
              </div>
            </div>

            <div class="col-xs-5">
              <!--<div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;"> -->
              <div id="detalle" class="form-group" style="max-height: 350px;overflow-y: scroll;">
              </div>
              <span data-key="detalle" class="label label-danger"></span>
            </div>
          </div>

          <div class="btn-group">
              <button type="submit" id="guardavale" class="btn btn-primary" disabled>Guardar</button>
          </div>

        </form>
    </div>
  </body>
  </html>
