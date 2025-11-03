import './bootstrap';
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import Alpine from 'alpinejs'
import focus from '@alpinejs/focus'
import './academicController';
import './attendanceStats';
import Chart from 'chart.js/auto';


window.Chart=Chart;
Alpine.plugin(focus)

window.Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: false,
});
import { multiSelect } from './components/form/multi-select';
import { autocompleteField } from './components/form/autocomplete';

window.multiSelect = multiSelect;
window.autocompleteField = autocompleteField;



