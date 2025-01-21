<?php
include 'Connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $car_id = $_POST['car_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Calculer le prix total
    $conn = new Connection();
    $query = "SELECT prix_par_jour FROM Cars WHERE car_id = ?";
    $stmt = $conn->conn->prepare($query);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $prix_total = (strtotime($date_fin) - strtotime($date_debut)) / (60 * 60 * 24) * $car['prix_par_jour'];

    // Enregistrer le client
    $query = "INSERT INTO Clients (firstname, lastname, email, phone, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->conn->prepare($query);
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $phone, $address);
    $stmt->execute();
    $client_id = $stmt->insert_id;

    // Enregistrer la réservation
    $query = "INSERT INTO Locations (client_id, car_id, date_debut, date_fin, prix_total, statut) VALUES (?, ?, ?, ?, ?, 'en cours')";
    $stmt = $conn->conn->prepare($query);
    $stmt->bind_param("iissd", $client_id, $car_id, $date_debut, $date_fin, $prix_total);
    $stmt->execute();

    // Mettre à jour le statut de la voiture
    $query = "UPDATE Cars SET statut = 'non disponible' WHERE car_id = ?";
    $stmt = $conn->conn->prepare($query);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();

    $stmt->close();
    $conn->closeConnection();

    echo "Réservation réussie !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .reservation-section {
            background-image: url('https://bandys.ma/wp-content/uploads/elementor/thumbs/IMG_7648-scaled-qnip8lwrhu6f2xpw8lubp0m57mcpr1ou3a42h1edr4.jpg'); /* Image d'arrière-plan */
            background-size: cover;
            background-position: center;
            color: white; /* Texte en blanc pour le contraste */
            text-align: center;
            padding: 120px 20px; /* Espacement supérieur pour le titre */
            height: 100vh; /* Prend toute la hauteur de l'écran */
            position: relative;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Couche sombre pour améliorer la lisibilité */
        }

        .content {
            position: relative; /* Pour que le texte soit au-dessus de l'overlay */
            z-index: 1;
        }

        .reservation-title {
            font-size: 3em;
            margin: 0;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Ombre portée pour le titre */
        }

        .divider {
            width: 50%;
            height: 2px;
            background: #e1c84a; /* Couleur dorée */
            margin: 20px auto;
        }

        .reservation-text {
            font-size: 1.5em;
            margin-top: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7); /* Ombre portée pour le texte */
        }

        .form-section {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group button {
            display: block;
            width: 100%;
            background-color:rgb(185, 162, 14);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color:rgb(179, 164, 9);
        }
    </style>
</head>
<body>
    <div class="reservation-section">
        <div class="overlay"></div> <!-- Couche sombre -->
        <div class="content">
            <h1 class="reservation-title">RÉSERVATION</h1>
            <hr class="divider">
            <p class="reservation-text">Pour toute réservation Merci de nous contacter</p>
        </div>
    </div>

    <div class="form-section">
        <h2>Formulaire de Réservation</h2>
        <form>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input type="tel" id="phone" name="phone" placeholder="Entrez votre numéro de téléphone" required>
            </div>

            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" placeholder="Entrez votre adresse" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
            </div>

            <div class="form-group">
                <label for="start-date">Date de début</label>
                <input type="date" id="start-date" name="start-date" required>
            </div>

            <div class="form-group">
                <label for="end-date">Date de fin</label>
                <input type="date" id="end-date" name="end-date" required>
            </div>

            <div class="form-group">
                <button type="submit">Réserver</button>
            </div>
        </form>
    </div>
</body>
</html>





