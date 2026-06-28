<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "mapa_cultural";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}

$sql = "
SELECT
    c.nome_coletivo_cultural AS nome_grupo,
    c.descricao_atuacao AS categoria,
    g.latitude,
    g.longitude
FROM coletivo_cultural c
INNER JOIN geolocalizacao g
    ON c.identificador_geolocalizacao = g.identificador_geolocalizacao
";

$result = $conn->query($sql);

$dados = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($dados);

$conn->close();
?>