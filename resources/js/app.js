import './bootstrap';
import './custom';

import $ from 'jquery';
import flatpickr from 'flatpickr';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2/dist/sweetalert2.js'

window.$ = window.jQuery = $;
window.Alpine = Alpine;
window.Swal = Swal;

console.log('jQuery:', window.$);
console.log('flatpickr:', flatpickr);
console.log('Swal:', Swal);

Alpine.start();
