<?php
// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$nombase = "magasin";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nombase;charset=utf8", $utilisateur, $motdepasse);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les données de la table
$requete = $connexion->prepare("SELECT * FROM lignes");
$requete->execute();
$resultats = $requete->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Table Ventes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Tableau des Ventes</h1>
    <table>
        <thead>
            <tr>
                <?php
                if (count($resultats) > 0) {
                    foreach (array_keys($resultats[0]) as $colonne) {
                        echo "<th>" . htmlspecialchars($colonne) . "</th>";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($resultats as $ligne) {
                echo "<tr>";
                foreach ($ligne as $valeur) {
                    echo "<td>" . htmlspecialchars($valeur) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <br><br>
    <button type="button" onclick="window.location.href='acceuil.php'">Revenir à accueil</button>
</body>
</html>
