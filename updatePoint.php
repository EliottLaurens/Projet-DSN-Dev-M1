<?php
include 'db_connect.php';

$id = $_POST['id'];
$structure = $_POST['structure'];
$type = $_POST['type'];
$professionnel = $_POST['professionnel'];
$mailPro = $_POST['mailPro'];
$telPro = $_POST['telPro'];
$date = $_POST['date'];
$etat = $_POST['etat'];

$stmt = $pdo->prepare("UPDATE point SET Structure= :structure, Type= :type, Professionnel= :professionnel, MailPro= :mailPro, TelPro= :telPro, Datedernierstage= :date, Etat= :etat WHERE ID= :id");
$stmt->execute([':structure' => $structure, ':type' => $type, ':professionnel' => $professionnel, ':mailPro' => $mailPro, ':telPro' => $telPro, ':date' => $date, ':etat' => $etat, ':id' => $id]);

if ($stmt) {
    echo json_encode(["message" => "Point mis à jour avec succès!"]);
} else {
    echo json_encode(["message" => "Erreur lors de la mise à jour du point."]);
}

?>


