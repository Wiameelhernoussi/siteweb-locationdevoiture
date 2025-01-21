<?php
session_start();
include 'Connection.php';

// Rediriger l'utilisateur s'il est déjà connecté
if (isset($_SESSION['user_email'])) {
    header("Location: update_cars.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $conn = new Connection();
    $stmt = $conn->conn->prepare("SELECT password FROM Admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Vérifier si le mot de passe fourni correspond au mot de passe haché
    if (password_verify($password, $hashed_password)) {
        // Connexion réussie, stocker l'email dans la session
        $_SESSION['user_email'] = $email;

        // Rediriger vers update_cars.php
        header("Location: update_cars.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }

    $conn->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('https://porschepictures.flowcenter.de/pmdb/thumbnail.cgi?id=298491&w=1935&h=1089&crop=1&public=1&cs=4d71adcf6b1101c1');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            width: 300px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .signup {
            margin-top: 15px;
            font-size: 14px;
        }

        .signup a {
            color: rgb(13, 123, 46);
            text-decoration: none;
        }

        .signup a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Connexion Admin</h2>
    <form method="POST" action="admin_login.php">
        <input type="email" name="email" placeholder="email" required>
        <input type="password" name="password" placeholder="password" required>
        <button type="submit">Se connecter</button>
    </form>
    <div class="signup">
        Pas de compte ? <a href="admin_signup.php">Inscrivez-vous</a>
    </div>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
</div>
</body>
</html>
