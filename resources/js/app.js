import './bootstrap';
import './custom';

import jQuery from 'jquery';
import Alpine from 'alpinejs';
import 'jquery-ui/ui/widgets/datepicker';

window.$ = window.jQuery = jQuery;
window.Alpine = Alpine;

Alpine.start();