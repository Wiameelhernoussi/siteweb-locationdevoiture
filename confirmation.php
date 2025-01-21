<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_id = $_POST['car_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $user_email = $_SESSION['user_email'];

    // Calculer la différence entre les dates
    $datetime1 = new DateTime($date_debut);
    $datetime2 = new DateTime($date_fin);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->days;

    if ($days <= 0) {
        echo "La date de fin doit être après la date de début.";
        exit();
    }

    // Récupérer le prix par jour de la voiture
    $connection = new Connection();
    $conn = $connection->conn;

    $query = "SELECT prix_par_jour FROM Cars WHERE car_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $prix_par_jour = $car['prix_par_jour'];

    // Calculer le prix total
    $prix_total = $prix_par_jour * $days;

    // Récupérer l'ID du client via son email
    $queryClient = "SELECT id FROM Clients WHERE email = ?";
    $stmtClient = $conn->prepare($queryClient);
    $stmtClient->bind_param("s", $user_email);
    $stmtClient->execute();
    $resultClient = $stmtClient->get_result();
    $client = $resultClient->fetch_assoc();
    $client_id = $client['id'];

    // Insérer la réservation dans la table Locations
    $queryInsert = "
        INSERT INTO Locations (client_id, car_id, date_debut, date_fin, prix_total, statut)
        VALUES (?, ?, ?, ?, ?, 'en cours')";
    $stmtInsert = $conn->prepare($queryInsert);
    $stmtInsert->bind_param("iissd", $client_id, $car_id, $date_debut, $date_fin, $prix_total);
    $stmtInsert->execute();

    echo "Réservation confirmée ! Le prix total est de " . $prix_total . " MAD.";
}
?>

