<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'magasin';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Erreur de connexion : ' . $conn->connect_error);
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeArticle = trim($_POST['id_article'] ?? '');
    $description = trim($_POST['design'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $prix = trim($_POST['prix'] ?? '');

    if ($codeArticle === '' || $description === '' || $categorie === '' || $prix === '') {
        $message = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO article (id_article, design , categorie, prix) VALUES (?, ?, ?, ?)"
        );

        if ($stmt === false) {
            $message = 'Erreur de préparation : ' . $conn->error;
        } else {
            $stmt->bind_param('sssd', $codeArticle, $description, $categorie, $prix);

            if ($stmt->execute()) {
                $message = 'Données enregistrées avec succès.';
                $success = true;
            } else {
                $message = 'Erreur lors de l\'enregistrement : ' . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Formulaire d'enregistrement</h1>

    <?php if ($success): ?>
        <script>
            alert('Enregistrement réussi');
            window.location.href = 'voirarticle.php';
        </script>
    <?php elseif ($message !== ''): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>
            Code article :
            <input type="text" name="id_article" required>
        </label>
        <br><br>

        <label>
            Description :
            <textarea name="design" required></textarea>
        </label>
        <br><br>

        <label>
            Catégorie :
            <input type="text" name="categorie" required>
        </label>
        <br><br>

        <label>
            Prix :
            <input type="float"  name="prix" required>
        </label>
        <br><br>

        <button type="submit">Enregistrer</button>
    
    </form>
    <br><br>
    <button type="button" onclick="window.location.href='acceuil.php'">Revenir à accueil</button>
</body>
</html>