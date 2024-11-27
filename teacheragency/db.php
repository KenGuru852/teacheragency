<?php
$mysqli = new mysqli("localhost", "root", "", "tutoring_agency");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}
?>