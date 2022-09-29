<?php

namespace WPPerfomance\inc\parser;



/** find image with classes nolazy for replace lazy to eager */
function eagerImage($html)
{
    $content = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
    $document = new \DOMDocument();
    libxml_use_internal_errors(true);
    if (!$content) {
        return $content;
    }
    $document->loadHTML(utf8_decode($content));
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


function parsing_end(string $html): string
{
    return eagerImage($html);
}


function parsing_start(): void
{
    ob_start(__NAMESPACE__ . '\parsing_end');
}
