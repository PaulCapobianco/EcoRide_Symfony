import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

// Affiche les deux graphiques statistiques du tableau de bord admin
// (trajets par jour et crédits générés). On mémorise un flag sur
// l’élément racine pour éviter de recréer les graphiques à chaque
// visite Turbo.

export default class extends Controller {
  static values = {
    rides: Array,
    gains: Array,
  };

  connect() {
    if (this.element.dataset.chartsRendered === '1') {
      return;
    }

    this.renderCharts();
  }

  renderCharts() {
    if (this.element.dataset.chartsRendered === '1') {
      return;
    }

    const ridesCanvas = document.getElementById('chartRides');
    const gainsCanvas = document.getElementById('chartGains');
    if (!ridesCanvas || !gainsCanvas) {
      return;
    }

    const ridesLabels = (this.ridesValue || []).map((item) => item.jour).reverse();
    const ridesValues = (this.ridesValue || []).map((item) => item.total).reverse();
    const gainsLabels = (this.gainsValue || []).map((item) => item.jour).reverse();
    const gainsValues = (this.gainsValue || []).map((item) => item.credits ?? 0).reverse();

    new Chart(ridesCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: ridesLabels,
        datasets: [{
          label: 'Covoiturages / jour',
          data: ridesValues,
          borderColor: '#1db954',
          backgroundColor: 'rgba(29,185,84,0.15)',
          tension: 0.2,
          fill: true,
        }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });

    new Chart(gainsCanvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: gainsLabels,
        datasets: [{
          label: 'Crédits / jour',
          data: gainsValues,
          backgroundColor: 'rgba(159,193,49,0.6)',
          borderColor: '#9FC131',
          borderWidth: 1,
        }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });

    this.element.dataset.chartsRendered = '1';
  }
}
