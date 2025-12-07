import { Controller } from '@hotwired/stimulus';

/**
 * Gère l’affichage conditionnel des sections profil (mode conducteur)
 * et l’état actif de la navigation latérale.
 */
export default class extends Controller {
  connect() {
    // Sections masquées tant que l'utilisateur n'a pas activé le mode conducteur
    this.driverElements = this.element.querySelectorAll('[data-requires="driver"]');

    // Gestion du surlignage dans la navigation profil
    this.navLinks = this.element.querySelectorAll('.settings-nav .settings-link');
    this.navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        this.navLinks.forEach((l) => l.classList.remove('is-active'));
        link.classList.add('is-active');
      });
    });

    const checked = this.element.querySelector('input[name="profil_type"]:checked');
    if (checked) {
      this.applyRole(checked.value);
    }
  }

  // Appelé par data-action="change->profile#onRoleChange"
  onRoleChange(event) {
    const role = event.target.value;
    this.applyRole(role);
  }

  applyRole(role) {
    const isDriverMode = role === 'driver' || role === 'both';

    this.driverElements.forEach((el) => {
      if (isDriverMode) {
        el.removeAttribute('hidden');
      } else {
        el.setAttribute('hidden', '');
      }
    });
  }
}
