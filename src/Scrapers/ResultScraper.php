<?php

declare(strict_types=1);

namespace BVP\BoatraceScraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class ResultScraper extends BaseScraper implements ResultScraperInterface
{
    /**
     * @var string
     */
    private string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @param  \Carbon\CarbonInterface  $raceDate
     * @param  int                      $raceStadiumNumber
     * @param  int                      $raceNumber
     * @return array
     */
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
        if (!is_null($this->filterXPath($scraper, $levelXPath))) {
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

        $response += $this->scrapeBoats($scraper, $raceStadiumNumber, $raceNumber);
        $response += $this->scrapeRefunds($scraper);

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @param  int                                    $raceStadiumNumber
     * @param  int                                    $raceNumber
     * @return array
     */
    private function scrapeBoats(Crawler $scraper, int $raceStadiumNumber, int $raceNumber): array
    {
        $response = [];

        $racerBoatNumberFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[1]';
        $racerStartTimingFormat = '%s/div[2]/div[%s]/div[2]/div/table/tbody/tr[%s]/td/div/span[3]/span';

        foreach (range(1, 6) as $courseNumber) {
            $racerBoatNumberXPath = sprintf($racerBoatNumberFormat, $this->baseXPath, $this->baseLevel + 5, $courseNumber);
            $racerStartTimingXPath = sprintf($racerStartTimingFormat, $this->baseXPath, $this->baseLevel + 5, $courseNumber);

            $racerBoatNumber = $this->filterXPath($scraper, $racerBoatNumberXPath);
            $racerStartTiming = $this->filterXPath($scraper, $racerStartTimingXPath);

            $racerCourseNumber = is_null($racerBoatNumber) ? null : $courseNumber;
            $racerBoatNumber = Converter::convertToInt($racerBoatNumber) ?? $courseNumber;
            $racerStartTiming = Converter::parseStartTiming($racerStartTiming);

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
            $racerBoatNumber = Converter::convertToInt($racerBoatNumber) ?? $placeNumber;
            $racerNumber = Converter::convertToInt($racerNumber);
            $racerName = Converter::convertToName($racerName);

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_place_number'] = $racerPlaceNumber;
            $response['boats'][$racerBoatNumber]['racer_number'] = $racerNumber;
            $response['boats'][$racerBoatNumber]['racer_name'] = $racerName;
        }

        ksort($response['boats']);

        return $response;
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @return array
     */
    private function scrapeRefunds(Crawler $scraper): array
    {
        $response = [];

        $scrapedCombinations = $this->scrapeCombinations($scraper);
        $scrapedPayouts = $this->scrapePayouts($scraper);

        foreach ($scrapedCombinations as $type => $combinations) {
            foreach ($combinations as $index => $combination) {
                if (!isset($response['payouts'][$type])) {
                    $response['payouts'][$type] = [];
                }

                if (!empty($combination) && !empty($scrapedPayouts[$type][$index])) {
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
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @return array
     */
    private function scrapeCombinations(Crawler $scraper): array
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

        $trifecta = $this->getCombinations($scraper, $trifectaTemplates, 1, 5);
        $trio = $this->getCombinations($scraper, $trioTemplates, 1, 5);
        $exacta = $this->getCombinations($scraper, $exactaTemplates, 1, 3);
        $quinella = $this->getCombinations($scraper, $quinellaTemplates, 1, 3);
        $quinella_place = $this->getCombinations($scraper, $quinellaPlaceTemplates, 1, 3);
        $win = $this->getCombinations($scraper, $winTemplates, 1, 1);
        $place = $this->getCombinations($scraper, $placeTemplates, 1, 1);

        return compact('trifecta', 'trio', 'exacta', 'quinella', 'quinella_place', 'win', 'place');
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @return array
     */
    private function scrapePayouts(Crawler $scraper): array
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

        $trifecta = $this->getPayouts($scraper, $trifectaTemplates);
        $trio = $this->getPayouts($scraper, $trioTemplates);
        $exacta = $this->getPayouts($scraper, $exactaTemplates);
        $quinella = $this->getPayouts($scraper, $quinellaTemplates);
        $quinella_place = $this->getPayouts($scraper, $quinellaPlaceTemplates);
        $win = $this->getPayouts($scraper, $winTemplates);
        $place = $this->getPayouts($scraper, $placeTemplates);

        return compact('trifecta', 'trio', 'exacta', 'quinella', 'quinella_place', 'win', 'place');
    }

    /**
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @param  array                                  $templates
     * @param  int                                    $first
     * @param  int                                    $last
     * @return array
     */
    private function getCombinations(Crawler $scraper, array $templates, int $first, int $last): array
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
     * @param  \Symfony\Component\DomCrawler\Crawler  $scraper
     * @param  array                                  $templates
     * @return array
     */
    private function getPayouts($scraper, array $templates): array
    {
        return array_map(function ($template) use ($scraper) {
            $value = $this->filterXPath($scraper, sprintf($template, $this->baseXPath));
            $value = str_replace('¥', '', $value ?? '');
            $value = str_replace(',', '', $value ?? '');
            return Converter::convertToInt($value);
        }, $templates);
    }
}
