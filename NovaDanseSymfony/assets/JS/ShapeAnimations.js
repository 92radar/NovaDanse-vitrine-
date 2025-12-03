// animation des shapes

document.addEventListener('DOMContentLoaded', () => {
  const shapes = document.querySelectorAll('.shape');
  const geometry = document.querySelector('.geometry');

  // 👇 Tableau de direction (X, Y) pour chaque shape
  // index 0 = shape 1, index 1 = shape 2, etc.
  const shapeDirections = [
    { x: -100, y: -100 }, // shape 1 : diagonale haut gauche
    { x:  100, y:   0 },  // shape 2 : horizontale droite
    { x: -100, y:   0 },  // shape 3 : horizontale gauche
    { x:  100, y:  100 }  // shape 4 : diagonale bas droite
  ];

  // 📍 Position initiale hors écran
  shapes.forEach((shape, i) => {
    const dir = shapeDirections[i] || { x: 0, y: 0 };
    shape.style.transform = `translate(${dir.x}px, ${dir.y}px)`;
    shape.style.opacity = 0;
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {

        function updateShapes() {
          const rect = geometry.getBoundingClientRect();
          const offsetY = Math.min(Math.max(window.innerHeight - rect.top, 0), 600);
          const progress = offsetY / 600;

          shapes.forEach((shape, i) => {
            const dir = shapeDirections[i] || { x: 0, y: 0 };
            const moveX = dir.x * (1 - progress) * 0.5;
            const moveY = dir.y * (1 - progress) * 0.5;
            shape.style.transform = `translate(${moveX}px, ${moveY}px)`;
            shape.style.opacity = progress;
          });
        }

        updateShapes();
        window.addEventListener('scroll', updateShapes);
      }
    });
  }, { threshold: 0.1 });

  observer.observe(geometry);
});