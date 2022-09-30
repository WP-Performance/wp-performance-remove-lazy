<?php

namespace WPPerfomance\inc\parser;

/** determine if string is json */
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}

/** find image with classes nolazy for replace lazy to eager */
function eagerImage($string)
{
    // return all request json or empty
    if (isJSON($string) || !$string) {
        return $string;
    }
    $document = new \DOMDocument();
    // hide error syntax warning
    libxml_use_internal_errors(true);

    $document->loadHTML($string);
    $xpath = new \DOMXpath($document);

    $lazyCover = $xpath->query("//img[contains(@class,'wp-block-cover__image-background')]");

    $lazyImgs = $xpath->query("//*[contains(@class,'nolazy')]/img");

    foreach ($lazyCover as $key => $value) {
        $value->setAttribute('loading', 'eager');
    }

    foreach ($lazyImgs as $key => $value) {
        $value->setAttribute('loading', 'eager');
    }


    return $document->saveHTML();
}


function parsing_end(string $string): string
{
    return eagerImage($string);
}


function parsing_start(): void
{
    ob_start(__NAMESPACE__ . '\parsing_end');
}
