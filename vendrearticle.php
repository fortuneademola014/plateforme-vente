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

$articles = [];
$articleResult = $conn->query("SELECT id_article, design, categorie, prix FROM article ORDER BY design");
if ($articleResult) {
    while ($row = $articleResult->fetch_assoc()) {
        $articles[] = $row;
    }
    $articleResult->free();
}

$idClient = '';
$nom = '';
$prenom = '';
$age = '';
$adresse = '';
$ville = '';
$mail = '';
$selectedArticle = '';
$quantite = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idClient = trim($_POST['id_client'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $selectedArticle = trim($_POST['article_id'] ?? '');
    $quantite = trim($_POST['quantite'] ?? '');

    if ($nom === '' || $prenom === '' || $age === '' || $adresse === '' || $ville === '' || $mail === '' || $selectedArticle === '' || $quantite === '') {
        $message = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!ctype_digit($age) || (int)$age < 0) {
        $message = 'L\'âge doit être un nombre entier positif.';
    } elseif (!ctype_digit($quantite) || (int)$quantite < 1) {
        $message = 'La quantité doit être un nombre entier supérieur ou égal à 1.';
    } elseif ($idClient !== '' && !preg_match('/^[a-zA-Z0-9]+$/', $idClient)) {
        $message = 'L\'identifiant client doit être alphanumérique.';
    } else {
        $clientId = ($idClient !== '' ? $idClient : null);
        $ageInt = (int)$age;
        $quantityInt = (int)$quantite;

        $conn->begin_transaction();
        try {
            if ($clientId !== null) {
                $stmt = $conn->prepare('SELECT id_client FROM client WHERE id_client = ?');
                $stmt->bind_param('s', $clientId);
                $stmt->execute();
                $stmt->store_result();
                $clientExists = $stmt->num_rows > 0;
                $stmt->close();
                if ($clientExists) {
                    throw new Exception('Cet identifiant client existe déjà.');
                }
            }

            if ($clientId !== null) {
                $stmt = $conn->prepare('INSERT INTO client (id_client, nom, prenom, age, adresse, ville, mail) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssisss', $clientId, $nom, $prenom, $ageInt, $adresse, $ville, $mail);
            } else {
                $stmt = $conn->prepare('INSERT INTO client (nom, prenom, age, adresse, ville, mail) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('ssisss', $nom, $prenom, $ageInt, $adresse, $ville, $mail);
            }
            $stmt->execute();
            if ($clientId === null) {
                $clientId = $stmt->insert_id;
            }
            $stmt->close();

            $date = date('Y-m-d');
            $stmt = $conn->prepare('INSERT INTO commande (`date`, id_client) VALUES (?, ?)');
            $stmt->bind_param('ss', $date, $clientId);
            $stmt->execute();
            $commandeId = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare('INSERT INTO lignes (id_comm, id_article, quantite) VALUES (?, ?, ?)');
            $stmt->bind_param('isi', $commandeId, $selectedArticle, $quantityInt);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $success = true;
            $message = 'Commande enregistrée avec succès.';
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Erreur lors de l\'enregistrement de la commande : ' . $e->getMessage();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vendre un article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Créer une commande</h1>

    <?php if ($success): ?>
        <script>
            alert('Commande enregistrée avec succès');
            window.location.href = 'ventes.php';
        </script>
    <?php elseif ($message !== ''): ?>
        <div class="message error">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="vendrearticle.php">
        <fieldset>
            <legend>Informations client</legend>

            <div class="form-group">
                <label>Id client :
                    <input type="text" name="id_client" value="<?php echo htmlspecialchars($idClient); ?>" placeholder="Laisser vide pour nouveau client">
                </label>
            </div>

            <div class="form-group">
                <label>Nom :
                    <input type="text" name="nom" required value="<?php echo htmlspecialchars($nom); ?>">
                </label>
            </div>

            <div class="form-group">
                <label>Prénom :
                    <input type="text" name="prenom" required value="<?php echo htmlspecialchars($prenom); ?>">
                </label>
            </div>

            <div class="form-group">
                <label>Âge :
                    <input type="number" name="age" min="0" required value="<?php echo htmlspecialchars($age); ?>">
                </label>
            </div>

            <div class="form-group">
                <label>Adresse :
                    <input type="text" name="adresse" required value="<?php echo htmlspecialchars($adresse); ?>">
                </label>
            </div>

            <div class="form-group">
                <label>Ville :
                    <input type="text" name="ville" required value="<?php echo htmlspecialchars($ville); ?>">
                </label>
            </div>

            <div class="form-group">
                <label>Mail :
                    <input type="email" name="mail" required value="<?php echo htmlspecialchars($mail); ?>">
                </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Commande</legend>

            <div class="form-group">
                <label>Article :
                    <select name="article_id" required>
                        <option value="">-- Sélectionnez un article --</option>
                        <?php foreach ($articles as $article): ?>
                            <option value="<?php echo htmlspecialchars($article['id_article']); ?>" <?php echo $selectedArticle === $article['id_article'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($article['id_article'] . ' - ' . $article['design'] . ' (' . $article['categorie'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label>Quantité :
                    <input type="number" name="quantite" min="1" required value="<?php echo htmlspecialchars($quantite); ?>">
                </label>
            </div>
        </fieldset>

        <button type="submit">Enregistrer la commande</button>
    </form>
    <br><br>
    <button type="button" onclick="window.location.href='acceuil.php'">Revenir à accueil</button>
</body>
</html>
