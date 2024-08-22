<?php
session_start();
session_unset();
session_destroy();
header("Location:Index.php"); // Redirect to the home page
exit();
?>
