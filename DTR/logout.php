<?php
session_start();
session_unset();    // Tanggalin lahat ng session variables
session_destroy();  // Sirain ang session record

// I-redirect sa login page at lagyan ng "nocache" parameter para sigurado
header("Location: index.php?logged_out=1");
exit();
?>