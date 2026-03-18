<?php
session_start();
include "boulangeriebd.php";
 
// Vérification : le client est-il connecté ?
if (!isset($_SESSION['client_id'])) {
    die("Vous devez être connecté pour voir votre panier. <a class='inscrire' href='connexion.php'>Se connecter</a>");
}
 
$id_client = $_SESSION['client_id'];
 
// Traitement pour retirer UN SEUL exemplaire du produit (-1)
if (isset($_GET['retirer_un'])) {
    $id_produit_a_retirer = $conn->real_escape_string($_GET['retirer_un']);
    // Le "LIMIT 1" est crucial ici pour ne supprimer qu'une seule ligne de commande à la fois
    $sql_delete_one = "DELETE FROM commande WHERE id_produit = '$id_produit_a_retirer' AND id_clients = '$id_client' LIMIT 1";
    $conn->query($sql_delete_one);
 
    header("Location: panier.php");
    exit();
}
 
// Traitement pour supprimer TOUS les exemplaires de ce produit dans le panier
if (isset($_GET['supprimer_tout'])) {
    $id_produit_a_supprimer = $conn->real_escape_string($_GET['supprimer_tout']);
    // Ici on supprime toutes les commandes de ce produit pour ce client
    $sql_delete_all = "DELETE FROM commande WHERE id_produit = '$id_produit_a_supprimer' AND id_clients = '$id_client'";
    $conn->query($sql_delete_all);
 
    header("Location: panier.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
 
<head>
    <title>Votre Panier</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="boulangerie.css">
</head>
 
<body>
    <header>
        <nav>
            <a href="panier.php" class="head">Mon Panier</a>
            <a href="commande.php" class="head">Commander</a>
            <a href="index.html#products" class="head">Produits</a>
            <a href="#contact" class="head">Contact</a>
            <a href="connexion.php" class="head">Connexion</a>
        </nav>
    </header>
 
    <h1>Votre Panier</h1>
 
    <?php
    // Nouvelle requête SQL : On regroupe par produit et on compte la quantité
    $sql = "SELECT
                produit.id_produit AS id_produit,
                produit.nom,
                produit.prix,
                COUNT(commande.id_commande) AS quantite_commandee,
                (produit.prix * COUNT(commande.id_commande)) AS prix_ligne
            FROM commande
            JOIN produit ON commande.id_produit = produit.id_produit
            WHERE commande.id_clients = '$id_client'
            GROUP BY produit.id_produit, produit.nom, produit.prix";
 
    $result = $conn->query($sql);
    $total_global = 0;
 
    if ($result->num_rows > 0) {
        // Affichage sous forme de tableau HTML basique
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>Produit</th>
                <th>Prix Unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th>Actions</th>
              </tr>";
 
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . ($row["nom"]) . "</td>";
            echo "<td>" . ($row["prix"]) . " €</td>";
            echo "<td><strong>" . ($row["quantite_commandee"]) . "</strong></td>";
            echo "<td>" . ($row["prix_ligne"]) . " €</td>";
 
            // Les nouveaux liens d'actions basés sur l'ID du produit
            echo "<td>
                    <a class='button' href='panier.php?retirer_un=" . $row["id_produit"] . "'>-1</a> |
                    <a class='button' href='panier.php?supprimer_tout=" . $row["id_produit"] . "'>Supprimer tout</a>
                  </td>";
            echo "</tr>";
 
            // On additionne le prix de la ligne pour le total global
            $total_global += $row["prix_ligne"];
        }
        echo "</table>";
 
        echo "<h2>Total à payer : " . $total_global . " €</h2>";
 
        echo "<a class='button' href='boncommande.php'>Valider et payer ma commande</a>";
    } else {
        echo "<p>Votre panier est actuellement vide.</p>";
        echo "<a class='button' href='commande.php'>Aller voir nos produits</a>";
    }
 
    $conn->close();
    ?>
 
    <br><br>
    <hr>
    <footer>
        <p>© 2026 La boulangerie du village. Tous droits réservés.</p>
        <div id="contact">
            <h2>Contactez-nous</h2>
            <p>Adresse : 123 Rue de la Boulangerie, Village</p>
            <p>Téléphone : 01 23 45 67 89</p>
            <a class="mail" href="mailto:laboulangerieduvillage@example.com">Envoyez-nous un email</a>
    </footer>
</body>
 
</html>
 