<?php

$host = "localhost";
$username = "root";
$password = "";
$bank_db = "bank";

$bank_db_conn = new mysqli($host, $username, $password, $bank_db);
if ($bank_db_conn->connect_error)
{
    die("Connect error (" . $bank_db_conn->connect_errno . ") "
            . $bank_db_conn->connect_error);
}
