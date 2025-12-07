// Permet de basculer entre les blocs "passager" et "conducteur" de la
// page “Comment ça marche ?” à l’aide des liens .how-switch.
export function initHowSwitch() {
  const sections = {
    passager: document.getElementById('passager'),
    conducteur: document.getElementById('conducteur'),
  };

  const switches = document.querySelectorAll('.how-switch');

  if (!sections.passager || !sections.conducteur || switches.length === 0) {
    return;
  }

  if (document.body.dataset.howInitialized === '1') {
    return;
  }
  document.body.dataset.howInitialized = '1';

  function activateSection(targetId) {
    Object.entries(sections).forEach(([id, section]) => {
      section.classList.toggle('is-active', id === targetId);
    });
  }

  activateSection('passager');

  switches.forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      const targetId = link.dataset.target;
      if (!targetId) {
        return;
      }
      activateSection(targetId);
    });
  });
}
