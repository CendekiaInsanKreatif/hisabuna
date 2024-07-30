import './bootstrap';
import './custom';

import $ from 'jquery';
import flatpickr from 'flatpickr';
import Alpine from 'alpinejs';

window.$ = window.jQuery = $;
window.Alpine = Alpine;

console.log('jQuery:', window.$);
console.log('flatpickr:', flatpickr);

Alpine.start();
