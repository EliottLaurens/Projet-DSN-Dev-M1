<?php
include 'db_connect.php';

$query = "SELECT * FROM point";
$stmt = $pdo->prepare($query);
$stmt->execute();
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($points);
?>
