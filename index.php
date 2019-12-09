<?php
// Load the Dotenv Module to use it for parsing the .env file
require __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$mysqli = new mysqli($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_DB']);
if ($mysqli->connect_errno) {
    die("Verbindung fehlgeschlagen: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SHOW TABLES LIKE 'under_construction'"); // Put your table name after Like '
$stmt->execute();
$result = $stmt->get_Result();
$resultConverted = mysqli_fetch_all($result, MYSQLI_ASSOC);
if(empty($resultConverted)) {
    $stmt = $mysqli->prepare("CREATE TABLE under_construction (
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        ip_adress VARCHAR(255) NOT NULL,
        a TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      )
      ");
    echo $stmt->execute();
}

function getUserIpAddress() {
    $arrToCheck = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR','HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($arrToCheck as $http_constant) {
        if (isset($_SERVER[$http_constant])) {
            return $_SERVER[$http_constant];
        }
    }
    return 'UNKNOWN!';
}

$ipAdress = getUserIpAddress();

// Track information about the entering client (ip_adress)
$stmt = $mysqli->prepare("INSERT INTO under_construction (ip_adress) VALUES (?)");
$stmt->bind_param("s", $ipAdress);
$stmt->execute();
// ip_adress tracked


// Start fetching information about User and get the highest id value to print it out
$stmt = $mysqli->prepare('SELECT id FROM under_construction ORDER BY id DESC LIMIT 1');
$stmt->execute();
$result = $stmt->get_Result();
$resultConverted = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Output in HTML
echo '<h1>Under construction until ' . $_ENV['UNDER_CONSTRUCTION_UNTIL'] . '</h1><br>';
echo 'Your ip-adress is: ' . $ipAdress . '<br>';
echo 'This site was visited by ' . $resultConverted[0]['id'] . ' users';