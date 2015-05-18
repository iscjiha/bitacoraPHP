<?php

require_once 'clases/DB.class.php';

$db = new DB();
$query = "SELECT sp_contar_tickets(2)";

$res = $db->single($query);

var_dump($res);

?>
