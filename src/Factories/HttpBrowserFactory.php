<?php

declare(strict_types=1);

namespace BVP\Scraper\Factories;

use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * @author shimomo
 */
final class HttpBrowserFactory
{
    /**
     * Builds a UA-spoofed HttpBrowser. $extraParameters lets a caller layer
     * in per-instance server params (e.g. a proxy-specific header) so that
     * multiple Scraper instances can each carry their own browser identity.
     *
     * @param array<non-empty-string, non-empty-string> $extraParameters
     * @return \Symfony\Component\BrowserKit\HttpBrowser
     */
    public static function create(array $extraParameters = []): HttpBrowser
    {
        $httpBrowser = new HttpBrowser();

        $httpBrowser->setServerParameters(array_merge([
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 ' .
                '(KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,' .
                'image/webp,image/apng,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'ja,en-US;q=0.9,en;q=0.8',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
            'HTTP_SEC_CH_UA' => '"Google Chrome";v="140", "Chromium";v="140", "Not=A?Brand";v="24"',
            'HTTP_SEC_CH_UA_PLATFORM' => '"Windows"',
            'HTTP_SEC_CH_UA_MOBILE' => '?0',
            'HTTP_SEC_FETCH_SITE' => 'none',
            'HTTP_SEC_FETCH_MODE' => 'navigate',
            'HTTP_SEC_FETCH_USER' => '?1',
            'HTTP_SEC_FETCH_DEST' => 'document',
        ], $extraParameters));

        return $httpBrowser;
    }
}
