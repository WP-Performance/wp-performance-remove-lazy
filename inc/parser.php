<?php

namespace WPPerformance\RemoveLazy\inc\parser;

/** determine if string is json */
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}


/** find image with classes nolazy for replace lazy to eager */
function parse($string)
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

    parseQueryCover($xpath);
    parseQueryImage($xpath);

    return $document->saveHTML();
}

/**
 * parse the cover block
 */
function parseQueryCover(\DOMXpath $xpath): void
{
    $lazyCover = $xpath->query("//img[contains(@class,'wp-block-cover__image-background')]");
    foreach ($lazyCover as $key => $value) {
        $value->setAttribute('loading', 'eager');
    }
}

/**
 * parse image with no lazy class
 */
function parseQueryImage(\DOMXpath $xpath): void
{
    $lazyImgs = $xpath->query("//*[contains(@class,'nolazy')]/img");

    foreach ($lazyImgs as $key => $value) {
        $value->setAttribute('loading', 'eager');
    }
}


function parsing_end(string $string): string
{
    return parse($string);
}


function parsing_start(): void
{
    ob_start(__NAMESPACE__ . '\parsing_end');
}
