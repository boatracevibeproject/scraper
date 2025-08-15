<?php

declare(strict_types=1);

namespace BVP\Scraper;

/**
 * @author shimomo
 */
interface ScraperInterface extends ScraperContractInterface
{
    /**
     * @param  \BVP\Scraper\ScraperCoreInterface|null  $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function getInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface;

    /**
     * @param  \BVP\Scraper\ScraperCoreInterface|null  $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function createInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface;

    /**
     * @return void
     */
    public static function resetInstance(): void;
}
