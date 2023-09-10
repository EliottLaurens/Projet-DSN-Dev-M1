<?php
session_start();
if(!isset($_SESSION['role'])) {
    header('Location: login.html');
    exit;
}
if($_SESSION['role'] != "administrateur") {
    die("Accès non autorisé");
}

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

$message = "";

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = "enseignant";

    $stmt = $pdo->prepare('INSERT INTO utilisateurs (email, mot_de_passe, role) VALUES (?, ?, ?)');
    if ($stmt->execute([$email, $mot_de_passe, $role])) {
        $message = "Enseignant ajouté avec succès!";
    } else {
        $message = "Erreur lors de l'ajout!";
    }
}
?>

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <title>Page d'administration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <div class="admin-section">
        <h2>Ajouter un enseignant</h2>
        <form action="admin.php" method="post">
    <input type="email" name="email" placeholder="Email de l'enseignant" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <input type="submit" value="Ajouter">
</form>
<!-- Afficher un message en cas de succès ou d'erreur -->
<?php if ($message): ?>
    <div class="notification <?php echo (strpos($message, 'Erreur') === false) ? 'success' : 'error'; ?>">
        <?= $message ?>
    </div>
<?php endif; ?>
    </div>

    <div class="admin-section">
        <h2>Gérer la carte</h2>
        <a href="carteAdmin.php">Accéder à la carte</a>
    </div>
</body>
</html>
