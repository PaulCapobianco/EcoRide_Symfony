import { Controller } from '@hotwired/stimulus';

// Ajuste temporairement l'attribut « size » d'un <select> pour limiter
// le nombre d'options visibles avant le scroll (utile pour les listes
// de marques très longues). Dès que l'utilisateur quitte le champ on
// remet le comportement normal.
export default class extends Controller {
  static values = {
    size: { type: Number, default: 6 },
  };

  connect() {
    this.defaultSize = this.element.hasAttribute('size')
      ? Number(this.element.getAttribute('size'))
      : null;

    this.expandHandler = () => this.expand();
    this.collapseHandler = () => this.collapse();

    this.element.addEventListener('focus', this.expandHandler);
    this.element.addEventListener('click', this.expandHandler);
    this.element.addEventListener('blur', this.collapseHandler);
    this.element.addEventListener('change', this.collapseHandler);
  }

  disconnect() {
    this.element.removeEventListener('focus', this.expandHandler);
    this.element.removeEventListener('click', this.expandHandler);
    this.element.removeEventListener('blur', this.collapseHandler);
    this.element.removeEventListener('change', this.collapseHandler);
  }

  expand() {
    this.element.setAttribute('size', this.sizeValue);
  }

  collapse() {
    if (this.defaultSize && this.defaultSize > 0) {
      this.element.setAttribute('size', this.defaultSize);
    } else {
      this.element.removeAttribute('size');
    }
  }
}
