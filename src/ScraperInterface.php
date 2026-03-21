<?php

declare(strict_types=1);

namespace BVP\Scraper;

/**
 * @author shimomo
 */
interface ScraperInterface extends ScraperContractInterface
{
    /**
     * @psalm-param ?\BVP\Scraper\ScraperDispatcherInterface $scraperDispatcher
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperDispatcherInterface $scraperDispatcher
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function getInstance(?ScraperDispatcherInterface $scraperDispatcher = null): ScraperInterface;

    /**
     * @psalm-param ?\BVP\Scraper\ScraperDispatcherInterface $scraperDispatcher
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperDispatcherInterface $scraperDispatcher
     * @return \BVP\Scraper\ScraperInterface
     */
    public static function createInstance(?ScraperDispatcherInterface $scraperDispatcher = null): ScraperInterface;

    /**
     * @psalm-return void
     *
     * @return void
     */
    public static function resetInstance(): void;
}
