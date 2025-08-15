<?php

declare(strict_types=1);

namespace BVP\Scraper\Traits;

use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
trait HttpBrowserInitializer
{
    /**
     * @param  \Symfony\Component\BrowserKit\HttpBrowser  $httpBrowser
     * @return void
     */
    private function initializeHttpBrowser(HttpBrowser $httpBrowser): void
    {
        $httpBrowser->setServerParameters([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'ja;q=0.8',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
        ]);
    }
}
