<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\Contracts\Scraper;
use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Filters\Filter;
use BVP\Scraper\Filters\WindDirectionFilter;
use BVP\Scraper\Parsers\Parser;
use BVP\Scraper\Parsers\ResultParser;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class ResultScraper extends BaseScraper implements Scraper
{
    /**
     * @var non-empty-string
     */
    private string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @var int<0, 1>
     */
    private int $baseLevel = 0;

    /**
     * @param \Carbon\CarbonInterface $date
     * @param int<1, 24> $stadiumNumber
     * @param int<1, 12> $raceNumber
     * @return array<non-empty-string, mixed>
     */
    #[\Override]
    public function scrape(CarbonInterface $date, int $stadiumNumber, int $raceNumber): array
    {
        $scraperFormat = '%s/owpc/pc/race/raceresult?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (Filter::byXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $windSpeedFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[3]/div/span[2]';
        $windSpeedXPath = sprintf($windSpeedFormat, $this->baseXPath, $this->baseLevel + 6);
        $windSpeed = ResultParser::parseWindSpeed(Filter::byXPath($scraper, $windSpeedXPath));

        $windDirectionFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[4]/p';
        $windDirectionXPath = sprintf($windDirectionFormat, $this->baseXPath, $this->baseLevel + 6);
        $windDirection = ResultParser::parseWindDirection(WindDirectionFilter::byXPath($scraper, $windDirectionXPath));

        $waveHeightFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[6]/div/span[2]';
        $waveHeightXPath = sprintf($waveHeightFormat, $this->baseXPath, $this->baseLevel + 6);
        $waveHeight = ResultParser::parseWaveHeight(Filter::byXPath($scraper, $waveHeightXPath));

        $weatherFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[2]/div/span';
        $weatherXPath = sprintf($weatherFormat, $this->baseXPath, $this->baseLevel + 6);
        $weather = ResultParser::parseWeather(Filter::byXPath($scraper, $weatherXPath));

        $airTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[1]/div/span[2]';
        $airTemperatureXPath = sprintf($airTemperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $airTemperature = ResultParser::parseAirTemperature(Filter::byXPath($scraper, $airTemperatureXPath));

        $waterTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[1]/div/div[1]/div[5]/div/span[2]';
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $waterTemperature = ResultParser::parseWaterTemperature(Filter::byXPath($scraper, $waterTemperatureXPath));

        $techniqueFormat = '%s/div[2]/div[%d]/div[2]/div[1]/div[2]/div[2]/table/tbody/tr/td';
        $techniqueXPath = sprintf($techniqueFormat, $this->baseXPath, $this->baseLevel + 6);
        $technique = ResultParser::parseTechnique(Filter::byXPath($scraper, $techniqueXPath));

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;

        $response += $windSpeed;
        $response += $windDirection;
        $response += $waveHeight;
        $response += $weather;
        $response += $airTemperature;
        $response += $waterTemperature;
        $response += $technique;

        $response += $this->scrapeRacers($scraper);
        $response += $this->scrapePayouts($scraper);

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array<non-empty-string, mixed>
     */
    private function scrapeRacers(Crawler $scraper): array
    {
        $response = ['racers' => []];

        foreach (range(1, 6) as $index) {
            $entryNumberFormat = '%s/div[2]/div[%d]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $entryNumber = Parser::parseEntryNumber(Filter::byXPath($scraper, $entryNumberXPath));

            $course = ['course_number' => $index];

            $startTimingFormat = '%s/div[2]/div[%d]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';
            $startTimingXPath = sprintf($startTimingFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $startTiming = ResultParser::parseStartTiming(Filter::byXPath($scraper, $startTimingXPath));

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
                $course['course_number'] = null;
            }

            $entryNumberKey = $entryNumber['entry_number'];

            if (!in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response['racers'][$entryNumberKey] ??= [];
            $response['racers'][$entryNumberKey] += $entryNumber;
            $response['racers'][$entryNumberKey] += $course;
            $response['racers'][$entryNumberKey] += $startTiming;
        }

        foreach (range(1, 6) as $index) {
            $placeFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[1]';
            $placeXPath = sprintf($placeFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $place = ResultParser::parsePlace(Filter::byXPath($scraper, $placeXPath));

            $entryNumberFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[2]';
            $entryNumberXPath = sprintf($entryNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $entryNumber = Parser::parseEntryNumber(Filter::byXPath($scraper, $entryNumberXPath));

            $numberFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[3]/span[1]';
            $numberXPath = sprintf($numberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $number = Parser::parseNumber(Filter::byXPath($scraper, $numberXPath));

            $nameFormat = '%s/div[2]/div[%d]/div[1]/div/table/tbody[%s]/tr/td[3]/span[2]';
            $nameXPath = sprintf($nameFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $name = Parser::parseName(Filter::byXPath($scraper, $nameXPath));

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
            }

            $entryNumberKey = $entryNumber['entry_number'];

            if (!in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response['racers'][$entryNumberKey] ??= [];
            $response['racers'][$entryNumberKey] += $entryNumber;
            $response['racers'][$entryNumberKey] += $place;
            $response['racers'][$entryNumberKey] += $number;
            $response['racers'][$entryNumberKey] += $name;
        }

        ksort($response['racers'], SORT_NUMERIC);

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array{
     *     payouts?: array{
     *         trifecta?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         trio?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         exacta?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         quinella?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         quinella_place?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         win?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *         place?: list<array{combination: non-empty-string, amount: int<0, max>}>,
     *     }
     * }
     */
    private function scrapePayouts(Crawler $scraper): array
    {
        $response = [];

        $combinations = $this->scrapeAllCombinations($scraper);
        $amounts = $this->scrapeAllAmounts($scraper);

        foreach ($combinations as $name => $values) {
            foreach ($values as $index => $value) {
                if (!isset($response['payouts'][$name])) {
                    $response['payouts'][$name] = [];
                }

                if ($value !== '' && $amounts[$name][$index] !== null) {
                    $response['payouts'][$name][] = [
                        'combination' => $value,
                        'amount' => $amounts[$name][$index],
                    ];
                }
            }
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array{
     *     trifecta: list<string>,
     *     trio: list<string>,
     *     exacta: list<string>,
     *     quinella: list<string>,
     *     quinella_place: list<string>,
     *     win: list<string>,
     *     place: list<string>,
     * }
     */
    private function scrapeAllCombinations(Crawler $scraper): array
    {
        return [
            'trifecta' => $this->scrapeCombinations($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[1]/td[2]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[2]/td[1]/div/div/span[%d]',
            ], range(1, 5)),
            'trio' => $this->scrapeCombinations($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[1]/td[2]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[2]/td[1]/div/div/span[%d]',
            ], range(1, 5)),
            'exacta' => $this->scrapeCombinations($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[1]/td[2]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[2]/td[1]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[3]/td[1]/div/div/span[%d]',
            ], range(1, 3)),
            'quinella' => $this->scrapeCombinations($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[1]/td[2]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[2]/td[1]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[3]/td[1]/div/div/span[%d]',
            ], range(1, 3)),
            'quinella_place' => $this->scrapeCombinations($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[1]/td[2]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[2]/td[1]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[3]/td[1]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[4]/td[1]/div/div/span[%d]',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[5]/td[1]/div/div/span[%d]',
            ], range(1, 3)),
            'win' => $this->scrapeCombinations($scraper, [
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[1]/td[2]/div/div/span[%d]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[2]/td[1]/div/div/span[%d]',
            ], range(1, 1)),
            'place' => $this->scrapeCombinations($scraper, [
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[1]/td[2]/div/div/span[%d]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[2]/td[1]/div/div/span[%d]',
                '%s//div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[3]/td[1]/div/div/span[%d]',
            ], range(1, 1)),
        ];
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param list<non-empty-string> $templates
     * @param list<int> $indexes
     * @return list<string>
     */
    private function scrapeCombinations(Crawler $scraper, array $templates, array $indexes): array
    {
        $response = [];

        foreach ($templates as $template) {
            $values = [];

            foreach ($indexes as $index) {
                $values[] = Filter::byXPath($scraper, sprintf($template, $this->baseXPath, $this->baseLevel + 6, $index));
            }

            $response[] = implode($values);
        }

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array{
     *     trifecta: list<?int<0, max>>,
     *     trio: list<?int<0, max>>,
     *     exacta: list<?int<0, max>>,
     *     quinella: list<?int<0, max>>,
     *     quinella_place: list<?int<0, max>>,
     *     win: list<?int<0, max>>,
     *     place: list<?int<0, max>>,
     * }
     */
    private function scrapeAllAmounts(Crawler $scraper): array
    {
        return [
            'trifecta' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[1]/tr[2]/td[2]/span',
            ]),
            'trio' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[2]/tr[2]/td[2]/span',
            ]),
            'exacta' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[3]/tr[3]/td[2]/span',
            ]),
            'quinella' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[4]/tr[3]/td[2]/span',
            ]),
            'quinella_place' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[3]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[4]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[5]/tr[5]/td[2]/span',
            ]),
            'win' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[6]/tr[2]/td[2]/span',
            ]),
            'place' => $this->scrapeAmounts($scraper, [
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[1]/td[3]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[2]/td[2]/span',
                '%s/div[2]/div[%d]/div[1]/div/table/tbody[7]/tr[3]/td[2]/span',
            ]),
        ];
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param list<non-empty-string> $templates
     * @return list<?int<0, max>>
     */
    private function scrapeAmounts(Crawler $scraper, array $templates): array
    {
        return array_map(function (string $template) use ($scraper): ?int {
            $value = Filter::byXPath($scraper, sprintf($template, $this->baseXPath, $this->baseLevel + 6));

            $value = str_replace(',', '', str_replace('¥', '', $value ?? ''));

            $value = Converter::toInt($value);

            return $value !== null && $value >= 0 ? $value : null;
        }, $templates);
    }
}
