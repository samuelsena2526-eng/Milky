<?php

session_start(); // Iniciamos la sesión

session_destroy();  // Destruimos la sesión

header("Location:../../index.php"); // Redirigimos al inicio

?>