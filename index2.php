<!DOCTYPE html>
<html>
<body>

<?php
$date = str_replace('/', '-','20/04/2019') ;
$newDate = date("Y-m-d", strtotime($date)) ;
echo   $newDate;
?>

</body>
</html>
