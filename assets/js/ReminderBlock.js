export function ReminderBlock(context) {
    $('.js-remind', context).on('change', function () {
        if ($(this).is(':checked')) {
            $('.js-wrap-remind', context).show();
        } else {
            $('.js-wrap-remind', context).hide();
        }
    }).trigger('change');
}