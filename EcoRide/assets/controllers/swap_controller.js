import { Controller } from '@hotwired/stimulus';

// Inverse rapidement les valeurs « départ » / « arrivée » sur les formulaires de recherche.
export default class extends Controller {
  static targets = ['from', 'to'];

  invert(event) {
    event.preventDefault();

    const fromInput = this.fromTarget;
    const toInput   = this.toTarget;

    [fromInput.value, toInput.value] = [toInput.value, fromInput.value];

    fromInput.focus();
  }
}
