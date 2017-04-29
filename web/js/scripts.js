$(window).on("load resize ", function() {
    var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
    $('.tbl-header').css({'padding-right':scrollWidth});
}).resize();

$(document).ready(function() {
    $('#filter').on('change', function() {
        var $form = $(this).closest('form');
        $form.find('input[type=submit]').click();
    });
});

jQuery(document).ready(function() {
    $('.js-datepicker').datepicker();
});