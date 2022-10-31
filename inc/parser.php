<?php

namespace WPPerformance\RemoveLazy\inc\parser;

/** determine if string is json */
function isJSON($string)
{
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}

/**
 * extract img url from srcset string
 */
function srcsetToArray($srcset, $min = 400, $max = 900)
{
    if (!$srcset) return [];
    // pattern for find size
    $pattern = '/\s+(\d+)w/';
    // explode string
    $ex = explode(',', $srcset);
    // final array
    $list = [];

    foreach ($ex as $key => $value) {
        // search size for use like a key
        preg_match($pattern, $value, $matches);
        if (!$matches) continue;
        [, $size] = $matches;
        if ($size && $size > $min && $size < $max) {
            $t = preg_replace($pattern, '', $value);
            $list[$size] = trim($t);
        }
    }

    ksort($list);
    return ($list);
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
    // libxml_use_internal_errors(true);

    $document->loadHTML(mb_convert_encoding($string, 'HTML-ENTITIES', 'UTF-8'));

    $xpath = new \DOMXpath($document);

    parseQueryCover($xpath, $document);
    parseQueryImage($xpath, $document);

    return $document->saveHTML();
}

/**
 * parse the cover block
 */
function parseQueryCover(\DOMXpath $xpath, \DOMDocument $document): void
{
    $config = include(dirname(__FILE__) . './../config.php');

    $lazyCover = $xpath->query("//img[contains(@class,'wp-block-cover__image-background')]");

    // head
    [$head] = $xpath->query('/html/head');
    // first child of head
    [$first] = $xpath->query('/html/head/*');

    foreach ($lazyCover as $key => $value) {
        $value->setAttribute('loading', 'eager');
        // add prefetch
        if ($config['preload'] === true) {
            $srcset = $value->getAttribute('srcset');
            $arr = srcsetToArray($srcset);
            foreach ($arr as $k => $v) {
                $pre = $document->createElement('link');
                $pre->setAttribute('rel', 'preload');
                $pre->setAttribute('as', 'image');
                $pre->setAttribute('href', $v);
                $head->insertBefore($pre, $first);
            }
        }
    }
}

/**
 * parse image with no lazy class
 */
function parseQueryImage(\DOMXpath $xpath, \DOMDocument $document): void
{
    $config = include(dirname(__FILE__) . './../config.php');

    $lazyImgs = $xpath->query("//*[contains(@class,'nolazy')]/img");

    // head
    [$head] = $xpath->query('/html/head');
    // first child of head
    [$first] = $xpath->query('/html/head/*');

    foreach ($lazyImgs as $key => $value) {
        $value->setAttribute('loading', 'eager');
        if ($config['preload'] === true) {
            // add prefetch
            $srcset = $value->getAttribute('srcset');
            $arr = srcsetToArray($srcset);
            foreach ($arr as $k => $v) {
                $pre = $document->createElement('link');
                $pre->setAttribute('rel', 'preload');
                $pre->setAttribute('as', 'image');
                $pre->setAttribute('href', $v);
                $head->insertBefore($pre, $first);
            }
        }
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
