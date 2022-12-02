class Location {
    static getStates($element, countryId) {
        $.ajax({
            url: $element.data('url') + '?country_id=' + countryId,
            type: 'GET',
            beforeSend: () => {
                $element.closest('form').find('button[type=submit], input[type=submit]').prop('disabled', true);
            },
            success: data => {
                let option = '';
                $.each(data.data, (index, item) => {
                    option += '<option value="' + item.id + '">' + item.name + '</option>';
                });

                $element.html(option);
                $element.closest('form').find('button[type=submit], input[type=submit]').prop('disabled', false);
            }
        });
    }

    static getCities($element, stateId) {
        $.ajax({
            url: $element.data('url') + '?state_id=' + stateId,
            type: 'GET',
            beforeSend: () => {
                $element.closest('form').find('button[type=submit], input[type=submit]').prop('disabled', true);
            },
            success: data => {
                let option = '';
                $.each(data.data, (index, item) => {
                    option += '<option value="' + item.id + '">' + item.name + '</option>';
                });

                $element.html(option);
                $element.trigger('change');
                $element.closest('form').find('button[type=submit], input[type=submit]').prop('disabled', false);
            }
        });
    }

    init() {
        const country = 'select[data-type="country"]';
        const state = 'select[data-type="state"]';
        const city = 'select[data-type="city"]';

        $(document).on('change', country, function (event) {
            event.preventDefault();

            const $element = $(document).find(state);
            if ($element.length && $(this).val()) {
                Location.getStates($element, $(event.currentTarget).val());
            }
        });

        $(document).on('change', state, function (event) {
            event.preventDefault();

            const $element = $(document).find(city);
            if ($element.length) {
                Location.getCities($element, $(event.currentTarget).val());
            }
        });
    }
}

$(document).ready(() => {
    (new Location()).init();
});
