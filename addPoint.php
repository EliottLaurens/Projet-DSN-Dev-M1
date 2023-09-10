<?php
header('Content-Type: application/json');
include 'db_connect.php';

$structure = $_POST['structure'];
$type = (int) $_POST['type'];
$professionnel = $_POST['professionnel'];
$mailPro = $_POST['mailPro'];
$telPro = $_POST['telPro'];
$lastDate = $_POST['lastDate'];
$etat = (int) $_POST['etat'];
$address = $_POST['address'];
$postalCode = $_POST['postalCode'];
$city = $_POST['city'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

if(empty($latitude) || empty($longitude)) {
    $fullAddress = $address . ", " . $postalCode . " " . $city;
    $geoData = getGeocodeData($fullAddress);
    if($geoData) {
        $latitude = $geoData['lat'];
        $longitude = $geoData['lon'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de géocodage. Veuillez vérifier l\'adresse.']);
        exit;
    }
}


$sql = "INSERT INTO point (Structure, Type, Professionnel, MailPro, TelPro, Datedernierstage, Etat, Adresse, Codepostal, Ville, Latitude, Longitude) 
        VALUES (:structure, :type, :professionnel, :mailPro, :telPro, :lastDate, :etat, :address, :postalCode, :city, :latitude, :longitude)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':structure' => $structure, 
		':type' => $type, 
		':professionnel' => $professionnel, 
		':mailPro' => $mailPro, 
		':telPro' => $telPro, 
        ':lastDate' => $lastDate,
        ':etat' => $etat,
        ':address' => $address,
		':postalCode' => $postalCode,
		':city' => $city,
        ':latitude' => $latitude,
        ':longitude' => $longitude
    ]);

    echo json_encode(["status" => "success", "message" => "Point ajouté avec succès!"]);
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur: " . $e->getMessage()]);
}

function getGeocodeData($fullAddress) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($fullAddress);
    
    // Initialiser cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo 'Erreur cURL : ' . curl_error($ch);
        return false;
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data[0])) {
        return $data[0];
    }
    
    return false;
}



?>

