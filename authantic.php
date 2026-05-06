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

    if ($username === '' || $password === '') {
        $message = 'Veuillez saisir un nom d\'utilisateur et un mot de passe.';
    } else {
        $stmt = $mysqli->prepare('SELECT password FROM users WHERE names = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($storedPassword);
        if ($stmt->fetch()) {
            $stmt->close();
            if ($password === $storedPassword) {
                $_SESSION['names'] = $username;
                header('Location: acceuil.php');
                exit;
            }
            $message = 'Nom d\'utilisateur ou mot de passe incorrect.';
        } else {
            $stmt->close();
            $message = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #ede2e2; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 400px; margin: 80px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; margin-bottom: 20px; color: #030000; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #030000; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; color: #030303; }
        .buttons { display: flex; justify-content: space-between; gap: 10px; }
        button { width: 100%; padding: 10px; border: none; border-radius: 4px; cursor: pointer; color: #fff; font-size: 16px; }
        .login { background: #007bff; }
        .signup { background: #28a745; }
        .message { margin-bottom: 15px; padding: 10px; border-radius: 4px; background: #ffe5e5; color: #a94442; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Connexion / Inscription</h1>
        <?php if ($message !== ''): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>

            <div class="buttons">
                <button type="submit" name="connexion" class="login">Connexion</button>
                <button type="button" onclick="window.location.href='inscri.php'" class="signup">S'inscrire</button>
            </div>
        </form>
    </div>
</body>
</html>
