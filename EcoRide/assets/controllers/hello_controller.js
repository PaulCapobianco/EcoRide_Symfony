import { Controller } from '@hotwired/stimulus';

// Contrôleur de démonstration (utilisé sur la page d’accueil build pour tester Stimulus)
export default class extends Controller {
    connect() {
        this.element.textContent = 'Stimulus est prêt.';
    }
}
