<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "mapa_cultural";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($email) || empty($senha)) {
    die("Preencha todos os campos.");
}

$sql = "SELECT * FROM usuario WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro SQL: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $usuario = $result->fetch_assoc();

    // Para senha salva em texto simples
    if ($senha == $usuario['senha_criptografada']) {

        $_SESSION['id_usuario'] = $usuario['identificador_usuario'];
        $_SESSION['nome'] = $usuario['primeiro_nome'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

        header("Location: agente.html");
        exit();
    }
}

echo "E-mail ou senha inválidos.";

$stmt->close();
$conn->close();
?>