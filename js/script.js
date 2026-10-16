(function () {
  'use strict';

  /* ---------- Mobile-only gate ---------- */
  function isMobileDevice() {
    var uaMobile = /Android|iPhone|iPad|iPod|IEMobile|Opera Mini|Mobile|webOS/i.test(navigator.userAgent);
    var coarsePointer = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
    var narrowScreen = Math.min(window.innerWidth, window.innerHeight) <= 820;
    return uaMobile || (coarsePointer && narrowScreen);
  }

  if (!isMobileDevice()) {
    document.documentElement.classList.add('not-mobile');
    return; // não inicializa o resto do convite em telas não-móveis
  }

  document.addEventListener('DOMContentLoaded', function () {
    initFloatingDecor();
    initEnvelope();
    initCountdown();
    initAddToCalendar();
    initRsvpForm();
    loadRsvpSummary();
  });

  /* ---------- Floating decorations ---------- */
  function initFloatingDecor() {
    var container = document.getElementById('floatingDecor');
    if (!container) return;
    var icons = ['🎀', '💕', '🐾', '⭐', '💗', '🎈', '💙'];
    var total = 14;

    for (var i = 0; i < total; i++) {
      var el = document.createElement('span');
      el.textContent = icons[Math.floor(Math.random() * icons.length)];
      el.style.left = Math.random() * 100 + '%';
      el.style.fontSize = (14 + Math.random() * 16) + 'px';
      el.style.animationDuration = (10 + Math.random() * 14) + 's';
      el.style.animationDelay = (Math.random() * 14) + 's';
      container.appendChild(el);
    }
  }

  /* ---------- Envelope open interaction ---------- */
  function initEnvelope() {
    var openBtn = document.getElementById('openBtn');
    var envelope = document.getElementById('envelope');
    var envelopeScreen = document.getElementById('envelopeScreen');
    var inviteCard = document.getElementById('inviteCard');
    if (!openBtn || !envelope || !inviteCard) return;

    openBtn.addEventListener('click', function () {
      if (envelope.classList.contains('opened')) return;
      envelope.classList.add('opened');
      openBtn.style.opacity = '0';
      openBtn.style.pointerEvents = 'none';

      setTimeout(function () {
        envelopeScreen.classList.add('hide');
        inviteCard.classList.add('show');
        burstConfetti();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }, 900);
    });
  }

  function burstConfetti() {
    var colors = ['#ffc2dd', '#f78fb3', '#b3e5fc', '#c8e6c9', '#fff3c4'];
    var count = 40;
    for (var i = 0; i < count; i++) {
      var piece = document.createElement('div');
      var size = 6 + Math.random() * 6;
      piece.style.position = 'fixed';
      piece.style.top = '-10px';
      piece.style.left = Math.random() * 100 + 'vw';
      piece.style.width = size + 'px';
      piece.style.height = size + 'px';
      piece.style.background = colors[Math.floor(Math.random() * colors.length)];
      piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '3px';
      piece.style.zIndex = '40';
      piece.style.pointerEvents = 'none';
      piece.style.opacity = '0.95';
      var duration = 2200 + Math.random() * 1800;
      var rotation = Math.random() * 720 - 360;
      piece.animate([
        { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
        { transform: 'translateY(100vh) rotate(' + rotation + 'deg)', opacity: 0.9 }
      ], { duration: duration, easing: 'ease-in', fill: 'forwards' });

      document.body.appendChild(piece);
      (function (el, ms) {
        setTimeout(function () { el.remove(); }, ms + 100);
      })(piece, duration);
    }
  }

  /* ---------- Countdown ---------- */
  function initCountdown() {
    var box = document.getElementById('countdown');
    if (!box) return;
    var raw = box.getAttribute('data-event'); // formato YYYYMMDDTHHMMSS (horário de Brasília)
    var y = raw.slice(0, 4), mo = raw.slice(4, 6), d = raw.slice(6, 8);
    var h = raw.slice(9, 11), mi = raw.slice(11, 13), s = raw.slice(13, 15);
    var target = new Date(y + '-' + mo + '-' + d + 'T' + h + ':' + mi + ':' + s + '-03:00').getTime();

    var elDays = document.getElementById('cdDays');
    var elHours = document.getElementById('cdHours');
    var elMin = document.getElementById('cdMin');
    var elSec = document.getElementById('cdSec');

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
      var diff = target - Date.now();
      if (diff <= 0) {
        elDays.textContent = elHours.textContent = elMin.textContent = elSec.textContent = '00';
        clearInterval(timer);
        return;
      }
      var days = Math.floor(diff / 86400000);
      var hours = Math.floor((diff % 86400000) / 3600000);
      var mins = Math.floor((diff % 3600000) / 60000);
      var secs = Math.floor((diff % 60000) / 1000);

      elDays.textContent = pad(days);
      elHours.textContent = pad(hours);
      elMin.textContent = pad(mins);
      elSec.textContent = pad(secs);
    }

    tick();
    var timer = setInterval(tick, 1000);
  }

  /* ---------- Add to calendar (.ics) ---------- */
  function initAddToCalendar() {
    var btn = document.getElementById('addCalendarBtn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var ics = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Convite Aniversario Isa//PT-BR',
        'BEGIN:VEVENT',
        'UID:' + Date.now() + '@convite-isa',
        'DTSTAMP:' + toIcsUtc(new Date()),
        'DTSTART:' + toIcsUtc(new Date('2026-08-22T15:00:00-03:00')),
        'DTEND:' + toIcsUtc(new Date('2026-08-22T18:00:00-03:00')),
        'SUMMARY:Aniversário da Isa e do Sinval',
        'DESCRIPTION:Venha comemorar conosco!',
        'LOCATION:QRI 22 Casa 12 - Residencial Santos Dumont - Santa Maria - DF',
        'END:VEVENT',
        'END:VCALENDAR'
      ].join('\r\n');

      var blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url;
      a.download = 'aniversario-isa.ics';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      setTimeout(function () { URL.revokeObjectURL(url); }, 2000);
    });
  }

  function toIcsUtc(date) {
    return date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
  }

  /* ---------- RSVP form ---------- */
  function initRsvpForm() {
    var form = document.getElementById('rsvpForm');
    if (!form) return;
    var msg = document.getElementById('rsvpMsg');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = document.getElementById('rsvpName').value.trim();
      var attending = document.getElementById('rsvpAttending').value;
      var guests = parseInt(document.getElementById('rsvpGuests').value, 10) || 1;

      if (!name) {
        showMsg('Por favor, digite seu nome.', 'error');
        return;
      }

      var submitBtn = form.querySelector('.btn-submit');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Enviando...';

      fetch('api/rsvp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name, attending: attending, guests: guests })
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.error) {
            showMsg(data.error, 'error');
          } else {
            var text = attending === 'sim'
              ? 'Presença confirmada! Até lá 🎉'
              : 'Que pena! Sentiremos sua falta 💗';
            showMsg(text, 'success');
            form.reset();
            document.getElementById('rsvpGuests').value = 1;
            loadRsvpSummary();
          }
        })
        .catch(function () {
          showMsg('Não foi possível enviar agora. Tente novamente.', 'error');
        })
        .finally(function () {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Enviar confirmação';
        });
    });

    function showMsg(text, type) {
      msg.textContent = text;
      msg.className = 'rsvp-msg ' + type;
    }
  }

  function loadRsvpSummary() {
    var countEl = document.getElementById('rsvpCount');
    if (!countEl) return;
    fetch('api/rsvp.php')
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (typeof data.confirmedCount === 'number') {
          countEl.textContent = data.totalGuests > 0
            ? '🎉 ' + data.totalGuests + ' pessoa(s) confirmada(s) até agora'
            : '';
        }
      })
      .catch(function () { /* silencioso */ });
  }
})();
