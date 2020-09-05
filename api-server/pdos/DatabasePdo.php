<?php

//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "13.209.224.207";
        $DB_NAME = "testDB";
        $DB_USER = "ubuntu";
        $DB_PW = "softsquared";
        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}