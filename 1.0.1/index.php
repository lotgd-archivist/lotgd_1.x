<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/http.php");
header("Location: home.php?".join("&",httpallget()));
?>
