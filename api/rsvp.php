<?php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';

try {
    $pdo = getPdo();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível conectar ao banco de dados.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $name      = isset($input['name']) ? trim((string) $input['name']) : '';
    $attending = (isset($input['attending']) && $input['attending'] === 'nao') ? 'nao' : 'sim';
    $guests    = isset($input['guests']) ? (int) $input['guests'] : 1;
    $guests    = max(1, min(10, $guests));

    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Nome é obrigatório.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO tb_convidados (nome, presenca, acompanhantes) VALUES (:nome, :presenca, :acompanhantes)');
    $stmt->execute([
        ':nome'          => $name,
        ':presenca'      => $attending,
        ':acompanhantes' => $guests,
    ]);

    echo json_encode(['success' => true]);
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) AS confirmedCount, COALESCE(SUM(acompanhantes), 0) AS totalGuests FROM tb_convidados WHERE presenca = 'sim'");
$row = $stmt->fetch();

echo json_encode([
    'confirmedCount' => (int) $row['confirmedCount'],
    'totalGuests'    => (int) $row['totalGuests'],
]);
