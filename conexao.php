<?php

function conectarBanco() {
    $servername = "localhost:3306";
    $username = "root";
    $password = "1776NYC!";
    $dbname = "ALEXANDRIA";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        return $conn;
    } catch (mysqli_sql_exception $e) {
        die("Erro na conexão: ".$e->getMessage());
    }
}

?>