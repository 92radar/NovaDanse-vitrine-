

// animation des onglet du carrousel
const tabs = document.querySelectorAll(".carrousel li");
const selector = document.createElement("div");
selector.classList.add("selector");
document.querySelector(".carrousel ul").appendChild(selector);

tabs.forEach((tab, i) => {
  tab.addEventListener("click", () => {
    document.querySelector(".carrousel li.active")?.classList.remove("active");
    tab.classList.add("active");
    selector.style.left = `${i * 25}%`;
  });
});


//activation des onglets 
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.carrousel ul li');
  const courses = document.querySelectorAll('.courses');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // --- Met à jour l'état actif du carrousel
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      // --- Affiche le cours correspondant
      courses.forEach(course => course.classList.remove('active'));
      const targetClass = tab.dataset.tab;
      const targetCourse = document.querySelector(`.courses.${targetClass}`);
      if (targetCourse) targetCourse.classList.add('active');
    });
  });

  // --- Initial : afficher le premier cours
  courses.forEach(course => course.classList.remove('active'));
  if (courses[0]) courses[0].classList.add('active');
});

//swipe mobile pour les courses
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.carrousel ul li');
  const coursesWrapper = document.querySelector('.courses-wrapper');
  const courses = document.querySelectorAll('.courses');
  const selector = document.querySelector('.carrousel .selector');

  let currentIndex = 0;

  function updateActive(index) {
    // Met à jour le sélecteur
    if (selector) {
      const tabWidth = tabs[0].offsetWidth;
      selector.style.left = `${tabWidth * index}px`;
    }

    // Met à jour les onglets
    tabs.forEach((t, i) => t.classList.toggle('active', i === index));

    // Met à jour les cours
    courses.forEach((c, i) => c.classList.toggle('active', i === index));

    currentIndex = index;
  }

  // Clic sur onglets
  tabs.forEach((tab, i) => {
    tab.addEventListener('click', () => {
      scrollToCourse(i);
    });
  });

  // Fonction pour scroller vers un cours
  function scrollToCourse(index) {
    const courseWidth = courses[0].offsetWidth;
    coursesWrapper.scrollTo({
      left: courseWidth * index,
      behavior: 'smooth'
    });
    updateActive(index);
  }

  // Initial
  scrollToCourse(0);

  // Swipe / scroll horizontal
  let isTouching = false;
  let startX = 0;

  coursesWrapper.addEventListener('touchstart', e => {
    isTouching = true;
    startX = e.touches[0].clientX;
  });

  coursesWrapper.addEventListener('touchmove', e => {
    if (!isTouching) return;
    const moveX = e.touches[0].clientX;
    const diff = startX - moveX;

    // On swipe terminé, on change l'index
    if (Math.abs(diff) > 50) {
      if (diff > 0 && currentIndex < courses.length - 1) {
        scrollToCourse(currentIndex + 1);
      } else if (diff < 0 && currentIndex > 0) {
        scrollToCourse(currentIndex - 1);
      }
      isTouching = false; // reset
    }
  });

  coursesWrapper.addEventListener('touchend', () => {
    isTouching = false;
  });

  // Sync sur resize
  window.addEventListener('resize', () => scrollToCourse(currentIndex));
});
// selecteur calcul largeur dynamique
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.carrousel ul li');
  const selector = document.querySelector('.carrousel .selector');

  function moveSelectorTo(index) {
    const tab = tabs[index];
    const { offsetLeft, offsetWidth } = tab;

    selector.style.left = `${offsetLeft}px`;
    selector.style.width = `${offsetWidth}px`;
  }

  // 👉 Clic sur les onglets
  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      moveSelectorTo(index);
    });
  });

  // 👉 Initialisation au premier onglet
  if (tabs[0]) {
    tabs[0].classList.add('active');
    moveSelectorTo(0);
  }

  // 👉 Recalcule si la fenêtre change de taille (important pour responsive)
  window.addEventListener('resize', () => {
    const activeIndex = [...tabs].findIndex(t => t.classList.contains('active'));
    if (activeIndex >= 0) moveSelectorTo(activeIndex);
  });
});
