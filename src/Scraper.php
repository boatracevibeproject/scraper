<?php

declare(strict_types=1);

namespace BVP\Scraper;

/**
 * @psalm-type ScraperArguments = array{}
 *     |array{CarbonInterface|string|null}
 *     |array{CarbonInterface|string|null, int<1,24>|string|null}
 *     |array{CarbonInterface|string|null, int<1,24>|string|null, int<1,12>|string|null}
 *     |array{CarbonInterface|string|null, int<1,24>|string|null, int<1,12>|string|null, ...list<mixed>}
 * @psalm-method static array<array-key, mixed> scrapePrograms(mixed ...$arguments)
 * @psalm-method static array<array-key, mixed> scrapePreviews(mixed ...$arguments)
 * @psalm-method static array<array-key, mixed> scrapeOdds(mixed ...$arguments)
 * @psalm-method static array<array-key, mixed> scrapeResults(mixed ...$arguments)
 * @psalm-method static array<array-key, mixed> scrapeStadiums(mixed ...$arguments)
 *
 * @author shimomo
 */
final class Scraper implements ScraperInterface
{
    /**
     * @psalm-var \BVP\Scraper\ScraperInterface
     *
     * @var \BVP\Scraper\ScraperInterface
     */
    private static ?ScraperInterface $instance;

    /**
     * @psalm-param \BVP\Scraper\ScraperCoreInterface $scraper
     *
     * @param \BVP\Scraper\ScraperCoreInterface $scraper
     */
    public function __construct(private readonly ScraperCoreInterface $scraper)
    {
        //
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param ScraperArguments $arguments
     * @psalm-return array<array-key, mixed>
     *
     * @param string $name
     * @param array $arguments
     * @return array
     */
    public function __call(string $name, array $arguments): array
    {
        $response = $this->scraper->$name(...$arguments);

        if (!is_array($response)) {
            $actualType = is_object($response)
                ? get_class($response)
                : gettype($response);

            throw new \LogicException(
                __METHOD__ . "() - Unexpected return value from scraper method '{$name}'. " .
                "Expected array, got {$actualType}."
            );
        }

        return $response;
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param ScraperArguments $arguments
     * @psalm-return array<array-key, mixed>
     *
     * @param string $name
     * @param array $arguments
     * @return array
     */
    public static function __callStatic(string $name, array $arguments): array
    {
        $response = self::getInstance()->$name(...$arguments);

        if (!is_array($response)) {
            $actualType = is_object($response)
                ? get_class($response)
                : gettype($response);

            throw new \LogicException(
                __METHOD__ . "() - Unexpected return value from scraper method '{$name}'. " .
                "Expected array, got {$actualType}."
            );
        }

        return $response;
    }

    /**
     * @psalm-param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    #[\Override]
    public static function getInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface
    {
        return self::$instance ??= new self($scraperCore ?? new ScraperCore());
    }

    /**
     * @psalm-param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @psalm-return \BVP\Scraper\ScraperInterface
     *
     * @param ?\BVP\Scraper\ScraperCoreInterface $scraperCore
     * @return \BVP\Scraper\ScraperInterface
     */
    #[\Override]
    public static function createInstance(?ScraperCoreInterface $scraperCore = null): ScraperInterface
    {
        return self::$instance = new self($scraperCore ?? new ScraperCore());
    }

    /**
     * @psalm-return void
     *
     * @return void
     */
    #[\Override]
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
