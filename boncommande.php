<?php
    // Démarrage de la session pour récupérer les informations de l'utilisateur
    session_start();?>
<!DOCTYPE html>
<html>
<head>
    <title>Bon de commande</title>
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

    <h1>Bon de commande</h1>


<?php
    include "boulangeriebd.php";
    // Vérification : le client est-il connecté ?
    if (!isset($_SESSION['client_id'])) {   
        die("Vous devez être connecté pour voir votre bon de commande. <a href='connexion.php'>Se connecter</a>");
    }
    $id_client = $_SESSION['client_id'];
    // Récupération des détails de la commande pour ce client
    $sql = "SELECT
                produit.nom,
                produit.description,
                produit.prix,
                produit.image
            FROM commande
            JOIN produit ON commande.id_produit = produit.id_produit
            WHERE commande.id_clients = '$id_client'";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    // Affichage du bon de commande avec les détails du produit
    echo "<h2>Merci pour votre commande, " . ($_SESSION['client_nom']) . " !</h2>";
   while ($produit = $result->fetch_assoc()) {
    echo "<div class='products'>"; 
    echo "<div class='product'>";
    echo "<img class='product-image' src='" .($produit['image']) . "' alt='" . ($produit['nom']) . "'>";
    echo "<h3>" . ($produit['nom']) . "</h3>";
    echo "<p>" . ($produit['description']) . "</p>";
    echo "<p>Prix : " . ($produit['prix']) . " €</p>";
    echo "</div>";}}
    else {
        echo "<p>Votre panier est vide. <a class='button' href='commande.php'>Aller voir nos produits</a></p>";
    }