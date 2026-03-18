<!doctype html>
<html>
<head>
    <title>Ajout de produit</title>
    <meta charset="UTF-8">
    
    <link rel="stylesheet" href="boulangerie.css">
</head>
<body>      
    <div class="ajout-produit">
        <h2>Ajouter un nouveau produit</h2>
        <form action="" method="post">
            Nom du produit : <input type="text" class="form-input" name="nom" value="" required><br>
            Description : <textarea class="form-input" name="description" required></textarea><br>
            Prix : <input type="number" step="0.01" class="form-input" name="prix" value="" required><br>
            quantité : <input type="number" class="form-input" name="quantite" value="" required><br>
            Image URL : <input type="text" class="form-input" name="image" value="" required><br>
            Type de produit : <input type="number" class="form-input" name="id_type" value="" required><br>

            <input type="submit" class="button" value="Ajouter le produit">
        </form>
    </div>
    <footer>    
    <p>© 2026 Boulangerie. Tous droits réservés.</p>
    <div id = "contact">    
        <h2>Contactez-nous</h2>
        <p>Adresse : 123 Rue de la Boulangerie, Village</p>
        <p>Téléphone : 01 23 45 67 89</p>
        <a class="mail" href="mailto:laboulangerieduvillage@example.com">Envoyez-nous un email</a>
    </div>
    </footer>   
</body>
</html> 
<?php
include "boulangeriebd.php";
ini_set('display errors',1);
ini_set('display startup_errors',1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $conn->real_escape_string($_POST['nom']);
    $description = $conn->real_escape_string($_POST['description']);
    $prix = $conn->real_escape_string($_POST['prix']);
    $quantite = $conn->real_escape_string($_POST['quantite']);
    $image = $conn->real_escape_string($_POST['image']);
    $id_type = $conn->real_escape_string($_POST['id_type']);

    $sql = "INSERT INTO produit (nom, description, prix, quantite, image,id_type) 
    VALUES ('$nom', '$description', '$prix', '$quantite', '$image','$id_type')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Produit ajouté avec succès.";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}