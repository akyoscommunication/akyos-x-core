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
