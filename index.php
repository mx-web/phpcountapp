<?php
require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$pdo = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='. $_ENV['MYSQL_DB'].'', $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);

echo "<pre>";

$stmt = $pdo->prepare("SELECT * FROM counter ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->fetchAll();
$count = $result[0]['count'];

$stmt = $pdo->prepare("INSERT INTO counter (count, timestp) VALUES (?, ?)");
$params = [++$count, date("Y-m-d",time())];
$stmt->execute($params);

echo "UNDER_CONSTRUCTION_UNTIL = " . $_ENV['UNDER_CONSTRUCTION_UNTIL'] . '<br>';
echo "YOUR REMOTE ADDRESS = " . $_SERVER['REMOTE_ADDR'] . ' <br>';
echo "COUNT = ". $count;