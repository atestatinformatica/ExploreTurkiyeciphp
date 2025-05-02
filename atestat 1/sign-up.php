
<?php
// /user/register.php
session_start();
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    if ($password !== $password_confirm) {
        $error = "Parolele nu se potrivesc.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password_hash])) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header("Location: profile.php");
            exit();
        } else {
            $error = "Eroare la înregistrare.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagină de Înregistrare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f7f7f7;
            padding: 2rem;
        }
        
        .signup-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .signup-container h2 {
            margin-bottom: 1.5rem;
            text-align: center;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn {
            background-color: #2c3e50;
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn:hover {
            background-color: #1a2530;
        }
        
        .error-message {
            color: red;
            margin-top: 1rem;
            text-align: center;
            display: none;
        }
        
        .success-message {
            color: green;
            margin-top: 1rem;
            text-align: center;
            display: none;
        }
        
        .password-strength {
            height: 5px;
            background-color: #ddd;
            margin-top: 0.5rem;
            border-radius: 2px;
            position: relative;
        }
        
        .password-strength-bar {
            height: 100%;
            border-radius: 2px;
            width: 0%;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .password-feedback {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            color: #666;
        }
        
        .terms {
            margin-top: 1rem;
        }
        
        .terms label {
            display: flex;
            align-items: center;
        }
        
        .terms input {
            margin-right: 0.5rem;
        }
        
        .login-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Creează un cont nou</h2>
        <form id="signupForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">Prenume</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Nume</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="password">Parolă</label>
                <input type="password" id="password" name="password" required>
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="password-feedback" id="passwordFeedback">Minim 8 caractere</div>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirmă parola</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="terms">
                <label>
                    <input type="checkbox" id="termsAgree" name="termsAgree" required>
                    Sunt de acord cu <a href="#">Termenii și Condițiile</a> și <a href="#">Politica de Confidențialitate</a>
                </label>
            </div>
            <div class="terms">
                <label>
                    <input type="checkbox" id="newsletterAgree" name="newsletterAgree">
                    Doresc să primesc newsletter-ul și alte comunicări de marketing
                </label>
            </div>
            <button type="submit" class="btn">Creează cont</button>
            <div id="errorMessage" class="error-message">Eroare la crearea contului!</div>
            <div id="successMessage" class="success-message">Cont creat cu succes! Vei fi redirecționat...</div>
        </form>
        <div class="login-link">
            Ai deja un cont? <a href="login.php">Conectează-te aici</a>
        </div>
    </div>

    <script>
        // Verificare putere parolă
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            const feedback = document.getElementById('passwordFeedback');
            
            // Calculează puterea parolei (simplificat)
            let strength = 0;
            
            // Lungimea minimă
            if (password.length >= 8) {
                strength += 25;
            }
            
            // Verifică pentru litere mari
            if (/[A-Z]/.test(password)) {
                strength += 25;
            }
            
            // Verifică pentru cifre
            if (/[0-9]/.test(password)) {
                strength += 25;
            }
            
            // Verifică pentru caractere speciale
            if (/[^A-Za-z0-9]/.test(password)) {
                strength += 25;
            }
            
            // Actualizează bara de putere
            strengthBar.style.width = strength + '%';
            
            // Setează culoarea în funcție de putere
            if (strength < 25) {
                strengthBar.style.backgroundColor = '#ff4d4d'; // roșu
                feedback.textContent = 'Parolă foarte slabă';
            } else if (strength < 50) {
                strengthBar.style.backgroundColor = '#ffa64d'; // portocaliu
                feedback.textContent = 'Parolă slabă';
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = '#ffff4d'; // galben
                feedback.textContent = 'Parolă medie';
            } else if (strength < 100) {
                strengthBar.style.backgroundColor = '#4dff4d'; // verde deschis
                feedback.textContent = 'Parolă bună';
            } else {
                strengthBar.style.backgroundColor = '#2eb82e'; // verde
                feedback.textContent = 'Parolă puternică';
            }
        });
        
        // Logica de înregistrare
        document.getElementById('signupForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const termsAgree = document.getElementById('termsAgree').checked;
            
            const errorMessage = document.getElementById('errorMessage');
            const successMessage = document.getElementById('successMessage');
            
            // Validare
            if (password !== confirmPassword) {
                errorMessage.textContent = 'Parolele nu coincid!';
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 3000);
                return;
            }
            
            if (password.length < 8) {
                errorMessage.textContent = 'Parola trebuie să aibă minim 8 caractere!';
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 3000);
                return;
            }
            
            // Verificare format email (simplificat)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorMessage.textContent = 'Adresa de email nu este validă!';
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 3000);
                return;
            }
            
            // Verificare acceptare termeni
            if (!termsAgree) {
                errorMessage.textContent = 'Trebuie să accepți termenii și condițiile!';
                errorMessage.style.display = 'block';
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 3000);
                return;
            }
            
            // Simulare înregistrare reușită (în aplicații reale, aceasta ar fi trimisă la server)
            successMessage.style.display = 'block';
            
            console.log('Cont creat:');
            console.log({
                firstName,
                lastName,
                email,
                phone,
                newsletter: document.getElementById('newsletterAgree').checked
            });
            
            // Resetează formularul
            this.reset();
            
            // Simulare redirecționare
            setTimeout(() => {
               // alert('Înregistrare reușită! În mod normal ai fi redirecționat către pagina de login.');
             window.location.href = 'login.php';
            }, 2000);
        });
    </script>
</body>
</html>