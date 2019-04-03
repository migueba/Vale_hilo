<?php

// Primero definimos la conexión a la base de datos, para que se fácil cambiar los parámetros si procede.
define('HOST_DB', 'localhost');  //Nombre del host, nomalmente localhost
define('USER_DB', 'root');       //Usuario de la bbdd
define('PASS_DB', '');           //Contraseña de la bbdd
define('NAME_DB', 'urdido_engomado'); //Nombre de la bbdd

// Definimos la conexión (versión PHP 7)
function conectar(){
    global $conexion;  //Definición global para poder utilizar en todo el contexto
    $conexion = mysqli_connect(HOST_DB, USER_DB, PASS_DB, NAME_DB)
    or die ('NO SE HA PODIDO CONECTAR AL MOTOR DE LA BASE DE DATOS');
    mysqli_select_db($conexion, NAME_DB)
    or die ('NO SE ENCUENTRA LA BASE DE DATOS ' . NAME_DB);
}

function desconectar(){
    global $conexion;
    mysqli_close($conexion);
}
