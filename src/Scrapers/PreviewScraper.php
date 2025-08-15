<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class PreviewScraper extends BaseScraper implements PreviewScraperInterface
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

        $scraperFormat = '%s/owpc/pc/race/beforeinfo?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $raceDate->format('Ymd'), $raceStadiumNumber, $raceNumber);
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (!is_null($this->filterXPath($scraper, $levelXPath))) {
            $this->baseLevel = 1;
        }

        $raceWindFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[3]/div/span[2]';
        $raceWindDirectionNumberFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[4]/p';
        $raceWaveFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[6]/div/span[2]';
        $raceWeatherNumberFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[2]/div/span';
        $raceTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[1]/div/span[2]';
        $raceWaterTemperatureFormat = '%s/div[2]/div[%s]/div[2]/div[2]/div[1]/div[5]/div/span[2]';

        $raceWindXPath = sprintf($raceWindFormat, $this->baseXPath, $this->baseLevel + 5);
        $raceWindDirectionNumberXPath = sprintf($raceWindDirectionNumberFormat, $this->baseXPath, $this->baseLevel + 5);
        $raceWaveXPath = sprintf($raceWaveFormat, $this->baseXPath, $this->baseLevel + 5);
        $raceWeatherNameXPath = sprintf($raceWeatherNumberFormat, $this->baseXPath, $this->baseLevel + 5);
        $raceTemperatureXPath = sprintf($raceTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);
        $raceWaterTemperatureXPath = sprintf($raceWaterTemperatureFormat, $this->baseXPath, $this->baseLevel + 5);

        $raceWind = $this->filterXPath($scraper, $raceWindXPath);
        $raceWindDirectionNumber = $this->filterXPathForWindDirectionNumber($scraper, $raceWindDirectionNumberXPath);
        $raceWave = $this->filterXPath($scraper, $raceWaveXPath);
        $raceWeatherName = $this->filterXPath($scraper, $raceWeatherNameXPath);
        $raceTemperature = $this->filterXPath($scraper, $raceTemperatureXPath);
        $raceWaterTemperature = $this->filterXPath($scraper, $raceWaterTemperatureXPath);

        $raceWind = Converter::parseWind($raceWind);
        $raceWindDirectionNumber = Converter::parseWindDirectionNumber($raceWindDirectionNumber);
        $raceWave = Converter::parseWave($raceWave);
        $raceWeatherNumber = Converter::convertToWeatherNumber($raceWeatherName);
        $raceTemperature = Converter::parseTemperature($raceTemperature);
        $raceWaterTemperature = Converter::parseTemperature($raceWaterTemperature);

        $response['race_date'] = $raceDate->format('Y-m-d');
        $response['race_stadium_number'] = $raceStadiumNumber;
        $response['race_number'] = $raceNumber;
        $response['race_wind'] = $raceWind;
        $response['race_wind_direction_number'] = $raceWindDirectionNumber;
        $response['race_wave'] = $raceWave;
        $response['race_weather_number'] = $raceWeatherNumber;
        $response['race_temperature'] = $raceTemperature;
        $response['race_water_temperature'] = $raceWaterTemperature;

        $response += $this->scrapeBoats($scraper, $raceStadiumNumber, $raceNumber);

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

            if (is_null($racerBoatNumber)) {
                continue;
            }

            $racerCourseNumber = $courseNumber;
            $racerBoatNumber = Converter::convertToInt($racerBoatNumber);
            $racerStartTiming = Converter::parseStartTiming($racerStartTiming);

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

            if (is_null($racerBoatNumber)) {
                continue;
            }

            $racerBoatNumber = Converter::convertToInt($racerBoatNumber);
            $racerWeight = Converter::convertToFloat($racerWeight);
            $racerWeightAdjustment = Converter::convertToFloat($racerWeightAdjustment);
            $racerExhibitionTime = Converter::convertToFloat($racerExhibitionTime);
            $racerTiltAdjustment = Converter::convertToFloat($racerTiltAdjustment);

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
