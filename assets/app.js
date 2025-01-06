import './bootstrap.js';
import 'datatables.net-dt';
import 'jquery';
const $ = require('jquery');
Window.prototype.$ = $;

import 'bootstrap';

import './js/color-modes';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/global.scss';

import zoomPlugin from 'chartjs-plugin-zoom';
document.addEventListener('chartjs:init', function (event) {
    const Chart = event.detail.Chart;
    Chart.register(zoomPlugin);
});

