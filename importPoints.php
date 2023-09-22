<?php
ob_start();
include 'db_connect.php';
ob_clean();
header('Content-Type: application/json');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

foreach ($data as $point) {
    try {
        $structure = $pdo->quote($point['structure']);
        $type = intval($point['type']);
        $professionnel = $pdo->quote($point['professionnel']);
        $mailPro = $pdo->quote($point['mailPro']);
        $telPro = $pdo->quote($point['telPro']);
        $lastDate = $pdo->quote($point['lastDate']);
        $etat = intval($point['etat']);
        $address = $pdo->quote($point['address']);
        $postalCode = $pdo->quote($point['postalCode']);
        $city = $pdo->quote($point['city']);
        $latitude = floatval($point['latitude']);
        $longitude = floatval($point['longitude']);

        $sql = "INSERT INTO point (Structure, Type, Professionnel, MailPro, TelPro, Datedernierstage, Etat, Adresse, Codepostal, Ville, Latitude, Longitude) VALUES ($structure, $type, $professionnel, $mailPro, $telPro, $lastDate, $etat, $address, $postalCode, $city, $latitude, $longitude)";

        $pdo->exec($sql);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

http_response_code(200);
echo json_encode(['status' => 'success']);



?>

