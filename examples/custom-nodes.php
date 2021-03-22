<?php
include_once '../vendor/autoload.php';

use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;

$fullNode = new HttpProvider('https://api.trongrid.io');
$solidityNode = new HttpProvider('https://api.trongrid.io');
$eventServer = new HttpProvider('https://api.trongrid.io');
$privateKey = 'private_key';

//Example 1
try {
    $tron = new Tron('4f7f258a-326f-43dd-8286-a8a4edf4c6b6', $fullNode, $solidityNode, $eventServer, null, $privateKey);
} catch (\IEXBase\TronAPI\Exception\TronException $e) {
    die($e->getMessage());
}