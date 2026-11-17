import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import fileCollector from './alpine/fileCollector';
import ticketFilter from './alpine/ticketFilter';
import attentionTicket from './alpine/attentionTicket';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('fileCollector', fileCollector);
Alpine.data('ticketFilter', ticketFilter);
Alpine.data('attentionTicket', attentionTicket);

Alpine.start();
