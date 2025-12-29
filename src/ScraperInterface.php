<?php

declare(strict_types=1);

namespace BVP\Scraper;

/**
 * @author shimomo
 */
interface ScraperInterface extends ScraperContractInterface
{
    /**
     * @psalm-param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function getInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface;

    /**
     * @psalm-param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function createInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface;

    /**
     * @psalm-return void
     *
     * @return void
     */
    public static function resetInstance(): void;
}
