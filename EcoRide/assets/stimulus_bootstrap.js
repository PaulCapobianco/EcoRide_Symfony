// assets/stimulus_bootstrap.js
import { Application } from '@hotwired/stimulus';
import SwapController from './controllers/swap_controller.js';
import ProfileController from './controllers/profile_controller.js';
import ConfirmController from './controllers/confirm_controller.js';
import AdminDashboardController from './controllers/admin_dashboard_controller.js';
import SelectScrollController from './controllers/select_scroll_controller.js';

const application = Application.start();

window.Stimulus = application;

application.register('swap', SwapController);
application.register('profile', ProfileController);
application.register('confirm', ConfirmController);
application.register('admin-dashboard', AdminDashboardController);
application.register('select-scroll', SelectScrollController);
