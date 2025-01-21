<?php
include 'Connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
    $lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    // Vérifier si tous les champs sont remplis
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($role)) {
        echo "Tous les champs doivent être remplis.";
        exit();
    }

    // Vérifier si le mot de passe se termine bien par 'cars'
    if (substr($password, -4) !== 'cars') {
        echo "Le mot de passe doit se terminer par 'cars'.";
        exit();
    }

    // Hachage du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Connexion à la base de données et insertion
    $conn = new Connection();
    $stmt = $conn->conn->prepare("INSERT INTO Admins (firstname, lastname, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Admin registered successfully!";
        header("Location: admin_login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up</title>
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
            background: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        input[type="text"], input[type="password"] {
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

        .login-link {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
        }

        .login-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sign Up Admin</h2>
        <form action="admin_signup.php" method="POST">
            <input type="text" name="firstname" placeholder="Firstname" required>
            <input type="text" name="lastname" placeholder="Lastname" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required pattern=".*cars$" title="Le mot de passe doit se terminer par 'cars'">
            <input type="text" name="role" placeholder="Role" required>
            <button type="submit">Sign Up</button>
        </form>
        <div class="login-link">
            Déjà un compte ? <a href="admin_login.php">Connectez-vous</a>
        </div>
    </div>
</body>
</html>
