//burger menu open
const menuIcon = document.getElementById('menu-icon');
const fullscreenMenu = document.getElementById('fullscreen-menu');

// Fonction pour ouvrir/fermer le menu
function toggleMenu() {
  const isActive = fullscreenMenu.classList.toggle('active');
  menuIcon.classList.toggle('active', isActive);
  document.body.style.overflow = isActive ? 'hidden' : ''; // bloque/débloque le scroll
}

// Clique sur l’icône → ouvre/ferme
menuIcon.addEventListener('click', toggleMenu);

// Clique sur le menu → ferme
fullscreenMenu.addEventListener('click', (e) => {
  if (e.target.tagName === 'A' || e.target === fullscreenMenu) {
    toggleMenu();
  }
});