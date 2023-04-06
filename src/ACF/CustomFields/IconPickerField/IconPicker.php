<?php

namespace Akyos\Core\ACF\CustomFields\IconPickerField;

/**
 * Exit if accessed directly
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register field name
 */
const FIELD_NAME = 'IconPicker';
const FIELD_TYPE = 'icon_picker';
const FORMATTED_FIELD_NAME = FIELD_NAME.'Field';
const VERSION = '1.0.0';

/**
 * Exit if class already exist
 */
if (!class_exists('App\Acf\CustomFields\IconPicker\RegisterCustomField')) {
    return;
}

/**
 * Register Field Class
 */
class RegisterCustomField
{

    // vars
    var $settings;

    /*
    *  __construct
    *
    *  This function will setup the class functionality
    *
    *  @type    function
    *  @date    17/02/2016
    *  @since   1.0.0
    *
    *  @param   void
    *  @return  void
    */

    public function __construct()
    {

        $this->settings = array(
            'name'        => FORMATTED_FIELD_NAME,
            'slug'        => FIELD_TYPE,
            'version'     => VERSION,
            'url'         => get_home_url() . '/app/themes/akyos-sage/app/Acf/CustomFields/' . FORMATTED_FIELD_NAME,
            'path'        => __DIR__
        );

        add_action('acf/include_field_types', array($this, 'includeField')); // v5
    }


    /*
    *  include_field
    *
    *  This function will include the field type class
    *
    *  @type    function
    *  @date    17/02/2016
    *  @since   1.0.0
    *
    *  @param   $version (int) major ACF version. Defaults to false
    *  @return  void
    */

    public function includeField($version = 5)
    {
        /**
         * .../app/Acf/CustomFields/{*fieldName*}Field/fields/acf_field_{*fieldName*}.php
         */
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fields' . DIRECTORY_SEPARATOR . 'acf_field_' . FIELD_NAME . '.php';
        if (is_file($path)) {
            include_once($path);
        }
    }
}

/**
 * Initialize
 */

//$name = 'App\Acf\CustomFields\\'.FIELD_NAME.'\RegisterCustomField';
//new $name();
