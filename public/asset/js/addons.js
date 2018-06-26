jQuery(document).ready(function ($) {

    $('.contract-form input, .contract-form textarea').each(function () {
        var $this = $(this);

        $this.data('placeholder', $this.attr('placeholder'))
            .focus(function () {
                $this.removeAttr('placeholder');
            })
            .blur(function () {
                $this.attr('placeholder', $this.data('placeholder'));
            });
    });

    $('.contract-form .header-form .checkboxes .radio.radio-inline:first-child').on('click', function () {
        $('.contract-form').addClass('private');
    });
    $('.contract-form .header-form .checkboxes .radio.radio-inline:last-child').on('click', function () {
        $('.contract-form').removeClass('private');
    });


});




