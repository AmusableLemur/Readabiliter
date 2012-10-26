<?php

namespace Readability\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

require __DIR__.'/../Readability/Readability.php';

class ReadabilityProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['readability'] = $app->protect(function ($html, $url) use ($app) {
            if (function_exists('mb_detect_encoding')) {
                $from = mb_detect_encoding($html, 'auto');
                $html = mb_convert_encoding($html, 'UTF-8', $from);
            }

            if (function_exists('tidy_parse_string')) {
                $tidy = tidy_parse_string($html, array(), 'UTF8');
                $tidy->cleanRepair();

                $html = $tidy->value;
            }

            $readability = new \Readability($html, $url);

            $readability->debug = false;
            $readability->convertLinksToFootnotes = true;

            $result = $readability->init();

            if ($result === false) {
                throw new RunTimeException('Parsing of document failed');
            }

            return array(
                'title' => $readability->getTitle()->textContent,
                'content' => $readability->getContent()->innerHTML
            );
        });
    }
}
