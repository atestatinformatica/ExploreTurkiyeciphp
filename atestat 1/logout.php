<?php
session_start();
session_unset();    // șterge toate variabilele de sesiune
session_destroy();  // distruge sesiunea

header("Location: index.php"); // redirecționează către pagina principală
exit;
