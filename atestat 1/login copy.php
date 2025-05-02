

<?php
// /user/login.php
//include 'header.php';
require_once 'db.php';
require_once 'functions.php';

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
        $error = "Utilizator sau parolă incorecteee.";
    }
}
?>


<?php
// Aici puteți include header-ul și footer-ul
// include 'header.php';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagină de Login</title>
    <style>
        /* Resetare stiluri pentru elementele din pagina de login */
        #login_wrapper * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        /* Container principal pentru login - izolează stilurile */
        #login_wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background-color: rgba(247, 247, 247, 0.8);
        }
        
        #login_container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1000;  /* Pentru a fi peste elementele din header */
        }
        
        #login_title {
            margin-bottom: 1.8rem;
            text-align: center;
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: bold;
        }
        
        .login_form_group {
            margin-bottom: 1.2rem;
        }
        
        .login_label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: bold;
            color: #333;
        }
        
        .login_input {
            width: 100% !important;  /* Important pentru a suprascrie alte stiluri */
            padding: 0.8rem !important;
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            font-size: 1rem !important;
            transition: border-color 0.3s !important;
            height: auto !important;  /* Suprascrie height-ul care ar putea fi setat în alte stiluri */
            background-color: white !important;
            color: #333 !important;
            display: block !important;
        }
        
        .login_input:focus {
            border-color: #2c3e50 !important;
            outline: none !important;
            box-shadow: 0 0 5px rgba(44, 62, 80, 0.2) !important;
        }
        
        #login_submit_btn {
            background-color: #2c3e50 !important;
            color: white !important;
            border: none !important;
            padding: 0.9rem 1rem !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            font-size: 1rem !important;
            width: 100% !important;
            transition: background-color 0.3s !important;
            display: block !important;
            text-align: center !important;
            margin: 0 !important;
            text-transform: none !important;
        }
        
        #login_submit_btn:hover {
            background-color: #1a2530 !important;
        }
        
        #login_error_message {
            color: #e74c3c !important;
            margin-top: 1rem !important;
            text-align: center !important;
            display: none;
            font-size: 0.9rem !important;
            padding: 0.5rem !important;
            background-color: #fadbd8 !important;
            border-radius: 4px !important;
        }
        
        #login_forgot_password {
            display: block !important;
            text-align: center !important;
            margin-top: 1.2rem !important;
            color: #2c3e50 !important;
            text-decoration: none !important;
            font-size: 0.9rem !important;
        }
        
        #login_forgot_password:hover {
            text-decoration: underline !important;
        }
        
        #login_signup_link {
            text-align: center !important;
            margin-top: 1.5rem !important;
            font-size: 0.9rem !important;
            color: #555 !important;
        }
        
        #login_signup_link a {
            color: #2c3e50 !important;
            text-decoration: none !important;
            font-weight: bold !important;
        }
        
        #login_signup_link a:hover {
            text-decoration: underline !important;
        }
        
        #login_loading {
            display: none;
            text-align: center;
            margin-top: 1rem;
        }
        
        #login_spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2c3e50;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: login_spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes login_spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Stil pentru overlay care poate ascunde elementele de header */
        .login_overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 999;
            display: none; /* Setați la block dacă doriți să ascundeți complet header-ul */
        }
        
        /* Stiluri responsive */
        @media (max-width: 768px) {
            #login_container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Opțional: Dacă doriți să ascundeți complet header-ul, activați acest overlay -->
    <div class="login_overlay"></div>
    
    <div id="login_wrapper">
        <div id="login_container">
            <h2 id="login_title">Autentificare</h2>
            <form id="login_form">
                <div class="login_form_group">
                    <label for="login_username" class="login_label">Nume utilizator sau Email</label>
                    <input type="text" id="login_username" name="username" class="login_input" required>
                </div>
                <div class="login_form_group">
                    <label for="login_password" class="login_label">Parolă</label>
                    <input type="password" id="login_password" name="password" class="login_input" required>
                </div>
                <button type="submit" id="login_submit_btn">Conectare</button>
                <div id="login_loading">
                    <div id="login_spinner"></div>
                </div>
                <div id="login_error_message">Nume de utilizator sau parolă incorectă!</div>
                <a href="#" id="login_forgot_password">Ai uitat parola?</a>
                <div id="login_signup_link">
                    Nu ai un cont? <a href="signup.php">Înregistrează-te acum</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verifică dacă există un header și ajustează paddingul containerului de login
            const header = document.querySelector('header');
            if (header) {
                const headerHeight = header.offsetHeight;
                document.getElementById('login_wrapper').style.paddingTop = (headerHeight + 20) + 'px';
            }
            
            // Logica de autentificare
            document.getElementById('login_form').addEventListener('submit', function(event) {
                event.preventDefault();
                
                const username = document.getElementById('login_username').value;
                const password = document.getElementById('login_password').value;
                const errorMessage = document.getElementById('login_error_message');
                const loadingIndicator = document.getElementById('login_loading');
                
                // Ascunde mesajul de eroare dacă există
                errorMessage.style.display = 'none';
                
                // Arată indicatorul de loading
                loadingIndicator.style.display = 'block';
                
                // Simulare verificare credențiale (într-o aplicație reală, aceasta ar fi făcută pe server)
                setTimeout(function() {
                    // Ascunde indicatorul de loading după "procesare"
                    loadingIndicator.style.display = 'none';
                    
                    if (username === 'admin' && password === 'password123') {
                        // Autentificare reușită
                        alert('Autentificare reușită! Redirecționare...');
                        // În mod normal, aici ar fi o redirecționare către pagina principală
                        // window.location.href = 'dashboard.php';
                    } else {
                        // Autentificare eșuată
                        errorMessage.style.display = 'block';
                        
                        // Ascunde mesajul de eroare după 3 secunde
                        setTimeout(function() {
                            errorMessage.style.display = 'none';
                        }, 3000);
                    }
                }, 1000); // Simulare întârziere de rețea de 1 secundă
            });
        });
    </script>
</body>
</html>
<?php
// include 'footer.php';
?>