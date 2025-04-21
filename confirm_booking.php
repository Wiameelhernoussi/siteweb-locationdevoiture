<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_id = $_POST['car_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $nom = $_POST['nom'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
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
    $query = "SELECT prix_par_jour, marque, modele FROM Cars WHERE car_id = ?";
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réservation - POO Voiture</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .confirmation-header {
            background: #28a745;
            color: white;
            padding: 1.5rem;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .summary-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .price-tag {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }
        .confirmation-details {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
        }
        .action-buttons {
            margin-top: 2rem;
            text-align: center;
        }
        .btn-primary {
            background: #28a745;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: bold;
        }
        .btn-primary:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 0.75rem 2rem;
            font-weight: bold;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .success-icon {
            font-size: 3rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <div class="confirmation-header">
                <i class="fas fa-check-circle success-icon"></i>
                <h2>Confirmation de réservation</h2>
            </div>

            <div class="confirmation-details">
                <h3 class="text-center mb-4">Détails de votre réservation</h3>

                <div class="row">
                    <div class="col-md-6 summary-item">
                        <strong>Voiture :</strong>
                        <?php echo htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?>
                    </div>
                    <div class="col-md-6 summary-item">
                        <strong>Prix par jour :</strong>
                        <span class="price-tag"><?php echo number_format($prix_par_jour, 2, ',', ' '); ?> €</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 summary-item">
                        <strong>Date de début :</strong>
                        <?php echo htmlspecialchars($date_debut); ?>
                    </div>
                    <div class="col-md-6 summary-item">
                        <strong>Date de fin :</strong>
                        <?php echo htmlspecialchars($date_fin); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 summary-item">
                        <strong>Nombre de jours :</strong>
                        <?php echo htmlspecialchars($days); ?> jours
                    </div>
                    <div class="col-md-6 summary-item">
                        <strong>Total :</strong>
                        <span class="price-tag"><?php echo number_format($prix_par_jour * $days, 2, ',', ' '); ?> €</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 summary-item">
                        <strong>Nom :</strong>
                        <?php echo htmlspecialchars($nom); ?>
                    </div>
                    <div class="col-md-6 summary-item">
                        <strong>Email :</strong>
                        <?php echo htmlspecialchars($email); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 summary-item">
                        <strong>Téléphone :</strong>
                        <?php echo htmlspecialchars($telephone); ?>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="cars.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux voitures
                </a>
                <a href="payments.php?car_id=<?php echo $car_id; ?>&days=<?php echo $days; ?>&total=<?php echo $prix_par_jour * $days; ?>" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Procéder au paiement
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
