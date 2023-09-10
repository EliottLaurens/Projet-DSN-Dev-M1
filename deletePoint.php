<?php
include 'db_connect.php';

$id = $_GET['id'];

$query = "DELETE FROM point WHERE ID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);

echo json_encode(['message' => 'Point supprimé avec succès']);
?>
