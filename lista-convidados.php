<?php
date_default_timezone_set('America/Sao_Paulo');

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require __DIR__ . '/api/db.php';

$rows = [];
$errorMessage = null;

try {
    $pdo = getPdo();
    $stmt = $pdo->query('SELECT nome, presenca, acompanhantes, criado_em FROM tb_convidados ORDER BY criado_em DESC');
    $rows = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = 'Não foi possível carregar a lista de convidados agora.';
}

$confirmedCount = 0;
$declinedCount  = 0;
$totalGuests    = 0;

foreach ($rows as $row) {
    if ($row['presenca'] === 'sim') {
        $confirmedCount++;
        $totalGuests += (int) $row['acompanhantes'];
    } else {
        $declinedCount++;
    }
}

$cssVersion = file_exists(__DIR__ . '/css/style.css') ? filemtime(__DIR__ . '/css/style.css') : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<meta name="theme-color" content="#f9c9d9">
<title>Lista de Convidados 🎀</title>
<link rel="icon" href="imagemConvite.jpeg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=<?= $cssVersion ?>">
</head>
<body>

<main id="mainApp">
  <section class="invite-card show">
    <div class="card">

      <h1 class="title-script">Lista de Convidados 🎀</h1>
      <h2 class="subtitle">Aniversário da <span class="name-isa">Isa</span> e do <span class="name-sinval">Sinval</span></h2>

      <div class="guest-summary">
        <span class="summary-pill">🎉 <?= $confirmedCount ?> confirmação(ões)</span>
        <span class="summary-pill">👥 <?= $totalGuests ?> pessoa(s)</span>
        <span class="summary-pill">😢 <?= $declinedCount ?> não vão</span>
      </div>

      <?php if ($errorMessage): ?>
        <p class="empty-state"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
      <?php elseif (empty($rows)): ?>
        <p class="empty-state">Ainda não há confirmações registradas.</p>
      <?php else: ?>
        <div class="guest-table-wrap">
          <table class="guest-table">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Vai à festa?</th>
                <th>Pessoas</th>
                <th>Confirmado em</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($rows as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <?php if ($row['presenca'] === 'sim'): ?>
                      <span class="badge badge-sim">Sim 🎉</span>
                    <?php else: ?>
                      <span class="badge badge-nao">Não 😢</span>
                    <?php endif; ?>
                  </td>
                  <td><?= (int) $row['acompanhantes'] ?></td>
                  <td><?= date('d/m/Y H:i', strtotime($row['criado_em'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <p class="footer-note">Com carinho, esperamos por vocês! 🐾🎀</p>
    </div>
  </section>
</main>

</body>
</html>
