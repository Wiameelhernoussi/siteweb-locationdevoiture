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

    // Connexion à la base de données
    $connection = new Connection();
    $conn = $connection->conn;

    // Récupérer le prix par jour de la voiture
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

    // Récupérer l'ID de la réservation insérée
    $location_id = $conn->insert_id;

    // Insérer un paiement associé à cette réservation
    $montant_paye = $prix_total; // Vous pouvez ajuster selon le montant payé
    $date_paiement = date('Y-m-d');
    $queryPayment = "
        INSERT INTO Payments (location_id, montant, date_paiement)
        VALUES (?, ?, ?)";
    $stmtPayment = $conn->prepare($queryPayment);
    $stmtPayment->bind_param("ids", $location_id, $montant_paye, $date_paiement);
    $stmtPayment->execute();

    echo "Réservation confirmée ! Le prix total est de " . $prix_total . " MAD.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
/* From Uiverse.io by 3HugaDa3 */ 
.checkbox-wrapper {
  --checkbox-size: 25px;
  --checkbox-color: #00ff88;
  --checkbox-shadow: rgba(0, 255, 136, 0.3);
  --checkbox-border: rgba(0, 255, 136, 0.7);
  display: flex;
  align-items: center;
  position: relative;
  cursor: pointer;
  padding: 10px;
}

.checkbox-wrapper input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

.checkbox-wrapper .checkmark {
  position: relative;
  width: var(--checkbox-size);
  height: var(--checkbox-size);
  border: 2px solid var(--checkbox-border);
  border-radius: 8px;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, 0.2);
  box-shadow: 0 0 15px var(--checkbox-shadow);
  overflow: hidden;
}

.checkbox-wrapper .checkmark::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, var(--checkbox-color), #00ffcc);
  opacity: 0;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  transform: scale(0) rotate(-45deg);
}

.checkbox-wrapper input:checked ~ .checkmark::before {
  opacity: 1;
  transform: scale(1) rotate(0);
}

.checkbox-wrapper .checkmark svg {
  width: 0;
  height: 0;
  color: #1a1a1a;
  z-index: 1;
  transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  filter: drop-shadow(0 0 2px rgba(0, 0, 0, 0.5));
}

.checkbox-wrapper input:checked ~ .checkmark svg {
  width: 18px;
  height: 18px;
  transform: rotate(360deg);
}

.checkbox-wrapper:hover .checkmark {
  border-color: var(--checkbox-color);
  transform: scale(1.1);
  box-shadow:
    0 0 20px var(--checkbox-shadow),
    0 0 40px var(--checkbox-shadow),
    inset 0 0 10px var(--checkbox-shadow);
}

.checkbox-wrapper input:checked ~ .checkmark {
  animation: pulse 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes pulse {
  0% {
    transform: scale(1);
    box-shadow: 0 0 20px var(--checkbox-shadow);
  }
  50% {
    transform: scale(0.9);
    box-shadow:
      0 0 30px var(--checkbox-shadow),
      0 0 50px var(--checkbox-shadow);
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 20px var(--checkbox-shadow);
  }
}

.checkbox-wrapper .label {
  margin-left: 15px;
  font-family: "Segoe UI", sans-serif;
  color: var(--checkbox-color);
  font-size: 18px;
  text-shadow: 0 0 10px var(--checkbox-shadow);
  opacity: 0.9;
  transition: all 0.3s;
}

.checkbox-wrapper:hover .label {
  opacity: 1;
  transform: translateX(5px);
}

/* Glowing dots animation */
.checkbox-wrapper::after,
.checkbox-wrapper::before {
  content: "";
  position: absolute;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: var(--checkbox-color);
  opacity: 0;
  transition: all 0.5s;
}

.checkbox-wrapper::before {
  left: -10px;
  top: 50%;
}

.checkbox-wrapper::after {
  right: -10px;
  top: 50%;
}

.checkbox-wrapper:hover::before {
  opacity: 1;
  transform: translateX(-10px);
  box-shadow: 0 0 10px var(--checkbox-color);
}

.checkbox-wrapper:hover::after {
  opacity: 1;
  transform: translateX(10px);
  box-shadow: 0 0 10px var(--checkbox-color);
}
</style>
</head>
<body>
<label class="checkbox-wrapper">
  <input type="checkbox" />
  <div class="checkmark">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path
        d="M20 6L9 17L4 12"
        stroke-width="3"
        stroke-linecap="round"
        stroke-linejoin="round"
      ></path>
    </svg>
  </div>
  <span class="label"></span>
</label>
<a href="payments.php" style="display: block; text-align: center; margin-top: 10px;">
  <button>Passer au paiement</button>
</a>
