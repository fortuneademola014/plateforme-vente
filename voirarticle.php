<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title>Lecture de la table article</title>
<link rel="stylesheet" href="style.css">
<style type="text/css" >
table {border-style:double;border-width: 3px;border-color: green;background-color:
none;}
</style>
</head>
<body>
<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'magasin';

$idcom = new mysqli($host, $user, $password, $database);
if ($idcom->connect_error) {
    die('Erreur de connexion : ' . $idcom->connect_error);
}

$requete = 'SELECT * FROM article ORDER BY categorie';
$result = $idcom->query($requete);
if (!$result) {
    echo 'Lecture impossible : ' . $idcom->error;
} else {
    $nbart = $result->num_rows;
    echo '<h3>Tous nos articles par Categorie</h3>';
    echo "<h4>Il y a $nbart articles en magasin</h4>";
    echo '<table border="1">';
    echo '<tr><th>Id article</th><th>Design</th><th>Prix</th><th>catégorie</th></tr>';
    while ($ligne = $result->fetch_array(MYSQLI_NUM)) {
        echo '<tr>';
        foreach ($ligne as $valeur) {
            echo '<td>' . htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8') . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    $result->free();
}

$idcom->close();
?>
    <p>
        <button type="button" onclick="window.location.href='ajoutarticle.php'">Ajouter un Article</button>
        <button type="button" onclick="window.location.href='acceuil.php'">Revenir à accueil</button>
    </p>
</body>
</html>
