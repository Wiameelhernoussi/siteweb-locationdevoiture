<?php  
session_start();  

class Auth {  
    public static function checkSession() {  
        if (!isset($_SESSION['user_email'])) {  
            header("Location: login.php");  
            exit();  
        }  
    }  
}  

class Page {  
    private $title;  
    private $userEmail;  

    public function __construct($title) {  
        $this->title = $title;  
        $this->userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';  
    }  

    public function render() {  
        echo $this->getHeader();  
        echo $this->getContent();  
        echo $this->getFooter();  
    }  

    private function getHeader() {  
        return '  
        <header>  
            <div class="logo">WELCOME</div>  
            <nav>  
                <ul>  
                    <li><a href="home.php">HOME</a></li>  
                    <li><a href="products.php">PRODUCTS</a></li>  
                    <span>' . htmlspecialchars($this->userEmail) . '</span>  
                    <li><a href="profile.php">PROFILE</a></li>  
                    <li><a href="logout.php">LOG OUT</a></li>  
                </ul>  
            </nav>  
        </header>';  
    }  

    private function getContent() {  
        return '  
        <div class="content">  
            <h1>Create something extraordinary</h1>  
            <p>  
                The best car for your business and for your life is here. Come to see it and fall in love. Premium moments await.  
            </p>  
            <a href="more.php">Learn More</a>  
        </div>';  
    }  

    private function getFooter() {  
        return '  
        <footer>  
            <p>+212 707770770 | Hivernage, MARRAKECH, MOROCCO</p>  
        </footer>';  
    }  

    public function getTitle() {  
        return $this->title;  
    }  
}  

Auth::checkSession();  

$page = new Page("Full Page Background");  

?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title><?php echo htmlspecialchars($page->getTitle()); ?></title>  
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
        }  

        header .logo {  
            font-size: 1.5rem;  
            font-weight: bold;  
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
            color: #f0b42f;  
        }  

        .content h1 {  
            font-size: 3rem;  
            margin-bottom: 20px;  
        }  

        .content p {  
            font-size: 1.2rem;  
            max-width: 600px;  
            margin-bottom: 20px;  
        }  

        .content a {  
            padding: 10px 20px;  
            background-color: #f0b42f;  
            color: black;  
            text-decoration: none;  
            font-weight: bold;  
            border-radius: 5px;  
            transition: background-color 0.3s ease;  
        }  

        .content a:hover {  
            background-color: #e09c25;  
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
    <div class="background-image">  
        <?php $page->render(); ?>  
    </div>  
</body>  
</html>