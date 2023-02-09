const FIELD_NAME = 'style_selector';
const FIELD_SLUG = 'style-selector';

function execute($field)
{

    let fieldElement = $field.get()[0];
    let field_ID = fieldElement.getAttribute('data-key')
    let ACF_Field = acf.getField(field_ID);

    let field = fieldElement.querySelector('.aky_acf_'+FIELD_SLUG);
    let items = field.querySelectorAll('.item-wrapper');
    let inputItem = fieldElement.querySelector('#field-value');

    items.forEach((item) => {
        item.addEventListener('click', () => {
            resetField(items)
            item.classList.add('selected')
            ACF_Field.val(item.dataset.value)
        })

        initDefaultField(item, inputItem)

    })

}

function resetField(items)
{
    items.forEach((item) => {
        item.classList.remove('selected')
    })
}

function initDefaultField(item, inputItem)
{
    let itemValue = item.dataset.value;
    let fieldValue = inputItem.value;

    console.log(itemValue, fieldValue)

    if (itemValue === fieldValue) {
        item.classList.add('selected')
    }
}

(function ($, field_name=FIELD_NAME) {


    /**
    *  initialize_field
    *
    *  This function will initialize the $field.
    *
    *  @date    30/11/17
    *  @since   5.6.5
    *
    *  @param   n/a
    *  @return  n/a
    */

    function initialize_field( $field )
    {
        execute($field);
    }


    if ( typeof acf.add_action !== 'undefined' ) {
        /*
        *  ready & append (ACF5)
        *
        *  These two events are called when a field element is ready for initizliation.
        *  - ready: on page load similar to $(document).ready()
        *  - append: on new DOM elements appended via repeater field or other AJAX calls
        *
        *  @param   n/a
        *  @return  n/a
        */

        acf.add_action('ready_field/type='+field_name, initialize_field);
        acf.add_action('append_field/type='+field_name, initialize_field);
    } else {
        /*
        *  acf/setup_fields (ACF4)
        *
        *  These single event is called when a field element is ready for initizliation.
        *
        *  @param   event       an event object. This can be ignored
        *  @param   element     An element which contains the new HTML
        *  @return  n/a
        */

        $(document).on('acf/setup_fields', function (e, postbox) {

            // find all relevant fields
            $(postbox).find('.field[data-field_type="'+field_name+'"]').each(function () {

                // initialize
                initialize_field($(this));

            });

        });
    }

})(jQuery);
