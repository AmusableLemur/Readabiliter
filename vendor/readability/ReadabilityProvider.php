<?php

namespace Readability;

use Silex\Application;
use Silex\ServiceProviderInterface;

require 'Readability.php';

class ReadabilityProvider implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['readability'] = $app->protect(function ($source) use ($app) {
            $parser = new \Readability($source);

            return $parser->getContent();
        });
    }
}
