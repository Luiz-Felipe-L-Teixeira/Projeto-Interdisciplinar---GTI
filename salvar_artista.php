<?php
session_start();


$host = "localhost";
$user = "root";
$pass = "";
$db   = "mapa_cultural";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if (!isset($_SESSION['id_usuario'])) {
    die("Faça login primeiro.");
}

$id_usuario = $_SESSION['id_usuario'];
$email = $_SESSION['email'];
$nome_agente = $_SESSION['nome'];

/*
 * Dados do formulário
 */
$nome_coletivo = $_POST['nome_grupo'];
$descricao_atuacao = $_POST['categoria'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$link_rede_social = $_POST['link_rede_social'] ?? '';

/*
 * Busca dados do agente territorial
 */
$sqlAgente = "
SELECT
    identificador_agente,
    territorio_atuacao,
    descricao_territorio
FROM agente_territorial
WHERE identificador_usuario = ?
";

$stmtAgente = $conn->prepare($sqlAgente);
$stmtAgente->bind_param("i", $id_usuario);
$stmtAgente->execute();

$agente = $stmtAgente->get_result()->fetch_assoc();

if (!$agente) {
    die("Agente territorial não encontrado.");
}

$identificador_agente = $agente['identificador_agente'];
$territorio_atuacao = $agente['territorio_atuacao'];
$descricao_territorio = $agente['descricao_territorio'];

/*
 * Salva geolocalização
 */
$sqlGeo = "
INSERT INTO geolocalizacao
(latitude, longitude)
VALUES (?, ?)
";

$stmtGeo = $conn->prepare($sqlGeo);
$stmtGeo->bind_param("dd", $latitude, $longitude);

if (!$stmtGeo->execute()) {
    die("Erro ao salvar geolocalização.");
}

$identificador_geolocalizacao = $conn->insert_id;

/*
 * Salva coletivo cultural
 */
$sqlColetivo = "
INSERT INTO coletivo_cultural
(
    identificador_geolocalizacao,
    identificador_agente,
    nome_coletivo_cultural,
    descricao_atuacao,
    data_cadastro,
    link_rede_social_coletivo,
    email,
    nome_agente_responsavel,
    territorio_atuacao,
    descricao_territorio
)
VALUES
(
    ?, ?, ?, ?, NOW(),
    ?, ?, ?, ?, ?
)
";

$stmt = $conn->prepare($sqlColetivo);

$stmt->bind_param(
    "iisssssss",
    $identificador_geolocalizacao,
    $identificador_agente,
    $nome_coletivo,
    $descricao_atuacao,
    $link_rede_social,
    $email,
    $nome_agente,
    $territorio_atuacao,
    $descricao_territorio
);

if ($stmt->execute()) {
    header("Location: agente.html?sucesso=1");
    exit;
} else {
    echo "Erro: " . $stmt->error;
}

$conn->close();
?>