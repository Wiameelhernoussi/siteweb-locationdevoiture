<?php  
session_start();  

include 'connection.php'; // Inclure la classe Connection  

class User {  
    private $db;  

    public function __construct(Connection $connection) {  
        $this->db = $connection->conn; }  

    public function login($email, $password) {  
        $errors = [];  
         
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {  
            $errors[] = "Invalid email: please enter a valid email, e.g. name@gmail.com.";  }  

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/', $password)) {  
            $errors[] = "The password must contain at least one letter, one number, and one special character.";  }  
 
        if (empty($errors)) {  
            $sql = "SELECT id, password FROM Clients WHERE email = ?";  
            $stmt = $this->db->prepare($sql);  
            $stmt->bind_param('s', $email);  
            $stmt->execute();  
            $stmt->bind_result($id, $hashed_password);  
            $stmt->fetch();  

            if ($hashed_password) {    
                if (password_verify($password, $hashed_password)) {  
                    $_SESSION['user_email'] = $email;  
                    header("Location: home.php");  
                    exit();  
                } else {  
                    $errors[] = "Incorrect password.";  
                }  
            } else {  
                $errors[] = "Your email is invalid or no account was found.";  
        }  
            $stmt->close();  
        }  

        return $errors;  
    }  
}  

// Process login if form was submitted  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $connection = new Connection(); // Créer une nouvelle instance de Connection  
    $user = new User($connection); // Passer l'instance de Connection au User  

    $email = $_POST['email'];  
    $password = $_POST['password'];  
    $errors = $user->login($email, $password);  

    // Fermer la connexion  
    $connection->closeConnection();  

    // Display errors if any  
    if (!empty($errors)) {  
        echo "<script>  
                alert('" . implode("\\n", $errors) . "');  
                window.location.href = 'signup.php';  
              </script>";  
        exit();  
    }  
}  

?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>Login</title>  
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
    </style>  
</head>  
<body>  
<div class="container">  
    <h2>Login</h2>  
    <form action="login.php" method="post">  
        <input type="email" name="email" placeholder="Email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">  
        <input type="password" name="password" placeholder="Password" required pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$">  
        <button type="submit">Sign In</button>  
    </form>  
    <div class="signup">  
        Don't have an account? <a href="signup.php">Sign Up</a>  
    </div>  
</div>  
</body>  
</html>