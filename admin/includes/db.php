<?php
$host = "db.fr-roub1.bengt.wasmernet.com";
$user = "user_f10c4ef4";
$pass = "pw_uPtNw8YpAS7REO7JEIM85Oy3Y6kLYGoY";
$db = "db_e4f94ca1";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
