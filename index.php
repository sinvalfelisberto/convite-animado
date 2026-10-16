<?php
date_default_timezone_set('America/Sao_Paulo');

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$eventDate = new DateTime('2026-08-22 15:00:00');
$weekdays  = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$weekday   = $weekdays[(int) $eventDate->format('w')];

$address     = 'QRI 22 Casa 12 - Residencial Santos Dumont - Santa Maria - DF';
$mapsCoords  = '-15.9980031,-47.9979630';
$mapsLink    = "https://www.google.com/maps/search/?api=1&query={$mapsCoords}";
$eventIsoUtc = $eventDate->format('Ymd\THis');

$cssVersion = file_exists(__DIR__ . '/css/style.css') ? filemtime(__DIR__ . '/css/style.css') : time();
$jsVersion  = file_exists(__DIR__ . '/js/script.js') ? filemtime(__DIR__ . '/js/script.js') : time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#f9c9d9">
<title>Convite de Aniversário 🎀</title>
<link rel="icon" href="imagemConvite.jpeg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=<?= $cssVersion ?>">
</head>
<body>

<div class="floating-decor" id="floatingDecor"></div>

<!-- Aviso exibido apenas quando o convite é aberto fora de um celular -->
<div class="desktop-warning" id="desktopWarning">
  <div class="desktop-warning-box">
    <span class="desktop-warning-emoji">📱💌</span>
    <h2>Abra no seu celular!</h2>
    <p>Este convite foi feito especialmente para ser aberto em um dispositivo móvel. Acesse este link pelo seu celular para ver a surpresa completa 🎀</p>
  </div>
</div>

<main id="mainApp">

  <section id="envelopeScreen" class="envelope-screen">
    <div class="envelope" id="envelope">
      <div class="envelope-shadow"></div>
      <div class="envelope-body">
        <div class="envelope-letter">
          <img src="imagemConvite.jpeg" alt="Hello Kitty" class="envelope-kitty">
          <p>Um convite especial para você...</p>
        </div>
        <div class="envelope-flap-back"></div>
        <div class="envelope-front-left"></div>
        <div class="envelope-front-right"></div>
        <div class="envelope-flap" id="envelopeFlap"></div>
        <div class="envelope-seal" id="envelopeSeal">🎀</div>
      </div>
    </div>
    <button id="openBtn" class="btn-open">💌 Toque para abrir o convite</button>
  </section>

  <section id="inviteCard" class="invite-card">
    <div class="card">

      <img src="imagemConvite.jpeg" class="hero-img" alt="Hello Kitty e amiga se abraçando">

      <h1 class="title-script">Venha comemorar conosco!</h1>
      <h2 class="subtitle">Aniversário da <span class="name-isa">Isa</span> e do <span class="name-sinval">Sinval</span></h2>

      <div class="countdown" id="countdown" data-event="<?= htmlspecialchars($eventIsoUtc, ENT_QUOTES, 'UTF-8') ?>">
        <div class="cd-box"><span id="cdDays">00</span><small>dias</small></div>
        <div class="cd-box"><span id="cdHours">00</span><small>horas</small></div>
        <div class="cd-box"><span id="cdMin">00</span><small>min</small></div>
        <div class="cd-box"><span id="cdSec">00</span><small>seg</small></div>
      </div>

      <div class="details">
        <div class="detail-item">
          <span class="detail-icon">📅</span>
          <div><strong><?= $weekday ?></strong><br>22 de agosto de 2026</div>
        </div>
        <div class="detail-item">
          <span class="detail-icon">🕒</span>
          <div><strong>Às 15h</strong></div>
        </div>
        <div class="detail-item">
          <span class="detail-icon">📍</span>
          <div>
            <strong>Residencial Santos Dumont</strong><br>
            <?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?>
            <br><a href="<?= $mapsLink ?>" target="_blank" rel="noopener" class="maps-link">Ver no mapa ↗</a>
          </div>
        </div>
      </div>

      <div class="actions">
        <button id="addCalendarBtn" type="button" class="btn">📆 Adicionar ao calendário</button>
        <a href="<?= $mapsLink ?>" target="_blank" rel="noopener" class="btn btn-outline">🗺️ Como chegar</a>
      </div>

      <form id="rsvpForm" class="rsvp-form" autocomplete="off">
        <h3>Confirme sua presença 💕</h3>

        <label class="field">
          <span>Seu nome</span>
          <input type="text" name="name" id="rsvpName" placeholder="Digite seu nome" required>
        </label>

        <div class="rsvp-row">
          <label class="field">
            <span>Vai comparecer?</span>
            <select name="attending" id="rsvpAttending">
              <option value="sim">Sim! 🎉</option>
              <option value="nao">Não posso 😢</option>
            </select>
          </label>
          <label class="field">
            <span>Nº de pessoas</span>
            <input type="number" name="guests" id="rsvpGuests" min="1" max="10" value="1">
          </label>
        </div>

        <button type="submit" class="btn btn-submit">Enviar confirmação</button>
        <p id="rsvpMsg" class="rsvp-msg"></p>
        <p id="rsvpCount" class="rsvp-count"></p>
      </form>

      <p class="footer-note">Com carinho, esperamos por você! 🐾🎀</p>
    </div>
  </section>

</main>

<script src="js/script.js?v=<?= $jsVersion ?>"></script>
</body>
</html>
