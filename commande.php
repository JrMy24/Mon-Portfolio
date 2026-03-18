<?php session_start(); ?>
<!DOCTYPE html>
<html>
 
<head>
    <title>Passer une commande</title>
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
 
    <div class="header1">
        <h1>Nos Produits Délicieux</h1>
    </div>
 
    <h2 style="text-align: center;">Faites votre choix</h2>
 
    <?php
    include "boulangeriebd.php";
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
 
    // Traitement de la commande si le bouton est cliqué
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commander'])) {
        $id_produit = $conn->real_escape_string($_POST['id_produit']);
 
        // On vérifie si le client est bien connecté en mémoire
        if (isset($_SESSION['client_id'])) {
            // On récupère automatiquement son ID sans rien lui demander !
            $id_client = $_SESSION['client_id'];
 
            // On insère dans la base de données
            $sql_insert = "INSERT INTO commande (id_produit, id_clients) VALUES ('$id_produit', '$id_client')";
 
            if ($conn->query($sql_insert) === TRUE) {
                echo "<p>Commande passée avec succès !</p>";
            } else {
                echo "<p>Erreur : " . $conn->error . "</p>";
            }
        } else {
            // Si le serveur n'a pas de mémoire, c'est que le client n'est pas connecté
            echo "<p>Vous devez être connecté pour passer une commande. <a class='inscrire' href='connexion.php'>Connectez-vous ici</a>.</p>";
        }
    }
    ?>
 
    <div class="products">
        <?php
        // Récupération des produits depuis la base de données
        $sql_select = "SELECT * FROM produit";
        $result = $conn->query($sql_select);
 
        if ($result->num_rows > 0) {
            // Affichage de chaque produit
            while ($row = $result->fetch_assoc()) {
                echo '<div class="product">';
                echo '<img src="' . ($row["image"]) . '" alt="' . ($row["nom"]) . '" class="product-image">';
                echo '<h2 class="product-title">' .($row["nom"]) . '</h2>';
                echo '<p>' . ($row["description"]) . '</p>';
                echo '<p><strong>Prix : ' . ($row["prix"]) . ' €</strong></p>';
                echo '<p><small>En stock : ' . ($row["quantite"]) . '</small></p>';
 
                // Formulaire caché pour envoyer l'ID du produit lors du clic sur Commander
                echo '<form action="" method="post">';
                echo '<input type="hidden" name="id_produit" value="' . $row["id_produit"] . '">';
                echo '<input type="submit" name="commander" class="button" value="Commander">';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "<p>Aucun produit disponible pour le moment.</p>";
        }
 
        $conn->close();
        ?>
    </div>
 
    <footer>
        <p>© 2026 La boulangerie du village. Tous droits réservés.</p>
        <div id="contact">
            <h2>Contactez-nous</h2>
            <p>Adresse : 123 Rue de la Boulangerie, Village</p>
            <p>Téléphone : 01 23 45 67 89</p>
            <a class="mail" href="mailto:laboulangerieduvillage@example.com">Envoyez-nous un email</a>
        </div>
    </footer>
</body>
 
</html>