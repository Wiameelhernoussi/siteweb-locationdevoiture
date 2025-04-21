
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - POO Voiture</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .booking-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .car-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .price-tag {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }
        .form-control {
            border-radius: 8px;
        }
        .date-input {
            position: relative;
        }
        .date-input i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-container">
            <h2 class="text-center mb-4"><i class="fas fa-car"></i> Réservation de <?php echo htmlspecialchars($car['marque'] . ' ' . $car['modele']); ?></h2>
            
            <div class="car-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Prix par jour :</strong>
                        <span class="price-tag"><?php echo number_format($car['prix_par_jour'], 2, ',', ' '); ?> €</span>
                    </div>
                </div>
            </div>

            <form id="bookingForm" action="confirm_booking.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
                
                <div class="mb-3">
                    <label for="date_debut" class="form-label">Date de début</label>
                    <div class="date-input">
                        <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="error-message"></div>
                </div>

                <div class="mb-3">
                    <label for="date_fin" class="form-label">Date de fin</label>
                    <div class="date-input">
                        <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="error-message"></div>
                </div>

                <div class="mb-3">
                    <label for="nom" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="nom" name="nom" required>
                    <div class="error-message"></div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="error-message"></div>
                </div>

                <div class="mb-4">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" required>
                    <div class="error-message"></div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check"></i> Confirmer la réservation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation du formulaire
        const form = document.getElementById('bookingForm');
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');

        // Vérifier que la date de fin est après la date de début
        dateDebut.addEventListener('change', function() {
            if (dateDebut.value && dateFin.value && dateDebut.value >= dateFin.value) {
                dateFin.setCustomValidity('La date de fin doit être après la date de début');
            } else {
                dateFin.setCustomValidity('');
            }
        });

        dateFin.addEventListener('change', function() {
            if (dateDebut.value && dateFin.value && dateDebut.value >= dateFin.value) {
                dateFin.setCustomValidity('La date de fin doit être après la date de début');
            } else {
                dateFin.setCustomValidity('');
            }
        });

        // Validation du formulaire
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    </script>
</body>
</html>
