// Initialisation des curseurs de filtrage (prix max + durée max)
// sur la page de recherche de covoiturages.
export function initCovoiturageFilters() {
  initSliders();
}

function initSliders() {
  const priceInput    = document.getElementById('filterPrice');
  const priceSpan     = document.getElementById('priceValue');
  const durationInput = document.getElementById('filterDuration');
  const durationSpan  = document.getElementById('durationValue');

  // Slider de prix max (en crédits)
  if (priceInput && priceSpan) {
    const updatePrice = () => {
      priceSpan.textContent = priceInput.value + ' crédit(s)';
    };

    // valeur au chargement
    updatePrice();

    priceInput.addEventListener('input', updatePrice);
  }

  // Slider de durée max (en heures)
  if (durationInput && durationSpan) {
    const updateDuration = () => {
      durationSpan.textContent = durationInput.value + ' h';
    };

    // valeur au chargement
    updateDuration();

    durationInput.addEventListener('input', updateDuration);
  }
}
