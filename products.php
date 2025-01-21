<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

include 'connection.php'; // Inclure la classe de connexion

// Créer une instance de la connexion
$connection = new Connection();
$conn = $connection->conn; // Assurez-vous que cette méthode retourne une connexion mysqli

// Requête pour récupérer les voitures depuis la base de données
$query = "SELECT car_id,marque, modele, annee, statut, prix_par_jour, image FROM Cars WHERE statut = 'disponible'";
$query = "SELECT car_id,marque, modele, annee, statut, prix_par_jour, image FROM Cars WHERE statut = 'disponible' ORDER BY annee DESC";
$result = $conn->query($query);

// Stocker les voitures dans un tableau
$cars = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modèles de voitures</title>
    <style>
        body.page {
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            padding: 10px;
            text-align: center;
        }
        .navbar a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
        }
        .navbar a:hover {
            color: #f0b42f;
        }
        h2 {
            color: #ffb74d;
            text-align: center;
            margin: 20px 0;
        }
        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        ul li {
            display: inline-block;
            width: 250px;
            background-color: #222;
            margin: 10px;
            padding: 15px;
            border-radius: 8px;
        }
        ul li .car-image {
            width: 100%;
            height: 110px;
            background-size: cover;
            background-position: center;
            margin-bottom: 10px;
        }
        ul li h3 {
            font-size: 18px;
            color: #ffb74d;
            margin: 10px 0;
        }
        ul li p {
            color: #fff;
            font-size: 14px;
        }
        ul li .price {
            font-size: 24px;
            color: #ffb74d;
            font-weight: bold;
        }
        ul li .reserve-btn {
            display: inline-block;
            padding: 8px 12px;
            color: #000;
            background-color: #ffb74d;
            border-radius: 4px;
            text-decoration: none;
        }
        ul li .reserve-btn:hover {
            background-color: #ff4141;
            color: #fff;
        }
    </style>
</head>
<body class="page">

<div class="navbar">
    <a href="home.php">Homepage</a>
    <a href="products.php">Products</a>
    <a href="profile.php">Profile</a>
    <span>Bienvenue, <?= htmlspecialchars($_SESSION['user_email']); ?></span>
    <a href="logout.php">Log out</a>
</div>

<h2>Modèles de Voitures</h2>
<ul>
    <?php if (!empty($cars)): ?>
        <?php foreach ($cars as $car): ?>
            <li>
            <div class="car-image" style="background-image: url('<?= htmlspecialchars($car['image']); ?>');"></div>

                <h3><?= htmlspecialchars($car['marque']) . " " . htmlspecialchars($car['modele']); ?></h3>
                <p>Année : <?= htmlspecialchars($car['annee']); ?></p>
                <p>Statut : <?= htmlspecialchars($car['statut']); ?></p>
                <p class="price"><?= htmlspecialchars($car['prix_par_jour']); ?> MAD</p>
                <a href="process_booking.php?car_id=<?= htmlspecialchars($car['car_id']); ?>" class="reserve-btn">Réserver</a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune voiture disponible pour le moment.</p>
    <?php endif; ?>
</ul>

</body>
</html>
