
<?php
session_start();
include 'connection.php';

if (!isset($_GET['car_id'])) {
    header("Location: cars.php");
    exit();
}

$car_id = $_GET['car_id'];
$connection = new Connection();
$conn = $connection->conn;

// Récupérer les informations de la voiture
$query = "SELECT marque, modele, prix_par_jour FROM Cars WHERE car_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Voiture introuvable.";
    exit();
}

$car = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réserver une voiture</title>
</head>
<body>
    <h2>Réserver <?= htmlspecialchars($car['marque']) . " " . htmlspecialchars($car['modele']); ?></h2>

    <form action="confirm_booking.php" method="POST">
        <input type="hidden" name="car_id" value="<?= htmlspecialchars($car_id); ?>">

        <label for="date_debut">Date de début :</label>
        <input type="date" id="date_debut" name="date_debut" required><br>

        <label for="date_fin">Date de fin :</label>
        <input type="date" id="date_fin" name="date_fin" required><br>

        <label for="phone">Téléphone :</label>
        <input type="text" id="phone" name="phone" required><br>

        <label for="address">Adresse :</label>
        <input type="text" id="address" name="address" required><br>

        <button type="submit">Confirmer la réservation</button>
    </form>
</body>
</html>
