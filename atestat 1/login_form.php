<?php
// /user/login.php
//include 'header.php';
require_once 'db.php';
require_once 'functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Utilizator sau parolă incorectă.";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular Login</title>
    <style>
        /* Stiluri complet izolate care nu vor afecta și nu vor fi afectate de pagina părinte */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            padding: 0;
            margin: 0;
            overflow-x: hidden;
        }
        
        .login_form_container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 380px;
        }
        
        .login_form_title {
            margin-bottom: 25px;
            text-align: center;
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
        }
        
        .login_form_group {
            margin-bottom: 20px;
        }
        
        .login_form_label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .login_form_input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .login_form_input:focus {
            border-color: #2c3e50;
            outline: none;
            box-shadow: 0 0 5px rgba(44, 62, 80, 0.2);
        }
        
        .login_form_button {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .login_form_button:hover {
            background-color: #1a2530;
        }
        
        .login_form_error {
            color: #e74c3c;
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
            padding: 8px;
            background-color: #fadbd8;
            border-radius: 4px;
            display: <?php echo !empty($error) ? 'block' : 'none'; ?>;
        }
        
        .login_form_forgot {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #2c3e50;
            text-decoration: none;
            font-size: 14px;
        }
        
        .login_form_forgot:hover {
            text-decoration: underline;
        }
        
        .login_form_signup {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        
        .login_form_signup a {
            color: #2c3e50;
            text-decoration: none;
            font-weight: bold;
        }
        
        .login_form_signup a:hover {
            text-decoration: underline;
        }
        
        .login_form_loading {
            display: none;
            text-align: center;
            margin-top: 15px;
        }
        
        .login_form_spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2c3e50;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: login_form_spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes login_form_spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="login_form_container">
        <h2 class="login_form_title">Autentificare</h2>
        <form id="loginFormIsolated" method="post">
            <div class="login_form_group">
                <label for="login_form_username" class="login_form_label">Nume utilizator sau Email</label>
                <input type="text" id="login_form_username" name="username" class="login_form_input" required>
            </div>
            <div class="login_form_group">
                <label for="login_form_password" class="login_form_label">Parolă</label>
                <input type="password" id="login_form_password" name="password" class="login_form_input" required>
            </div>
            <button type="submit" class="login_form_button">Conectare</button>
            <div class="login_form_loading">
                <div class="login_form_spinner"></div>
            </div>
            <div id="login_form_error" class="login_form_error">
                <?php echo $error; ?>
            </div>
            <a href="#" class="login_form_forgot">Ai uitat parola?</a>
            <div class="login_form_signup">
                Nu ai un cont? <a href="sign-up.php" onclick="window.parent.location.href='sign-up.php'; return false;">Înregistrează-te acum</a>
            </div>
        </form>
    </div>

    <script>
        // Trimite înălțimea iframe-ului către părinte pentru a ajusta dimensiunea
        function updateParentHeight() {
            const height = document.body.scrollHeight;
            window.parent.postMessage({
                type: 'login_height',
                height: height
            }, '*');
        }
        
        // Actualizează înălțimea la încărcare și la redimensionare
        window.addEventListener('load', updateParentHeight);
        window.addEventListener('resize', updateParentHeight);
        
        // Logica de autentificare
        document.getElementById('loginFormIsolated').addEventListener('submit', function(e) {
            // Uncomment this if you want to handle form submission with JavaScript
            // e.preventDefault();
            
            // Show loading spinner
            document.querySelector('.login_form_loading').style.display = 'block';
            
            // If you want to handle form submission via AJAX, uncomment and complete this section
            /*
            const username = document.getElementById('login_form_username').value;
            const password = document.getElementById('login_form_password').value;
            
            // Send AJAX request
            fetch('login_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.parent.location.href = 'index.php';
                } else {
                    document.getElementById('login_form_error').textContent = data.message;
                    document.getElementById('login_form_error').style.display = 'block';
                }
            })
            .catch(error => {
                document.getElementById('login_form_error').textContent = 'A apărut o eroare. Vă rugăm încercați din nou.';
                document.getElementById('login_form_error').style.display = 'block';
            })
            .finally(() => {
                document.querySelector('.login_form_loading').style.display = 'none';
            });
            */
        });
    </script>
</body>
</html>