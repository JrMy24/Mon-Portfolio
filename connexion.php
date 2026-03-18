<?php
// Démarrage de la session (doit toujours être la première ligne)
session_start();
 
include "boulangeriebd.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
$message_erreur = "";
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $conn->real_escape_string($_POST['nom']);
    $mdp = $conn->real_escape_string($_POST['mdp']);
 
    $sql = "SELECT * FROM clients WHERE nom='$nom' AND mdp='$mdp'";
    $result = $conn->query($sql);
 
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
 
        // on mémorise l'ID du client !
        $_SESSION['client_id'] = $row['id'];
        $_SESSION['client_nom'] = $row['nom'];
 
        // Redirection propre en PHP
        header("Location: commande.php");
        exit();
    } else {
        $message_erreur = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!doctype html>
<html>
 
<head>
    <title>Veuillez vous connecter</title>
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
    <div class="connexion">
        <p>Veuillez entrer votre nom d'utilisateur et votre mot de passe pour vous connecter.</p>
 
        <?php if (!empty($message_erreur)) {
            echo "<p style='color: red; text-align: center;'>$message_erreur</p>";
        } ?>
 
        <form action="" method="post">
            Nom d'utilisateur : <input type="text" class="form-input" name="nom" value="" required><br>
            Mot de passe : <input type="password" class="form-input" name="mdp" value="" required><br>
            <input type="submit" class="button" value="Se connecter">
        </form>
    </div>
 
    <div class="inscription">
        <p>Pas encore inscrit ? <a href="inscription.php" class="inscrire">Inscrivez-vous ici</a></p>
    </div>
    <footer>
        <p>© 2026 Boulangerie. Tous droits réservés.</p>
        <div id="contact">
            <h2>Contactez-nous</h2>
            <p>Adresse : 123 Rue de la Boulangerie, Village</p>
            <p>Téléphone : 01 23 45 67 89</p>
            <a class="mail" href="mailto:laboulangerieduvillage@example.com">Envoyez-nous un email</a>
        </div>
    </footer>
</body>
 
</html>