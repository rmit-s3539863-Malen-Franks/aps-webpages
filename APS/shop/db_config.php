<?php

$host = "localhost";
$username = "root";
$password = "";
$market_db = "marketplace";
$user_db = "user";
$voucher_db = "voucher";

$market_db_conn = new mysqli($host, $username, $password, $market_db);
if ($market_db_conn->connect_error)
{
    die("Connect error (" . $market_db_conn->connect_errno . ") "
            . $market_db_conn->connect_error);
}

$user_db_conn = new mysqli($host, $username, $password, $user_db);
if ($user_db_conn->connect_error)
{
    die("Connect error (" . $user_db_conn->connect_errno . ") "
            . $user_db_conn->connect_error);
}

$voucher_db_conn = new mysqli($host, $username, $password, $voucher_db);
if ($voucher_db_conn->connect_error)
{
    die("Connect error (" . $voucher_db_conn->connect_errno . ") "
            . $voucher_db_conn->connect_error);
}
