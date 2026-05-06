<?php
// Connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$basedonnees = "magasin";

$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basedonnees);

// Vérifier la connexion
if ($connexion->connect_error) {
    die("Erreur de connexion: " . $connexion->connect_error);
}

// Récupérer les clients
$sql = "SELECT * FROM client";
$resultat = $connexion->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des Clients</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
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
    <h1>Liste des Clients</h1>
    
    <?php
    if ($resultat->num_rows > 0) {
        echo "<table>";
        echo "<tr>";
        
        // Afficher les en-têtes
        $info = $resultat->fetch_fields();
        foreach ($info as $col) {
            echo "<th>" . $col->name . "</th>";
        }
        echo "</tr>";
        
        // Afficher les données
        while ($row = $resultat->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Aucun client trouvé.";
    }
    
    $connexion->close();
    ?>
    <br><br>
    <button type="button" onclick="window.location.href='acceuil.php'">Revenir à accueil</button>
</body>
</html>
