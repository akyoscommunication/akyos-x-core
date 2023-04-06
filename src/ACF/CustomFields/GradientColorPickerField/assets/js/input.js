// import XNColorPicker from '../libs/xncolorpicker/src/xncolorpicker.js';
import '../libs/gpickr-master/dist/gpickr.min.js';

const FIELD_NAME = 'gradient_color_picker';
const FIELD_SLUG = 'gradient-color-picker ';

class Field {

    constructor($field)
    {
        this.fieldElement = $field.get()[0];
        this.field = this.fieldElement.querySelector('.aky_acf_'+FIELD_SLUG);
        let field_ID = this.fieldElement.getAttribute('data-key')
        this.ACF_Field = acf.getField(field_ID);
        this.preview = this.field.querySelector('.show-color')
        this.inputItem = this.fieldElement.querySelector('#field-value');

        let gradients = this.field.querySelector('.picker-btn').getAttribute('gradient').split('_')

        this.gpickr = new GPickr({
            el: '.aky_acf_'+ FIELD_SLUG + '> #colorpicker',
            stops: [
              [gradients[0], 0],
              [gradients[1], 1],
            ],
        })

        this.gpickr.on('init', (instance, field) => {
            this.toggleColorPicker(field)
        }).on('change', instance => {
            let gradient = instance.getGradient()
            this.updateColorPreview(gradient)
            this.updateAcfValue(gradient)
        });

        let button = this.field.querySelector('.picker-btn')
        button.addEventListener('click', () => {
            this.toggleColorPicker()
        })

    }

    toggleColorPicker()
    {
        let gradientPicker = this.field.querySelector('.gpickr')
        gradientPicker.classList.toggle('hidden')
    }

    updateColorPreview(gradient)
    {
        this.preview.style.background = gradient;
    }

    updateAcfValue(value)
    {
        this.ACF_Field.val(value)
        this.inputItem.setAttribute('value', value)
    }

}

function execute($field)
{

    // let gradientPicker = new XNColorPicker({
    //     selector: '.aky_acf_'+ FIELD_SLUG + '> #colorpicker',
    //     format: 'hex',
    //     canMove: false,
    //     showPalette: true,
    //     lang: 'en',
    //     colorTypeOption: 'linear-gradient,radial-gradient',
    //     autoConfirm: true,
    //     showprecolor: false,
    //     show: true,
    //     alwaysShow: true,
    //     onError: function (e) {
    //         console.log('error', e)
    //     },
    //     onCancel: function (color) {
    //         console.log('cancel', color)
    //     },
    //     onChange: function (color) {
    //         console.log('change', color)
    //     },
    //     onConfirm: function (color) {
    //         console.log('confirm', color)
    //     },
    // })
    // toggleColorPicker(true)
    //
    // let button = field.querySelector('.picker-btn')
    // button.addEventListener('click', () => {
    //     toggleColorPicker()
    // })
    //
    // let closeButton = document.querySelector('.fcolorpicker .cancel-color')
    // closeButton.addEventListener('click', () => {
    //     toggleColorPicker()
    // })
    //
    // let acceptButton = document.querySelector('.fcolorpicker .confirm-color')
    // acceptButton.addEventListener('click', () => {
    //     toggleColorPicker()
    // })
    //
    // let singleColorPickers = document.querySelectorAll('.fcolorpicker .gradient-item')
    // singleColorPickers.forEach((colorPickerItem) => {
    //     console.log(colorPickerItem)
    //     colorPickerItem.addEventListener('click', () => {
    //         console.log('clicked')
    //         gradientPicker.updateAngleBar()
    //         gradientPicker.updateGradientBar()
    //         gradientPicker.updateGradientColorItem()
    //         gradientPicker.updateGradientColors()
    //         gradientPicker.updatelightbar()
    //     })
    // })

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
        new Field($field)
    }

    // console.log('.aky_acf_'+ FIELD_SLUG + '> #colorpicker')
    // console.log(typeof acf.add_action)

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
