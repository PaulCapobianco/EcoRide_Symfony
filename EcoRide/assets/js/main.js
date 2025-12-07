// Point d’entrée JS côté client : on initialise la classe `js` pour
// différencier les styles CSS, puis on active nos modules maison.
document.documentElement.classList.add('js');

import { initHowSwitch } from './modules/how_it_works.js';
import { initCovoiturageFilters } from './modules/covoiturageFilters.js';

function initCookieModal() {
  const modal = document.querySelector('[data-cookie-modal]');
  if (!modal) return;

  const acceptBtn = modal.querySelector('[data-cookie-modal-accept]');
  const closeBtn = modal.querySelector('[data-cookie-modal-close]');
  const openBtns = document.querySelectorAll('[data-cookie-modal-open]');

  const hide = () => modal.classList.remove('is-open');
  const show = () => modal.classList.add('is-open');

  const hasConsent = document.cookie.split('; ').some(c => c.startsWith('EcoRide='));
  if (!hasConsent) {
    show();
  }

  acceptBtn?.addEventListener('click', () => {
    const maxAge = 180 * 24 * 60 * 60;
    document.cookie = 'EcoRide=1; path=/; max-age=' + maxAge;
    hide();
  });

  closeBtn?.addEventListener('click', () => {
    const maxAge = 180 * 24 * 60 * 60;
    document.cookie = 'EcoRide=0; path=/; max-age=' + maxAge;
    hide();
  });

  openBtns.forEach(btn => btn.addEventListener('click', (e) => {
    e.preventDefault();
    show();
  }));
}

function boot() {
  try {
    // Filtres de la page Covoiturages
    if (typeof initCovoiturageFilters === 'function') {
      initCovoiturageFilters();
    }

    // Toggle passager / conducteur dans “Comment ça marche ?”
    if (typeof initHowSwitch === 'function') {
      initHowSwitch();
    }

    // Consentement cookies + gestion du lien “gérer mes cookies”
    initCookieModal();
  } catch (error) {
    console.error('[EcoRide] Erreur dans boot()', error);
  }
}

// Avec Turbo, déclenché à chaque navigation
document.addEventListener('turbo:load', boot);
