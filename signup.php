<?php
session_start(); // Initialiser la session en haut du fichier

class Client {
    public $firstname;
    public $lastname;
    public $email;
    public $password;
    public static $errorMsg = "";
    public static $successMsg = "";

    public function __construct($firstname, $lastname, $email, $password) {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
    }

    public function insertClient($tableName, $conn) {
        $sql = "INSERT INTO $tableName (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $this->firstname, $this->lastname, $this->email, $this->password);

        if ($stmt->execute()) {
            self::$successMsg = "Client successfully registered.";
            return true;
        } else {
            self::$errorMsg = "Error: " . $stmt->error;
            return false;
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <style>
        /* Ajout de styles */
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
        }
        .container {
            width: 300px;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        input[type="text"], input[type="password"], input[type="email"] {
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
    </style>
</head>
<body>
<?php
// Initialisation des variables
$first_name = $last_name = $email = '';

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation des entrées
    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        // Connexion à la base de données
        $conn = new mysqli("localhost", "root", "", "location");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $client = new Client($first_name, $last_name, $email, $password);

        if ($client->insertClient("Clients", $conn)) {
            // Stocker l'email de l'utilisateur dans la session
            $_SESSION['user_email'] = $email;

            // Redirection vers la page d'accueil
            header("Location: home.php");
            exit();
        } else {
            echo "<script>alert('" . Client::$errorMsg . "');</script>";
        }
        $conn->close();
    } else {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>
<div class="container">
    <h2>Sign Up</h2>
    <form action="signup.php" method="post">
        <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($first_name); ?>" required>
        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($last_name); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Sign Up</button>
    </form>
    <div class="signup">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
</div>
</body>
</html>
