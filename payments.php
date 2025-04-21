<?php
session_start();
include 'connection.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];
$connection = new Connection();
$conn = $connection->conn;

// Gestion du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['car_id'])) {
    $car_id = $_POST['car_id'];
    $days = $_POST['days'];
    $total = $_POST['total'];
    $card_number = $_POST['card_number'];
    $card_expiry = $_POST['card_expiry'];
    $card_cvc = $_POST['card_cvc'];

    // Validation des données de carte
    if (strlen($card_number) != 16 || !ctype_digit($card_number)) {
        $error = "Numéro de carte invalide";
    } elseif (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $card_expiry)) {
        $error = "Date d'expiration invalide";
    } elseif (strlen($card_cvc) != 3 || !ctype_digit($card_cvc)) {
        $error = "CVC invalide";
    } else {
        // Récupérer l'ID du client
        $query = "SELECT id FROM Clients WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $client = $result->fetch_assoc();
        $client_id = $client['id'];

        // Créer la location
        $query = "INSERT INTO Locations (client_id, car_id, date_debut, date_fin, prix_total, statut) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ?, 'en cours')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiid", $client_id, $car_id, $days, $total);
        $stmt->execute();
        $location_id = $conn->insert_id;

        // Créer le paiement
        $query = "INSERT INTO Payments (location_id, montant, date_paiement) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("id", $location_id, $total);
        $stmt->execute();

        header("Location: payment_confirmation.php?location_id=" . $location_id);
        exit();
    }
}

// Récupérer les paiements existants
$query = "
    SELECT Payments.payment_id, Payments.montant, Payments.date_paiement, Locations.prix_total 
    FROM Payments 
    JOIN Locations ON Payments.location_id = Locations.location_id 
    JOIN Clients ON Locations.client_id = Clients.id 
    WHERE Clients.email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - POO Voiture</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .background-image {
            height: 100%;
            width: 100%;
            background-image: url('https://media.s-bol.com/gNrKVg8yjmor/M8jBJnG/1200x675.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Content styles */
        .content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            background: rgba(0, 0, 0, 0.5);
        }

        header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 40px;
            background: rgb(61, 60, 59);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        header nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        header nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        header nav ul li a:hover {
            color: #fff;
        }

        nav ul li span {
            color: white;
            font-weight: bold;
        }

        .payment-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .payment-card {
            background:rgb(5, 5, 5);
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .card-input {
            position: relative;
        }

        .card-input i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .error-message {
            color:rgb(10, 10, 10);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .payment-history {
            margin-top: 2rem;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .price-tag {
            font-size: 1.5rem;
            color: #28a745;
            font-weight: bold;
        }

        footer {
            width: 100%;
            text-align: center;
            padding: 20px;
            background-color: #111;
            font-size: 0.9rem;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header>
        <div class="logo">
            <i class="fas fa-car"></i> POO Voiture
        </div>
        <nav>
            <ul>
                <li><a href="home.php">Accueil</a></li>
                <li><a href="products.php">Nos Voitures</a></li>
                <li><a href="cars.php">Rechercher</a></li>
                <span><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                <li><a href="profile.php">Mon Profil</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <!-- Contenu principal -->
    <div class="container">
        <div class="payment-container">
            <h2 class="text-center mb-4"><i class="fas fa-credit-card"></i> Paiement</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['car_id'])): ?>
                <form id="paymentForm" action="" method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="car_id" value="<?php echo htmlspecialchars($_GET['car_id']); ?>">
                    <input type="hidden" name="days" value="<?php echo htmlspecialchars($_GET['days']); ?>">
                    <input type="hidden" name="total" value="<?php echo htmlspecialchars($_GET['total']); ?>">

                    <div class="payment-card">
                        <h3 class="text-center mb-4">Détails du paiement</h3>
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Numéro de carte</label>
                            <div class="card-input">
                                <input type="text" class="form-control" id="card_number" name="card_number" required maxlength="16" pattern="\d{16}" placeholder="XXXX XXXX XXXX XXXX">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_expiry" class="form-label">Date d'expiration</label>
                                <div class="card-input">
                                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" required pattern="^(0[1-9]|1[0-2])\/\d{2}$" placeholder="MM/YY">
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="card_cvc" class="form-label">CVC</label>
                                <div class="card-input">
                                    <input type="text" class="form-control" id="card_cvc" name="card_cvc" required maxlength="3" pattern="\d{3}" placeholder="XXX">
                                    <i class="fas fa-lock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="price-tag"><?php echo number_format($_GET['total'], 2, ',', ' '); ?> €</div>
                            <button type="submit" class="btn btn-primary btn-lg mt-3">
                                <i class="fas fa-check"></i> Confirmer le paiement
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <div class="payment-history">
                <h3 class="text-center mb-4">Historique des paiements</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($row['date_paiement'])); ?></td>
                                <td><?php echo number_format($row['montant'], 2, ',', ' '); ?> €</td>
                                <td><span class="badge bg-success">Payé</span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation du formulaire
        const form = document.getElementById('paymentForm');
        const cardNumber = document.getElementById('card_number');
        const cardExpiry = document.getElementById('card_expiry');
        const cardCvc = document.getElementById('card_cvc');

        // Formatage du numéro de carte
        cardNumber.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? ' ' + x[4] : '');
        });

        // Formatage de la date d'expiration
        cardExpiry.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,2})/);
            e.target.value = !x[2] ? x[1] : x[1] + '/' + x[2];
        });

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
<?php
$stmt->close();
$conn->close();
?>
