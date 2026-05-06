<?php
session_start();

$host = 'localhost';
$dbName = 'magasin';
$dbUser = 'root';
$dbPass = '';
$message = '';

$mysqli = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
    $message = 'Erreur de connexion à la base de données : ' . $mysqli->connect_error;
} else {
    $mysqli->query(
        "CREATE TABLE IF NOT EXISTS users (
            identifiant INT AUTO_INCREMENT PRIMARY KEY,
            names VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            createdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB"
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($message)) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm_password === '') {
        $message = 'Veuillez remplir tous les champs.';
    } elseif ($password !== $confirm_password) {
        $message = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $message = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        $stmt = $mysqli->prepare('SELECT identifiant FROM users WHERE names = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = 'Ce nom d\'utilisateur existe déjà. Veuillez en choisir un autre.';
            $stmt->close();
        } else {
            $stmt->close();
            $insert = $mysqli->prepare('INSERT INTO users (names, password) VALUES (?, ?)');
            $insert->bind_param('ss', $username, $password);
            if ($insert->execute()) {
                $message = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
                header('Location: authantic.php');
                exit;
            } else {
                $message = 'Erreur lors de l\'inscription, veuillez réessayer.';
            }
            $insert->close();
        }
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 400px; margin: 80px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; cursor: pointer; color: #fff; font-size: 16px; background: #28a745; }
        .message { margin-bottom: 15px; padding: 10px; border-radius: 4px; background: #ffe5e5; color: #a94442; }
        .back { margin-top: 10px; text-align: center; }
        .back a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        <?php if ($message !== ''): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">S'inscrire</button>
        </form>
        <div class="back">
            <a href="authantic.php">Retour à la connexion</a>
        </div>
    </div>
</body>
</html>