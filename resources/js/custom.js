import jQuery from 'jquery';
import 'jquery-ui/ui/widgets/datepicker';


window.$ = window.jQuery = jQuery;

$(document).ready(function() {

    $('#coaTable th').click(function() {
        var table = $(this).parents('table').eq(0);
        var rows = table.find('tr:gt(0)').toArray().sort((a, b) => {
            var idx = $(this).index();
            var valA = $(a).children('td').eq(idx).text().toUpperCase();
            var valB = $(b).children('td').eq(idx).text().toUpperCase();
            if ($.isNumeric(valA) && $.isNumeric(valB)) {
                return valA - valB;
            } else {
                return valA.localeCompare(valB);
            }
        });

        var sortIcon = $(this).find('.sort-icon');
        var currentSort = sortIcon.data('sort');

        if (currentSort === 'asc') {
            rows = rows.reverse();
            sortIcon.attr('src', '/images/icons/sort-desc.svg');
            sortIcon.data('sort', 'desc');
        } else {
            sortIcon.attr('src', '/images/icons/sort-asc.svg');
            sortIcon.data('sort', 'asc');
        }

        $.each(rows, function(index, row) {
            table.children('tbody').append(row);
        });

        $('#coaTable th').not(this).find('.sort-icon').attr('src', '/images/icons/ic-sort.svg').data('sort', 'none');
    });

    $('#jurnalTable th').click(function() {
        var table = $(this).parents('table').eq(0);
        var rows = table.find('tr:gt(0)').toArray().sort((a, b) => {
            var idx = $(this).index();
            var valA = $(a).children('td').eq(idx).text().toUpperCase();
            var valB = $(b).children('td').eq(idx).text().toUpperCase();
            if ($.isNumeric(valA) && $.isNumeric(valB)) {
                return valA - valB;
            } else {
                return valA.localeCompare(valB);
            }
        });

        var sortIcon = $(this).find('.sort-icon');
        var currentSort = sortIcon.data('sort');

        if (currentSort === 'asc') {
            rows = rows.reverse();
            sortIcon.attr('src', '/images/icons/sort-desc.svg');
            sortIcon.data('sort', 'desc');
        } else {
            sortIcon.attr('src', '/images/icons/sort-asc.svg');
            sortIcon.data('sort', 'asc');
        }

        $.each(rows, function(index, row) {
            table.children('tbody').append(row);
        });

        $('#jurnalTable th').not(this).find('.sort-icon').attr('src', '/images/icons/ic-sort.svg').data('sort', 'none');
    });

    function importExcel(){
        console.log('import')
    }
});
