<?php
// Acest fișier este login.php (pagina principală care include header-ul)
session_start(); // Asigură-te că sesiunea este pornită

// Verifică dacă utilizatorul e deja autentificat
if (isset($_SESSION['user_id'])&&$_SESSION['user_id']>0) {
    // Dacă utilizatorul e deja autentificat, redirecționează către pagina principală
  header("Location: profile.php");
   exit();
}

// Include header-ul normal al site-ului
include 'header.php'; 
?>

<div class="login_page_wrapper" style="width: 100%; height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; background: transparent;">
    <!-- Folosim un iframe care va încărca formularul de login dintr-un fișier separat -->
    <iframe src="login_form.php" id="login_iframe" style="width: 420px; height: 500px; border: none; overflow: hidden;"></iframe>
</div>

<script>
// Script pentru a ajusta înălțimea iframe-ului automat
window.addEventListener('message', function(event) {
    if (event.data.type === 'login_height') {
        document.getElementById('login_iframe').style.height = event.data.height + 'px';
    }
    
    // Gestionăm redirecționarea din iframe
    if (event.data.type === 'login_success') {
        window.location.href = event.data.redirect;
    }
});
</script>

<?php 
// Include footer-ul normal
// include 'footer.php'; 
?>