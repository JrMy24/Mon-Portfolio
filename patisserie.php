<!doctype html>
<html>
<head>
    <title>Nos produits</title>
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
    <h2>Nos produits</h2>   
    <div class="products">
        <?php
        include "boulangeriebd.php";
        $sql = "SELECT * FROM produit where id_type = 2";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<img class='product-image' src='" . $row["image"] . "' alt='" . $row["nom"] . "'>";
                echo "<h3>" . $row["nom"] . "</h3>";
                echo "<p>" . $row["description"] . "</p>";
                echo "<p>Prix : " . $row["prix"] . " €</p>";
                echo "<p>Quantité disponible : " . $row["quantite"] . "</p>";
                echo "<a href=commande.php class='button'>Commander</a>";
                echo "</div>";
            }
        } else {
            echo "Aucun produit disponible.";
        }
        $conn->close();
        ?>
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