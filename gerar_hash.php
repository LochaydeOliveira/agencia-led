<?php
$senha = '123456'; // Troque pela senha desejada
$hash = password_hash($senha, PASSWORD_DEFAULT);
echo $hash;
?>
