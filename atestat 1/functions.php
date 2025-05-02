<?php
// functions.php
$url=$_SERVER['HTTP_HOST'];
function getUrl(){
    $curent_url="http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
    return $curent_url;
}
$link=getUrl();
//echo $link;
// Sanitizare input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Obține detaliile unui produs după ID
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
