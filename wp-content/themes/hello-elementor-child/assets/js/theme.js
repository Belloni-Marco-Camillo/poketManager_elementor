(function () {
  // Fullscreen nav overlay
  var btn     = document.getElementById('pm-menu-toggle');
  var overlay = document.getElementById('pm-overlay');
  var bar1    = document.getElementById('pm-bar1');
  var bar2    = document.getElementById('pm-bar2');
  var bar3    = document.getElementById('pm-bar3');

  if (!btn || !overlay) return;

  function openMenu() {
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    btn.setAttribute('aria-expanded', 'true');
    btn.classList.add('is-open');
    bar1.style.transform = 'translateY(7px) rotate(45deg)';
    bar2.style.opacity   = '0';
    bar3.style.transform = 'translateY(-7px) rotate(-45deg)';
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    btn.setAttribute('aria-expanded', 'false');
    btn.classList.remove('is-open');
    bar1.style.transform = '';
    bar2.style.opacity   = '';
    bar3.style.transform = '';
    document.body.style.overflow = '';
  }

  btn.addEventListener('click', function () {
    overlay.classList.contains('is-open') ? closeMenu() : openMenu();
  });

  // Close on nav link click
  overlay.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', closeMenu);
  });

  // Close on ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeMenu();
  });

  // Parallax hero
  var hero = document.querySelector('.pm-hero');
  if (hero) {
    var bg   = hero.querySelector('.pm-hero-bg');
    var deco = hero.querySelector('.pm-hero-deco');
    function onScroll() {
      var y = window.scrollY;
      if (bg)   bg.style.transform = 'translateY(' + (y * 0.25) + 'px)';
      if (deco) deco.style.transform = 'translateY(' + (y * 0.12) + 'px)';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // Pillar cards stagger
  var pillarsRow = document.querySelector('.pm-pillars-row');
  if (pillarsRow) {
    var pillarsObs = new IntersectionObserver(function(entries) {
      if (entries[0].isIntersecting) {
        setTimeout(function() { pillarsRow.classList.add('is-visible'); }, 60);
        pillarsObs.disconnect();
      }
    }, { threshold: 0.15 });
    pillarsObs.observe(pillarsRow);
  }
})();
