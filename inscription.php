<!DOCTYPE html>
<html>
<head>
    <title>Veuillez vous inscrire</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="boulangerie.css">

</head>

<body>
    <header>
        <nav>
                <a href="commande.php" class="head">Commander</a>
                <a href="index.html#products" class="head">Produits</a>
                <a href="#contact" class="head">Contact</a>
                <a href="connexion.php" class="head">Connexion</a>
                <a href="panier.php" class="head">Mon Panier</a>
    
        </nav>
    </header>
    <div class="inscription">
            <p>Veuillez remplir le formulaire ci-dessous pour vous inscrire.</p>
    <form action="" method="post">
        Nom : <input type="text" class="form-input" name="nom" value="" required><br>
        Prenom : <input type="text" class="form-input" name="prenom" value="" required><br>
        adresse : <input type="text" class="form-input" name="adresse" value="" required><br>
        Téléphone : <input type="tel" class="form-input" name="telephone" value="" required><br>
        email : <input type="email" class="form-input" name="email" value="" required><br>
        mot de passe : <input type="password" class="form-input" name="mdp" value="" required><br>
        <input type="submit" class="button" value="S'incrire">
    </form>
    </div>
<footer>
    <p>© 2026 Boulangerie. Tous droits réservés.</p>
    <div id = "contact">
        <h2>Contactez-nous</h2>
        <p>Adresse : 123 Rue de la Boulangerie, Village</p>
        <p>Téléphone : 01 23 45 67 89</p>
        <a class="mail" href="mailto: laboulangerieduvillage@example.com">Envoyez-nous un email</a>
    </p>    </div>
    </footer>
</body>
</html>
<?php
include "boulangeriebd.php";
ini_set('display errors',1);
ini_set('display startup_errors',1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Récupération des données du formulaire
$nom = $conn->real_escape_string($_POST['nom']);
$prenom = $conn->real_escape_string($_POST['prenom']);
$adresse = $conn->real_escape_string($_POST['adresse']);
$telephone = $conn->real_escape_string($_POST['telephone']);
$email = $conn->real_escape_string($_POST['email']);
$mdp = $conn->real_escape_string($_POST['mdp']);



// Préparation de la requête SQL d'insertion
$sql = "INSERT INTO clients (nom, prenom, adresse, telephone, email, mdp)
        VALUES ('$nom', '$prenom', '$adresse', '$telephone', '$email', '$mdp')";

if ($conn->query($sql) === TRUE) {
    echo "Nouvel inscrit ajouté avec succès";
} else {
    echo "Erreur : " . $sql . "<br>" . $conn->error;
}}
// Fermeture de la connexion
$conn->close();
?>
