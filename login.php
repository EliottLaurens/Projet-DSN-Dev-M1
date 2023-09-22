<?php
$host = '127.0.0.1';
$db   = 'cesf';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$email = $_POST['email'];
$mot_de_passe = $_POST['mot_de_passe'];

$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ? AND mot_de_passe = ?');
$stmt->execute([$email, $mot_de_passe]);
$user = $stmt->fetch();

if ($user) {
    session_start();
    $_SESSION['role'] = $user['role'];
    if ($user['role'] == "administrateur") {
        header('Location: admin.php');
    } else {
			if ($user['role'] == "enseignant") {
			header('Location: carteEnseignant.php');
		}
    }
}
else {
    header('Location: login.html?error=1');
}
?>