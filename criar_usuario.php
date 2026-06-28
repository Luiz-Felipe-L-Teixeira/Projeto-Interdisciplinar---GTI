<?php
$host = "localhost"; $user = "root"; $pass = ""; $db = "mapa_cultural";
$conn = new mysqli($host, $user, $pass, $db);

$nome  = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha']; // Em produção, use password_hash
$tipo  = $_POST['tipo'];

$sql = "INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $senha, $tipo);

if ($stmt->execute()) {
    header("Location: coordenador.html?sucesso=1");
} else {
    echo "Erro ao criar usuário: " . $conn->error;
}
?>