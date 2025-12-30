<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class PreviewScraper extends BaseScraper implements PreviewScraperInterface
{
    /**
     * @psalm-var non-empty-string
     *
     * @var string
     */
    private string $baseXPath = 'descendant-or-self::body/main/div/div/div';

    /**
     * @psalm-param \Carbon\CarbonInterface $date
     * @psalm-param int<1, 24> $stadiumNumber
     * @psalm-param int<1, 12> $number
     * @psalm-return array<non-empty-string, mixed>
     *
     * @param \Carbon\CarbonInterface $date
     * @param int $stadiumNumber
     * @param int $number
     * @return array
     */
    #[\Override]
    public function scrape(CarbonInterface $date, int $stadiumNumber, int $number): array
    {
        $response = [];

        $scraperFormat = '%s/owpc/pc/race/beforeinfo?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $number);
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if ($this->filterXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $windSpeedFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[3]/div/span[2]';
        $windDirectionNumberFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[4]/p';
        $waveHeightFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[6]/div/span[2]';
        $weatherNumberFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[2]/div/span';
        $airTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[1]/div/span[2]';
        $waterTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[5]/div/span[2]';

        $windSpeedXPath = sprintf($windSpeedFormat, $this->baseXPath, $this->baseLevel + 5);
        $windDirectionNumberXPath = sprintf($windDirectionNumberFormat, $this->baseXPath, $this->baseLevel + 5);
        $waveHeightXPath = sprintf($waveHeightFormat, $this->baseXPath, $this->baseLevel + 5);
        $weatherNameXPath = sprintf($weatherNumberFormat, $this->baseXPath, $this->baseLevel + 5);
        $airTemperatureXPath = sprintf($airTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);
        $waterTemperatureXPath = sprintf($waterTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);

        $windSpeed = $this->filterXPath($scraper, $windSpeedXPath);
        $windDirectionNumber = $this->filterXPathForWindDirectionNumber($scraper, $windDirectionNumberXPath);
        $waveHeight = $this->filterXPath($scraper, $waveHeightXPath);
        $weatherName = $this->filterXPath($scraper, $weatherNameXPath);
        $airTemperature = $this->filterXPath($scraper, $airTemperatureXPath);
        $waterTemperature = $this->filterXPath($scraper, $waterTemperatureXPath);

        $windSpeed = Converter::parseWindSpeed($windSpeed);
        $windDirectionNumber = Converter::parseWindDirectionNumber($windDirectionNumber);
        $waveHeight = Converter::parseWaveHeight($waveHeight);
        $weatherNumber = Converter::convertToWeatherNumber($weatherName);
        $airTemperature = Converter::parseTemperature($airTemperature);
        $waterTemperature = Converter::parseTemperature($waterTemperature);

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['number'] = $number;
        $response['wind_speed'] = $windSpeed;
        $response['wind_direction_number'] = $windDirectionNumber;
        $response['wave_height'] = $waveHeight;
        $response['weather_number'] = $weatherNumber;
        $response['air_temperature'] = $airTemperature;
        $response['water_temperature'] = $waterTemperature;

        $response += $this->scrapeBoats($scraper);

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
            $response['boats'][$boatNumber]['racer_weight'] = null;
            $response['boats'][$boatNumber]['racer_weight_adjustment'] = null;
            $response['boats'][$boatNumber]['racer_exhibition_time'] = null;
            $response['boats'][$boatNumber]['racer_tilt_adjustment'] = null;
        }

        $racerBoatNumberFormat = '%s/div[2]/div[%s]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[1]';
        $racerStartTimingFormat = '%s/div[2]/div[%s]/div[2]/div[1]/table/tbody/tr[%s]/td/div/span[3]';

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

        $racerBoatNumberFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[1]';
        $racerWeightFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[4]';
        $racerWeightAdjustmentFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[3]/td[1]';
        $racerExhibitionTimeFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[5]';
        $racerTiltAdjustmentFormat = '%s/div[2]/div[%s]/div[1]/div[1]/table/tbody[%s]/tr[1]/td[6]';

        foreach (range(1, 6) as $boatNumber) {
            $racerBoatNumberXPath = sprintf($racerBoatNumberFormat, $this->baseXPath, $this->baseLevel + 5, $boatNumber);
            $racerWeightXPath = sprintf($racerWeightFormat, $this->baseXPath, $this->baseLevel + 5, $boatNumber);
            $racerWeightAdjustmentXPath = sprintf($racerWeightAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $boatNumber);
            $racerExhibitionTimeXPath = sprintf($racerExhibitionTimeFormat, $this->baseXPath, $this->baseLevel + 5, $boatNumber);
            $racerTiltAdjustmentXPath = sprintf($racerTiltAdjustmentFormat, $this->baseXPath, $this->baseLevel + 5, $boatNumber);

            $racerBoatNumber = $this->filterXPath($scraper, $racerBoatNumberXPath);
            $racerWeight = $this->filterXPath($scraper, $racerWeightXPath);
            $racerWeightAdjustment = $this->filterXPath($scraper, $racerWeightAdjustmentXPath);
            $racerExhibitionTime = $this->filterXPath($scraper, $racerExhibitionTimeXPath);
            $racerTiltAdjustment = $this->filterXPath($scraper, $racerTiltAdjustmentXPath);

            $racerBoatNumber = Converter::convertToInt($racerBoatNumber);
            $racerWeight = Converter::convertToFloat($racerWeight);
            $racerWeightAdjustment = Converter::convertToFloat($racerWeightAdjustment);
            $racerExhibitionTime = Converter::convertToFloat($racerExhibitionTime);
            $racerTiltAdjustment = Converter::convertToFloat($racerTiltAdjustment);

            if ($racerBoatNumber === null) {
                continue;
            }

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_weight'] = $racerWeight;
            $response['boats'][$racerBoatNumber]['racer_weight_adjustment'] = $racerWeightAdjustment;
            $response['boats'][$racerBoatNumber]['racer_exhibition_time'] = $racerExhibitionTime;
            $response['boats'][$racerBoatNumber]['racer_tilt_adjustment'] = $racerTiltAdjustment;
        }

        ksort($response['boats']);

        return $response;
    }
}
