<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
// $db_giftcat = new GIFTCAT();
class GIFTCAT extends SQLite3
{
    function __construct()
    {
        $this->open('giftcat.sqlite');
    }
}

$db_giftcat = new GIFTCAT();
$result = $db_giftcat->query('SELECT * FROM "table"');
$data = array();
while ($res = $result->fetchArray(1)){array_push($data, $res);}
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);