<?php
/**
 * Created by PhpStorm.
 *
 * User:      sunqingjiang(aiwozhe@yeah.net)
 * Date:      2021/4/19
 * Time:      20:23
 */
include_once '../vendor/autoload.php';

$tron = new \IEXBase\TronAPI\Tron('4f7f258a-326f-43dd-8286-a8a4edf4c6b6');

$tron->setPrivateKey('');
$result = $tron->freezeBalance('', 10, 3, 'BANDWIDTH', '');

var_dump($result);