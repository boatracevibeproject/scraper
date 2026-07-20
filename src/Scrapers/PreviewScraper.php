<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\Contracts\Scraper;
use BVP\Scraper\Filters\Filter;
use BVP\Scraper\Filters\WindDirectionFilter;
use BVP\Scraper\Parsers\Parser;
use BVP\Scraper\Parsers\PreviewParser;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class PreviewScraper extends BaseScraper implements Scraper
{
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
        $scraperFormat = '%s/owpc/pc/race/beforeinfo?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndAssertPage('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (Filter::byXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $windSpeedFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[3]/div/span[2]';
        $windSpeedXPath = sprintf($windSpeedFormat, $this->baseXPath, $this->baseLevel + 5);
        $windSpeed = PreviewParser::parseWindSpeed(Filter::byXPath($scraper, $windSpeedXPath));

        $windDirectionFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[4]/p';
        $windDirectionXPath = sprintf($windDirectionFormat, $this->baseXPath, $this->baseLevel + 5);
        $windDirection = PreviewParser::parseWindDirection(WindDirectionFilter::byXPath($scraper, $windDirectionXPath));

        $waveHeightFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[6]/div/span[2]';
        $waveHeightXPath = sprintf($waveHeightFormat, $this->baseXPath, $this->baseLevel + 5);
        $waveHeight = PreviewParser::parseWaveHeight(Filter::byXPath($scraper, $waveHeightXPath));

        $weatherFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[2]/div/span';
        $weatherXPath = sprintf($weatherFormat, $this->baseXPath, $this->baseLevel + 5);
        $weather = PreviewParser::parseWeather(Filter::byXPath($scraper, $weatherXPath));

        $airTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[1]/div/span[2]';
        $airTemperatureXPath = sprintf($airTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);
        $airTemperature = PreviewParser::parseAirTemperature(Filter::byXPath($scraper, $airTemperatureXPath));

        $waterTemperatureFormat = '%s/div[2]/div[%d]/div[2]/div[2]/div[1]/div[5]/div/span[2]';
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);
        $waterTemperature = PreviewParser::parseWaterTemperature(Filter::byXPath($scraper, $waterTemperatureXPath));

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

        $response += $this->scrapeRacers($scraper);

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
            $entryNumberFormat = '%s/div[2]/div[%d]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $entryNumber = Parser::parseEntryNumber(Filter::byXPath($scraper, $entryNumberXPath));

            $course = ['course_number' => $index];

            $startTimingFormat = '%s/div[2]/div[%d]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[3]';
            $startTimingXPath = sprintf($startTimingFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $startTiming = PreviewParser::parseStartTiming(Filter::byXPath($scraper, $startTimingXPath));

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
            $entryNumberFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $entryNumber = Parser::parseEntryNumber(Filter::byXPath($scraper, $entryNumberXPath));

            $weightFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[4]';
            $weightXPath = sprintf($weightFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $weight = PreviewParser::parseWeight(Filter::byXPath($scraper, $weightXPath));

            $weightAdjustmentFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[3]/td[1]';
            $weightAdjustmentXPath = sprintf($weightAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $weightAdjustment = PreviewParser::parseWeightAdjustment(Filter::byXPath($scraper, $weightAdjustmentXPath));

            $exhibitionTimeFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[5]';
            $exhibitionTimeXPath = sprintf($exhibitionTimeFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $exhibitionTime = PreviewParser::parseExhibitionTime(Filter::byXPath($scraper, $exhibitionTimeXPath));

            $tiltAdjustmentFormat = '%s/div[2]/div[%d]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[6]';
            $tiltAdjustmentXPath = sprintf($tiltAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $tiltAdjustment = PreviewParser::parseTiltAdjustment(Filter::byXPath($scraper, $tiltAdjustmentXPath));

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
            }

            $entryNumberKey = $entryNumber['entry_number'];

            if (!in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response['racers'][$entryNumberKey] ??= [];
            $response['racers'][$entryNumberKey] += $entryNumber;
            $response['racers'][$entryNumberKey] += $weight;
            $response['racers'][$entryNumberKey] += $weightAdjustment;
            $response['racers'][$entryNumberKey] += $exhibitionTime;
            $response['racers'][$entryNumberKey] += $tiltAdjustment;
        }

        ksort($response['racers'], SORT_NUMERIC);

        return $response;
    }
}
