(function () {
  // Mobile nav toggle
  var btn  = document.getElementById('pm-menu-toggle');
  var menu = document.getElementById('pm-mobile-menu');
  var bar1 = document.getElementById('pm-bar1');
  var bar2 = document.getElementById('pm-bar2');
  var bar3 = document.getElementById('pm-bar3');
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var hidden = menu.classList.toggle('pm-hidden');
      btn.setAttribute('aria-expanded', String(!hidden));
      if (!hidden) {
        bar1.style.transform = 'translateY(8px) rotate(45deg)';
        bar2.style.opacity   = '0';
        bar3.style.transform = 'translateY(-8px) rotate(-45deg)';
      } else {
        bar1.style.transform = '';
        bar2.style.opacity   = '';
        bar3.style.transform = '';
      }
    });
  }

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
})();
