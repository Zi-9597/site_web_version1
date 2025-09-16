<?php
// Basic connection settings
$databaseHost = 'docker-heberg-mariadb-1.univ-lille.fr';
$databaseUsername = 'association-eea';
$databasePassword = '13@q@HmC1@QTpgh0rS-j';
$databaseName = 'association-eea';

// Connect to the database
$mysqli = mysqli_connect($databaseHost, $databaseUsername, $databasePassword , $databaseName);


// Check connection
if (!$mysqli) {
  die("Connection failed merde : " . mysqli_connect_error());
}
var_dump($mysqli);
echo "Connected successfully";
?>