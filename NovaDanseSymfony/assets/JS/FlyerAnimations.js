
//flyer animation
document.addEventListener('DOMContentLoaded', () => {
    const flyerImg = document.querySelector('.anim-img');
    const flyerTops = document.querySelectorAll('.anim-top');
    const flyerLeft = document.querySelector('.anim-left');
    const flyerGeometry = document.querySelector('.flyer-geometry');
  
    // Position initiale hors écran
    flyerImg.style.transform = 'translateX(100px)';
    flyerImg.style.opacity = 0;
  
    flyerTops.forEach(el => {
      el.style.transform = 'translateY(-100px)';
      el.style.opacity = 0;
    });
  
    flyerLeft.style.transform = 'translateX(-100px)';
    flyerLeft.style.opacity = 0;
  
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
  
          function updateFlyer() {
            const rect = flyerGeometry.getBoundingClientRect();
            const offsetY = Math.min(Math.max(window.innerHeight - rect.top, 0), 600);
            const progress = offsetY / 600;
  
            // IMAGE
            const moveImg = 200 * (1 - progress) * 0.6;
            flyerImg.style.transform = `translateX(${moveImg}px)`;
            flyerImg.style.opacity = progress;
  
            // TITRES
            flyerTops.forEach(el => {
              const moveTop = -100 * (1 - progress) * 0.5;
              el.style.transform = `translateY(${moveTop}px)`;
              el.style.opacity = progress;
            });
  
            // TEXTE GAUCHE
            const moveLeft = -100 * (1 - progress) * 0.5;
            flyerLeft.style.transform = `translateX(${moveLeft}px) rotate(-90deg)`;
            flyerLeft.style.opacity = progress;
          }
  
          updateFlyer();
          window.addEventListener('scroll', updateFlyer);
        }
      });
    }, { threshold: 0.5 });
  
    observer.observe(flyerGeometry);
  });