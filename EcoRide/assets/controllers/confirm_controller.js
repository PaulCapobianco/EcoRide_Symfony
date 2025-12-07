import { Controller } from '@hotwired/stimulus';

// Ajoute une confirmation navigateur avant d’exécuter un formulaire
// (utilisé pour les actions créditées type participation ou annulation).
export default class extends Controller {
    static values = {
        message: String,
    };

    confirm(event) {
        const message =
            this.messageValue ||
            "Confirmer l'utilisation de vos crédits pour ce trajet ?";

        const ok = window.confirm(message);

        if (!ok) {
            event.preventDefault();
            event.stopPropagation();
        }
    }
}
