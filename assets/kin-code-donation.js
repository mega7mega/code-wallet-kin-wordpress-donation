(function($) {
    $(document).ready(function() {
        var buttonContainer = $('<div>', {
            'id': 'button-container'
        });

        $('body').append(buttonContainer);
        $('#button-container').hide();

        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#button-container').addClass('show');
            } else {
                $('#button-container').removeClass('show');
            }
        });

        var amount = kin_code_donation_params.amount;
        var destination = kin_code_donation_params.destination;

        import('https://js.getcode.com/v1')
            .then(code => {
                const { button } = code.elements.create('button', {
                    currency: 'usd',
                    amount: amount,
                    destination: destination,
                });

                button.on('success', () => {
                    alert('Thank you!');
                });

                button.mount('#button-container');
            })
            .catch(error => {
                console.error('Failed to load Code.com:', error);
            });
    });
})(jQuery);