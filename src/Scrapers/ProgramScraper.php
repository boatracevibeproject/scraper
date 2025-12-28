<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use BVP\Trimmer\Trimmer;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
class ProgramScraper extends BaseScraper implements ProgramScraperInterface
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

        $scraperFormat = '%s/owpc/pc/race/racelist?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $raceDate->format('Ymd'), $raceStadiumNumber, $raceNumber);
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if ($this->filterXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $raceDayLabelFormat = '%s/div[2]/div[1]/ul/li[%s]/span/span';
        $raceGradeFormat = '%s/div[1]/div/div[2]';
        $raceTitleFormat = '%s/div[1]/div/div[2]/h2';
        $raceSubtitleDistanceFormat = '%s/div[2]/div[%s]/h3';
        $raceDeadlineFormat = '%s/div[2]/div[2]/table/tbody/tr[1]/td[%s]';

        foreach (range(1, 14) as $index) {
            $raceDayLabelXPath = sprintf($raceDayLabelFormat, $this->baseXPath, $index);
            $raceDayLabel = $this->filterXPath($scraper, $raceDayLabelXPath);
            if ($raceDayLabel !== null) {
                break;
            }
        }

        $raceGradeXPath = sprintf($raceGradeFormat, $this->baseXPath);
        $raceTitleXPath = sprintf($raceTitleFormat, $this->baseXPath);
        $raceSubtitleDistanceXPath = sprintf($raceSubtitleDistanceFormat, $this->baseXPath, $this->baseLevel + 3);
        $raceDeadlineXPath = sprintf($raceDeadlineFormat, $this->baseXPath, $raceNumber + 1);

        $raceGradeLabel = $this->filterXPathForGradeLabel($scraper, $raceGradeXPath);
        $raceGradeNumber = $this->filterXPathForGradeNumber($scraper, $raceGradeXPath);
        $raceTitle = $this->filterXPath($scraper, $raceTitleXPath);
        $raceSubtitleDistance = $this->filterXPath($scraper, $raceSubtitleDistanceXPath);
        $raceDeadline = $this->filterXPath($scraper, $raceDeadlineXPath);

        $raceClosedAt = null;
        if ($raceDeadline !== null) {
            $raceClosedAt = $raceDate->setTimeFromTimeString($raceDeadline)->format('Y-m-d H:i:s');
        }

        [$raceSubtitle, $raceDistance] = $this->explodeSubtitleDistance($raceSubtitleDistance);

        $response['race_date'] = $raceDate->format('Y-m-d');
        $response['race_stadium_number'] = $raceStadiumNumber;
        $response['race_number'] = $raceNumber;
        $response['race_closed_at'] = $raceClosedAt;
        $response['race_day_label'] = $raceDayLabel;
        $response['race_grade_label'] = $raceGradeLabel;
        $response['race_grade_number'] = $raceGradeNumber;
        $response['race_title'] = $raceTitle;
        $response['race_subtitle'] = $raceSubtitle;
        $response['race_distance'] = $raceDistance;

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

        $racerBoatNumberFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[1]';
        $racerNameFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[2]/a';
        $racerNumberClassFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[1]';
        $racerBranchBirthplaceAgeWeightFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[3]/div[3]';
        $racerFlyingLateStartTimingFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[4]';
        $racerNationalTop123PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[5]';
        $racerLocalTop123PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[6]';
        $racerAssignedMotorNumberMotorTop23PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[7]';
        $racerAssignedBoatNumberBoatTop23PercentFormat = '%s/div[2]/div[%s]/table/tbody[%s]/tr[1]/td[8]';

        foreach (range(1, 6) as $index) {
            $racerBoatNumberXPath = sprintf($racerBoatNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerNameXPath = sprintf($racerNameFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerNumberClassXPath = sprintf($racerNumberClassFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerBranchBirthplaceAgeWeightXPath = sprintf($racerBranchBirthplaceAgeWeightFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerFlyingLateStartTimingXPath = sprintf($racerFlyingLateStartTimingFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerNationalTop123PercentXPath = sprintf($racerNationalTop123PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerLocalTop123PercentXPath = sprintf($racerLocalTop123PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerAssignedMotorNumberMotorTop23PercentXPath = sprintf($racerAssignedMotorNumberMotorTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $racerAssignedBoatNumberMotorTop23PercentXPath = sprintf($racerAssignedBoatNumberBoatTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);

            $racerBoatNumber = $this->filterXPath($scraper, $racerBoatNumberXPath);
            $racerName = $this->filterXPath($scraper, $racerNameXPath);
            $racerNumberClass = $this->filterXPath($scraper, $racerNumberClassXPath);
            $racerBranchBirthplaceAgeWeight = $this->filterXPath($scraper, $racerBranchBirthplaceAgeWeightXPath);
            $racerFlyingLateStartTiming = $this->filterXPath($scraper, $racerFlyingLateStartTimingXPath);
            $racerNationalTop123Percent = $this->filterXPath($scraper, $racerNationalTop123PercentXPath);
            $racerLocalTop123Percent = $this->filterXPath($scraper, $racerLocalTop123PercentXPath);
            $racerAssignedMotorNumberMotorTop23Percent = $this->filterXPath($scraper, $racerAssignedMotorNumberMotorTop23PercentXPath);
            $racerAssignedBoatNumberBoatTop23Percent = $this->filterXPath($scraper, $racerAssignedBoatNumberMotorTop23PercentXPath);

            $racerBoatNumber = Converter::convertToInt($racerBoatNumber ?? $index);
            $racerName = Converter::convertToName($racerName);

            [$racerNumber, $racerClassNumber] = $this->explodeNumberClass($racerNumberClass);
            [$racerBranchNumber, $racerBirthplaceNumber, $racerAge, $racerWeight] = $this->explodeBranchBirthplaceAgeWeight($racerBranchBirthplaceAgeWeight);
            [$racerFlyingCount, $racerLateCount, $racerAverageStartTiming] = $this->explodeFlyingLateStartTiming($racerFlyingLateStartTiming);
            [$racerNationalTop1Percent, $racerNationalTop2Percent, $racerNationalTop3Percent] = $this->explodeNationalTop123Percent($racerNationalTop123Percent);
            [$racerLocalTop1Percent, $racerLocalTop2Percent, $racerLocalTop3Percent] = $this->explodeLocalTop123Percent($racerLocalTop123Percent);
            [$racerAssignedMotorNumber, $racerAssignedMotorTop2Percent, $racerAssignedMotorTop3Percent] = $this->explodeAssignedMotorNumberMotorTop23Percent($racerAssignedMotorNumberMotorTop23Percent);
            [$racerAssignedBoatNumber, $racerAssignedBoatTop2Percent, $racerAssignedBoatTop3Percent] = $this->explodeAssignedBoatNumberBoatTop23Percent($racerAssignedBoatNumberBoatTop23Percent);

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_name'] = $racerName;
            $response['boats'][$racerBoatNumber]['racer_number'] = $racerNumber;
            $response['boats'][$racerBoatNumber]['racer_class_number'] = $racerClassNumber;
            $response['boats'][$racerBoatNumber]['racer_branch_number'] = $racerBranchNumber;
            $response['boats'][$racerBoatNumber]['racer_birthplace_number'] = $racerBirthplaceNumber;
            $response['boats'][$racerBoatNumber]['racer_age'] = $racerAge;
            $response['boats'][$racerBoatNumber]['racer_weight'] = $racerWeight;
            $response['boats'][$racerBoatNumber]['racer_flying_count'] = $racerFlyingCount;
            $response['boats'][$racerBoatNumber]['racer_late_count'] = $racerLateCount;
            $response['boats'][$racerBoatNumber]['racer_average_start_timing'] = $racerAverageStartTiming;
            $response['boats'][$racerBoatNumber]['racer_national_top_1_percent'] = $racerNationalTop1Percent;
            $response['boats'][$racerBoatNumber]['racer_national_top_2_percent'] = $racerNationalTop2Percent;
            $response['boats'][$racerBoatNumber]['racer_national_top_3_percent'] = $racerNationalTop3Percent;
            $response['boats'][$racerBoatNumber]['racer_local_top_1_percent'] = $racerLocalTop1Percent;
            $response['boats'][$racerBoatNumber]['racer_local_top_2_percent'] = $racerLocalTop2Percent;
            $response['boats'][$racerBoatNumber]['racer_local_top_3_percent'] = $racerLocalTop3Percent;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_number'] = $racerAssignedMotorNumber;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_top_2_percent'] = $racerAssignedMotorTop2Percent;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_top_3_percent'] = $racerAssignedMotorTop3Percent;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_number'] = $racerAssignedBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_top_2_percent'] = $racerAssignedBoatTop2Percent;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_top_3_percent'] = $racerAssignedBoatTop3Percent;
        }

        return $response;
    }

    /**
     * @param  string|null  $subtitleDistance
     * @return array
     */
    private function explodeSubtitleDistance(?string $subtitleDistance = null): array
    {
        if ($subtitleDistance === null) {
            return array_fill(0, 2, null);
        }

        $subtitleDistance = Converter::convertToString($subtitleDistance);

        $values = array_filter(Trimmer::trim(explode(' ', $subtitleDistance)));
        $distance = Converter::convertToInt(array_pop($values));
        $subtitle = Converter::convertToString(implode($values));

        return [$subtitle, $distance];
    }

    /**
     * @param  string|null  $numberClass
     * @return array
     */
    private function explodeNumberClass(?string $numberClass = null): array
    {
        if ($numberClass === null) {
            return array_fill(0, 2, null);
        }

        $numberClass = Converter::convertToString($numberClass);

        [$number, $className] = Trimmer::trim(
            explode('/', $numberClass)
        );

        $number = Converter::convertToInt($number);
        $classNumber = Converter::convertToClassNumber($className);

        return [$number, $classNumber];
    }

    /**
     * @param  string|null  $branchBirthplaceAgeWeight
     * @return array
     */
    private function explodeBranchBirthplaceAgeWeight(?string $branchBirthplaceAgeWeight = null): array
    {
        if ($branchBirthplaceAgeWeight === null) {
            return array_fill(0, 4, null);
        }

        $branchBirthplaceAgeWeight = Converter::convertToString($branchBirthplaceAgeWeight);

        [$branchNameBirthplaceName, $ageWeight] = Trimmer::trim(explode(' ', $branchBirthplaceAgeWeight));
        [$branchName, $birthplaceName] = Trimmer::trim(explode('/', $branchNameBirthplaceName));
        [$age, $weight] = Trimmer::trim(explode('/', $ageWeight));

        $branchNumber = Converter::convertToPrefectureNumber($branchName);
        $birthplaceNumber = Converter::convertToPrefectureNumber($birthplaceName);
        $age = Converter::convertToInt($age);
        $weight = Converter::convertToFloat($weight);

        return [$branchNumber, $birthplaceNumber, $age, $weight];
    }

    /**
     * @param  string|null  $flyingLateStartTiming
     * @return array
     */
    private function explodeFlyingLateStartTiming(?string $flyingLateStartTiming = null): array
    {
        if ($flyingLateStartTiming === null) {
            return array_fill(0, 3, null);
        }

        $flyingLateStartTiming = Converter::convertToString($flyingLateStartTiming);

        [$flyingCount, $lateCount, $averageStartTiming] = Trimmer::trim(
            explode(' ', $flyingLateStartTiming)
        );

        $flyingCount = Converter::parseFlyingCount($flyingCount);
        $lateCount = Converter::parseLateCount($lateCount);
        $averageStartTiming = Converter::parseStartTiming($averageStartTiming);

        return [$flyingCount, $lateCount, $averageStartTiming];
    }

    /**
     * @param  string|null  $nationalTop123Percent
     * @return array
     */
    private function explodeNationalTop123Percent(?string $nationalTop123Percent = null): array
    {
        if ($nationalTop123Percent === null) {
            return array_fill(0, 3, null);
        }

        $nationalTop123Percent = Converter::convertToString($nationalTop123Percent);

        [$nationalTop1Percent, $nationalTop2Percent, $nationalTop3Percent] = Trimmer::trim(
            explode(' ', $nationalTop123Percent)
        );

        $nationalTop1Percent = Converter::convertToFloat($nationalTop1Percent);
        $nationalTop2Percent = Converter::convertToFloat($nationalTop2Percent);
        $nationalTop3Percent = Converter::convertToFloat($nationalTop3Percent);

        return [$nationalTop1Percent, $nationalTop2Percent, $nationalTop3Percent];
    }

    /**
     * @param  string|null  $localTop123Percent
     * @return array
     */
    private function explodeLocalTop123Percent(?string $localTop123Percent = null): array
    {
        if ($localTop123Percent === null) {
            return array_fill(0, 3, null);
        }

        $localTop123Percent = Converter::convertToString($localTop123Percent);

        [$localTop1Percent, $localTop2Percent, $localTop3Percent] = Trimmer::trim(
            explode(' ', $localTop123Percent)
        );

        $localTop1Percent = Converter::convertToFloat($localTop1Percent);
        $localTop2Percent = Converter::convertToFloat($localTop2Percent);
        $localTop3Percent = Converter::convertToFloat($localTop3Percent);

        return [$localTop1Percent, $localTop2Percent, $localTop3Percent];
    }

    /**
     * @param  string|null  $assignedMotorNumberMotorTop23Percent
     * @return array
     */
    private function explodeAssignedMotorNumberMotorTop23Percent(?string $assignedMotorNumberMotorTop23Percent = null): array
    {
        if ($assignedMotorNumberMotorTop23Percent === null) {
            return array_fill(0, 3, null);
        }

        $assignedMotorNumberMotorTop23Percent = Converter::convertToString($assignedMotorNumberMotorTop23Percent);

        [$assignedMotorNumber, $assignedMotorTop2Percent, $assignedMotorTop3Percent] = Trimmer::trim(
            explode(' ', $assignedMotorNumberMotorTop23Percent)
        );

        $assignedMotorNumber = Converter::convertToInt($assignedMotorNumber);
        $assignedMotorTop2Percent = Converter::convertToFloat($assignedMotorTop2Percent);
        $assignedMotorTop3Percent = Converter::convertToFloat($assignedMotorTop3Percent);

        return [$assignedMotorNumber, $assignedMotorTop2Percent, $assignedMotorTop3Percent];
    }

    /**
     * @param  string|null  $assignedBoatNumberBoatTop23Percent
     * @return array
     */
    private function explodeAssignedBoatNumberBoatTop23Percent(?string $assignedBoatNumberBoatTop23Percent = null): array
    {
        if ($assignedBoatNumberBoatTop23Percent === null) {
            return array_fill(0, 3, null);
        }

        $assignedBoatNumberBoatTop23Percent = Converter::convertToString($assignedBoatNumberBoatTop23Percent);

        [$assignedBoatNumber, $assignedBoatTop2Percent, $assignedBoatTop3Percent] = Trimmer::trim(
            explode(' ', $assignedBoatNumberBoatTop23Percent)
        );

        $assignedBoatNumber = Converter::convertToInt($assignedBoatNumber);
        $assignedBoatTop2Percent = Converter::convertToFloat($assignedBoatTop2Percent);
        $assignedBoatTop3Percent = Converter::convertToFloat($assignedBoatTop3Percent);

        return [$assignedBoatNumber, $assignedBoatTop2Percent, $assignedBoatTop3Percent];
    }
}
