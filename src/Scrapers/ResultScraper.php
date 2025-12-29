<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class ResultScraper extends BaseScraper implements ResultScraperInterface
{
    /**
     * @psalm-var non-empty-string
     *
     * @var string
     */
    private string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @psalm-param \Carbon\CarbonInterface $raceDate
     * @psalm-param int<1, 24> $raceStadiumNumber
     * @psalm-param int<1, 12> $raceNumber
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $raceDate
     * @param int $raceStadiumNumber
     * @param int $raceNumber
     * @return array
     */
    #[\Override]
    public function scrape(CarbonInterface $raceDate, int $raceStadiumNumber, int $raceNumber): array
    {
        $response = [];

        $scraperFormat = '%s/owpc/pc/race/raceresult?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $raceDate->format('Ymd'), $raceStadiumNumber, $raceNumber);
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if ($this->filterXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $raceWindFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[3]/div/span[2]';
        $raceWindDirectionNumberFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[4]/p';
        $raceWaveFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[6]/div/span[2]';
        $raceWeatherNameFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[2]/div/span';
        $raceTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[1]/div/span[2]';
        $raceWaterTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[1]/div/div[1]/div[5]/div/span[2]';
        $raceTechniqueNameFormat = '%s/div[2]/div[%s]/div[2]/div[1]/div[2]/div[2]/table/tbody/tr/td';

        $raceWindXPath = sprintf($raceWindFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceWindDirectionNumberXPath = sprintf($raceWindDirectionNumberFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceWaveXPath = sprintf($raceWaveFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceWeatherNameXPath = sprintf($raceWeatherNameFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceTemperatureXPath = sprintf($raceTemperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceWaterTemperatureXPath = sprintf($raceWaterTemperatureFormat, $this->baseXPath, $this->baseLevel + 6);
        $raceTechniqueNameXPath = sprintf($raceTechniqueNameFormat, $this->baseXPath, $this->baseLevel + 6);

        $raceWind = $this->filterXPath($scraper, $raceWindXPath);
        $raceWindDirectionNumber = $this->filterXPathForWindDirectionNumber($scraper, $raceWindDirectionNumberXPath);
        $raceWave = $this->filterXPath($scraper, $raceWaveXPath);
        $raceWeatherName = $this->filterXPath($scraper, $raceWeatherNameXPath);
        $raceTemperature = $this->filterXPath($scraper, $raceTemperatureXPath);
        $raceWaterTemperature = $this->filterXPath($scraper, $raceWaterTemperatureXPath);
        $raceTechniqueName = $this->filterXPath($scraper, $raceTechniqueNameXPath);

        $raceWind = Converter::parseWind($raceWind);
        $raceWindDirectionNumber = Converter::parseWindDirectionNumber($raceWindDirectionNumber);
        $raceWave = Converter::parseWave($raceWave);
        $raceWeatherNumber = Converter::convertToWeatherNumber($raceWeatherName);
        $raceTemperature = Converter::parseTemperature($raceTemperature);
        $raceWaterTemperature = Converter::parseTemperature($raceWaterTemperature);
        $raceTechniqueNumber = Converter::convertToTechniqueNumber($raceTechniqueName);

        $response['race_date'] = $raceDate->format('Y-m-d');
        $response['race_stadium_number'] = $raceStadiumNumber;
        $response['race_number'] = $raceNumber;
        $response['race_wind'] = $raceWind;
        $response['race_wind_direction_number'] = $raceWindDirectionNumber;
        $response['race_wave'] = $raceWave;
        $response['race_weather_number'] = $raceWeatherNumber;
        $response['race_temperature'] = $raceTemperature;
        $response['race_water_temperature'] = $raceWaterTemperature;
        $response['race_technique_number'] = $raceTechniqueNumber;

        $response += $this->scrapeBoats($scraper);
        $response += $this->scrapePayouts($scraper);

        return $response;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array
     */
    private function scrapeBoats(Crawler $scraper): array
    {
        $response = [];

        foreach (range(1, 6) as $boatNumber) {
            $response['boats'][$boatNumber]['racer_boat_number'] = $boatNumber;
            $response['boats'][$boatNumber]['racer_course_number'] = null;
            $response['boats'][$boatNumber]['racer_start_timing'] = null;
            $response['boats'][$boatNumber]['racer_place_number'] = null;
            $response['boats'][$boatNumber]['racer_number'] = null;
            $response['boats'][$boatNumber]['racer_name'] = null;
        }

        $racerBoatNumberFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
        $racerStartTimingFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';

        foreach (range(1, 6) as $courseNumber) {
            $racerBoatNumberXPath = sprintf($racerBoatNumberFormat, $this->baseXPath, $this->baseLevel + 5, $courseNumber);
            $racerStartTimingXPath = sprintf($racerStartTimingFormat, $this->baseXPath, $this->baseLevel + 5, $courseNumber);

            $racerBoatNumber = $this->filterXPath($scraper, $racerBoatNumberXPath);
            $racerStartTiming = $this->filterXPath($scraper, $racerStartTimingXPath);

            $racerCourseNumber = $courseNumber;
            $racerBoatNumber = Converter::convertToInt($racerBoatNumber);
            $racerStartTiming = Converter::parseStartTiming($racerStartTiming);

            if ($racerBoatNumber === null) {
                continue;
            }

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_course_number'] = $racerCourseNumber;
            $response['boats'][$racerBoatNumber]['racer_start_timing'] = $racerStartTiming;
        }

        $racerPlaceNameFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[1]';
        $racerBoatNumberFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[2]';
        $racerNumberFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[3]/span[1]';
        $racerNameFormat = '%s/div[2]/div[%s]/div[1]/div/table/tbody[%s]/tr/td[3]/span[2]';

        foreach (range(1, 6) as $placeNumber) {
            $racerPlaceNameXPath = sprintf($racerPlaceNameFormat, $this->baseXPath, $this->baseLevel + 5, $placeNumber);
            $racerBoatNumberXPath = sprintf($racerBoatNumberFormat, $this->baseXPath, $this->baseLevel + 5, $placeNumber);
            $racerNumberXPath = sprintf($racerNumberFormat, $this->baseXPath, $this->baseLevel + 5, $placeNumber);
            $racerNameXPath = sprintf($racerNameFormat, $this->baseXPath, $this->baseLevel + 5, $placeNumber);

            $racerPlaceName = $this->filterXPath($scraper, $racerPlaceNameXPath);
            $racerBoatNumber = $this->filterXPath($scraper, $racerBoatNumberXPath);
            $racerNumber = $this->filterXPath($scraper, $racerNumberXPath);
            $racerName = $this->filterXPath($scraper, $racerNameXPath);

            $racerPlaceNumber = Converter::convertToPlaceNumber($racerPlaceName);
            $racerBoatNumber = Converter::convertToInt($racerBoatNumber);
            $racerNumber = Converter::convertToInt($racerNumber);
            $racerName = Converter::convertToName($racerName);

            if ($racerBoatNumber === null) {
                continue;
            }

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_place_number'] = $racerPlaceNumber;
            $response['boats'][$racerBoatNumber]['racer_number'] = $racerNumber;
            $response['boats'][$racerBoatNumber]['racer_name'] = $racerName;
        }

        ksort($response['boats']);

        return $response;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-return array{
     *     payouts?: array{
     *         trifecta?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         trio?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         exacta?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         quinella?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         quinella_place?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         win?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *         place?: list<array{combination: non-empty-string, payout: int<0, max>}>,
     *     }
     * }
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array
     */
    private function scrapePayouts(Crawler $scraper): array
    {
        $response = [];

        $scrapedCombinations = $this->filterAllCombinations($scraper);
        $scrapedPayouts = $this->filterAllPayouts($scraper);

        foreach ($scrapedCombinations as $type => $combinations) {
            foreach ($combinations as $index => $combination) {
                if (!isset($response['payouts'][$type])) {
                    $response['payouts'][$type] = [];
                }

                if ($combination !== '' && $scrapedPayouts[$type][$index] !== null) {
                    $response['payouts'][$type][] = [
                        'combination' => $combination,
                        'payout' => $scrapedPayouts[$type][$index],
                    ];
                }
            }
        }

        return $response;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-return array{
     *     trifecta: list<string>,
     *     trio: list<string>,
     *     exacta: list<string>,
     *     quinella: list<string>,
     *     quinella_place: list<string>,
     *     win: list<string>,
     *     place: list<string>,
     * }
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array
     */
    private function filterAllCombinations(Crawler $scraper): array
    {
        $trifectaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[1]/tr[1]/td[2]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[1]/tr[2]/td[1]/div/div/span[%d]',
        ];

        $trioTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[2]/tr[1]/td[2]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[2]/tr[2]/td[1]/div/div/span[%d]',
        ];

        $exactaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[1]/td[2]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[2]/td[1]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[3]/td[1]/div/div/span[%d]',
        ];

        $quinellaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[1]/td[2]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[2]/td[1]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[3]/td[1]/div/div/span[%d]',
        ];

        $quinellaPlaceTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[1]/td[2]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[2]/td[1]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[3]/td[1]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[4]/td[1]/div/div/span[%d]',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[5]/td[1]/div/div/span[%d]',
        ];

        $winTemplates = [
            '%s//div[2]/div[6]/div[1]/div/table/tbody[6]/tr[1]/td[2]/div/div/span[%d]',
            '%s//div[2]/div[6]/div[1]/div/table/tbody[6]/tr[2]/td[1]/div/div/span[%d]',
        ];

        $placeTemplates = [
            '%s//div[2]/div[6]/div[1]/div/table/tbody[7]/tr[1]/td[2]/div/div/span[%d]',
            '%s//div[2]/div[6]/div[1]/div/table/tbody[7]/tr[2]/td[1]/div/div/span[%d]',
            '%s//div[2]/div[6]/div[1]/div/table/tbody[7]/tr[3]/td[1]/div/div/span[%d]',
        ];

        $trifecta = $this->filterCombinations($scraper, $trifectaTemplates, 1, 5);
        $trio = $this->filterCombinations($scraper, $trioTemplates, 1, 5);
        $exacta = $this->filterCombinations($scraper, $exactaTemplates, 1, 3);
        $quinella = $this->filterCombinations($scraper, $quinellaTemplates, 1, 3);
        $quinella_place = $this->filterCombinations($scraper, $quinellaPlaceTemplates, 1, 3);
        $win = $this->filterCombinations($scraper, $winTemplates, 1, 1);
        $place = $this->filterCombinations($scraper, $placeTemplates, 1, 1);

        return compact('trifecta', 'trio', 'exacta', 'quinella', 'quinella_place', 'win', 'place');
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-return array{
     *     trifecta: list<?int<0, max>>,
     *     trio: list<?int<0, max>>,
     *     exacta: list<?int<0, max>>,
     *     quinella: list<?int<0, max>>,
     *     quinella_place: list<?int<0, max>>,
     *     win: list<?int<0, max>>,
     *     place: list<?int<0, max>>,
     * }
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array
     */
    private function filterAllPayouts(Crawler $scraper): array
    {
        $trifectaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[1]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[1]/tr[2]/td[2]/span',
        ];

        $trioTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[2]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[2]/tr[2]/td[2]/span',
        ];

        $exactaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[2]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[3]/tr[3]/td[2]/span',
        ];

        $quinellaTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[2]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[4]/tr[3]/td[2]/span',
        ];

        $quinellaPlaceTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[2]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[3]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[4]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[5]/tr[5]/td[2]/span',
        ];

        $winTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[6]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[6]/tr[2]/td[2]/span',
        ];

        $placeTemplates = [
            '%s/div[2]/div[6]/div[1]/div/table/tbody[7]/tr[1]/td[3]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[7]/tr[2]/td[2]/span',
            '%s/div[2]/div[6]/div[1]/div/table/tbody[7]/tr[3]/td[2]/span',
        ];

        $trifecta = $this->filterPayouts($scraper, $trifectaTemplates);
        $trio = $this->filterPayouts($scraper, $trioTemplates);
        $exacta = $this->filterPayouts($scraper, $exactaTemplates);
        $quinella = $this->filterPayouts($scraper, $quinellaTemplates);
        $quinella_place = $this->filterPayouts($scraper, $quinellaPlaceTemplates);
        $win = $this->filterPayouts($scraper, $winTemplates);
        $place = $this->filterPayouts($scraper, $placeTemplates);

        return compact('trifecta', 'trio', 'exacta', 'quinella', 'quinella_place', 'win', 'place');
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param list<non-empty-string> $templates
     * @psalm-param int<0, max> $first
     * @psalm-param int<0, max> $last
     * @psalm-return list<string>
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param array $templates
     * @param int $first
     * @param int $last
     * @return array
     */
    private function filterCombinations(Crawler $scraper, array $templates, int $first, int $last): array
    {
        $response = [];
        foreach ($templates as $template) {
            $values = [];
            foreach (range($first, $last) as $index) {
                $values[] = $this->filterXPath(
                    $scraper,
                    sprintf($template, $this->baseXPath, $index)
                );
            }

            $response[] = implode($values);
        }

        return $response;
    }

    /**
     * @psalm-param \Symfony\Component\DomCrawler\Crawler $scraper
     * @psalm-param list<non-empty-string> $templates
     * @psalm-return list<?int<0, max>>
     *
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param array $templates
     * @return array
     */
    private function filterPayouts(Crawler $scraper, array $templates): array
    {
        return array_map(function (string $template) use ($scraper) {
            $value = $this->filterXPath($scraper, sprintf($template, $this->baseXPath));
            $value = str_replace(',', '', str_replace('¥', '', $value ?? ''));
            $value = Converter::convertToInt($value);
            return $value >= 0 ? $value : null;
        }, $templates);
    }
}
