<?php

namespace Akyos\Core\Helpers;

use Akyos\Core\Classes\Vite;
use Illuminate\Support\Facades\Blade;

function icon($icon): void
{
    $file = get_template_directory() . "/resources/assets/icons/{$icon}.svg";
    $file = str_replace("/", DIRECTORY_SEPARATOR, $file);
    if (!file_exists($file)) {
        echo (WP_ENV === "development") ? "<!-- Icon {$icon} does not exists -->" : "";
    }
    include $file;
}

function vite(): Vite
{
    return Vite::instance();
}

function checkReachability($url): bool
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_ENCODING => "",
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 1,
        CURLOPT_NOBODY => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_USERAGENT => "Mozilla/5.0 (compatible; StackOverflow/0.0.1; +https://codereview.stackexchange.com/)",
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code !== 0;
}

function concat_props($attrs, $var_string): string
{
    return array_reduce(array_keys($attrs), function ($carry, $key) use ($attrs, $var_string) {
        if (is_array($attrs[$key])) {
            $value = '$' . $var_string . '[\'' . $key . '\']';
            $key = ":$key";
        } else {
            $value = htmlspecialchars($attrs[$key]);
        }
        return $carry . ' ' . $key . '="' . $value . '"';
    }, "");
}

function print_component($component, $attrs, $var_string): string
{
    return htmlspecialchars_decode(Blade::render("<x-$component" . concat_props($attrs, $var_string) . "></x-$component>", [$var_string => $attrs]));
}

function style_vars($styleVars): string
{
    return array_reduce(array_keys($styleVars), fn($carry, $key) => $styleVars[$key] ? $carry . '--' . $key . ':' . $styleVars[$key] . ';' : $carry, "");
}

function options($option = null): bool|string|array
{
    // Get all theme options
    $options = get_fields('option');

    // If no option is specified, return all options
    if ($option == null) {
        return $options;
    }

    // If option is specified, return that option
    // Start by splitting the option into an array & count the total number of items
    $option = explode('.', $option);
    $optionCount = count($option);

    // Check if the option exists
    if (!isset($options[$option[0]])) {
        return "Key {$option[0]} not found.";
    }
    // Then, if the option is the only one, return it
    if ($optionCount == 1) {
        return $options[$option[0]];
    }

    // If the option is not the only one, loop through the array
    $optionValue = $options[$option[0]];
    for ($i = 1; $i < $optionCount; $i++) {
        if (!isset($optionValue[$option[$i]])) {
            return "Key {$option[$i]} not found.";
        }
        $optionValue = $optionValue[$option[$i]];
    }

    return $optionValue;
}

function breadcrumb(): array
{
    $pages = [0 => ["title" => "Accueil", "url" => "/"]];

    if (is_archive()) {
        $post_type = get_post_type();
        $map = [
            "post" => ["title" => "Blog", "url" => "/blog"],
            "property" => ["title" => "Nos biens", "url" => "/biens"],
        ];
        if (isset($map[$post_type])) {
            $pages[] = $map[$post_type];
        }
    } elseif (is_single()) {
        $post_type = get_post_type();
        $map = [
            "post" => ["title" => "Blog", "url" => "/blog"],
            "property" => ["title" => "Nos biens", "url" => "/biens"],
        ];
        if (isset($map[$post_type])) {
            $pages[] = $map[$post_type];
        }
        $pages[] = ["title" => get_the_title(), "url" => get_the_permalink()];
        collect(get_post_ancestors(get_the_ID()))->each(function ($item) use (&$pages) {
            $pages[] = ["title" => get_the_title($item), "url" => get_the_permalink($item)];
        });
    } else {
        $pages[] = ["title" => get_the_title(), "url" => get_the_permalink()];
        collect(get_post_ancestors(get_the_ID()))->each(function ($item) use (&$pages) {
            $pages[] = ["title" => get_the_title($item), "url" => get_the_permalink($item)];
        });
    }
    return $pages;
}
