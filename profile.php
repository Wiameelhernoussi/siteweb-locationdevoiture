<?php  
session_start();  

include 'connection.php'; // Inclusion du fichier de connexion  

class UserProfile {  
    private $conn;  

    public function __construct($dbConnection) {  
        $this->conn = $dbConnection; // Stockage de la connexion à la base de données  
    }  

    public function getUserByEmail($email) {  
        $stmt = $this->conn->prepare("SELECT id, firstname, lastname, email, password FROM Clients WHERE email = ?");  
        $stmt->bind_param("s", $email);  
        $stmt->execute();  
        $result = $stmt->get_result();  
        return $result->fetch_assoc();  
    }  

    public function updateUser($id, $firstname, $lastname, $email, $password) {  
        // Préparation de la requête pour mettre à jour l'utilisateur, incluant le mot de passe  
        $stmt = $this->conn->prepare("UPDATE Clients SET firstname = ?, lastname = ?, email = ?, password = ? WHERE id = ?");  
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe avant de le stocker  
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashedPassword, $id);  
        return $stmt->execute();  
    }  
}  

// Vérification de session  
if (!isset($_SESSION['user_email'])) {  
    header("Location: login.php");  
    exit();  
}  

$database = new Connection(); // Création d'une instance de la classe Connection  
$conn = $database->conn; // Récupération de la connexion à la base de données  

$userProfile = new UserProfile($conn); // Création d'une instance de UserProfile  

$user_email = $_SESSION['user_email'];  
$user = $userProfile->getUserByEmail($user_email);  

if ($user === null) {  
    echo "User not found.";  
    exit();  }  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $new_firstname = $_POST['first_name'];  
    $new_lastname = $_POST['last_name'];  
    $new_email = $_POST['email'];  
    $new_password = $_POST['password'];  

    // Validation de l'email  
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {  
        echo "<script>alert('Invalid email format.');</script>";  
    } else {  
        // Mise à jour des informations  
        if ($userProfile->updateUser($user['id'], $new_firstname, $new_lastname, $new_email, $new_password)) {  
            $_SESSION['user_email'] = $new_email; // Mettre à jour l'email de session  
            echo "<script>alert('Profile updated successfully.');</script>";  
            header("Location: home.php"); // Redirection après mise à jour  
            exit();  
        } else {  
            echo "Error updating record: " . $conn->error;  
        }  
    }  
}  

$database->closeConnection(); // Fermer la connexion  
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>Edit Profile</title>  
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
        }  

        .container {  
            width: 300px;  
            padding: 20px;  
            border: 1px solid #ccc;  
            border-radius: 10px;  
            background: rgba(255, 255, 255, 0.85);  
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);  
        }  

        input[type="text"], input[type="email"], input[type="password"] {  
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
            margin-top: 10px;  
        }  

        button:hover {  
            background-color: #45a049;  
        }  

        .cancel-btn {  
            background-color: #f44336; /* Rouge pour le bouton Cancel */  
        }  

        .cancel-btn:hover {  
            background-color: #d32f2f;  
        }  

        label {  
            font-weight: bold;  
        }  
    </style>  
</head>  
<body>  
<div class="container">  
    <h2>Edit Profile</h2>  
    <form action="profile.php" method="post">  
        <label for="id">ID (readonly)</label>  
        <input type="text" id="id" value="<?php echo htmlspecialchars($user['id']); ?>" readonly>  

        <label for="first_name">First Name</label>  
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>  

        <label for="last_name">Last Name</label>  
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>  

        <label for="email">Email</label>  
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>  

        <label for="password">New Password</label>  
        <input type="password" name="password" placeholder="Enter new password" required>  

        <button type="submit">Update Profile</button>  
        <button type="button" class="cancel-btn" onclick="window.location.href='home.php';">Cancel</button>  
    </form>  
</div>  
</body>  
</html>