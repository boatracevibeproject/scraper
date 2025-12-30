<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Converter\Converter;
use BVP\Trimmer\Trimmer;
use Carbon\CarbonImmutable as Carbon;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class ProgramScraper extends BaseScraper implements ProgramScraperInterface
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

        $scraperFormat = '%s/owpc/pc/race/racelist?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $number);
        $scraper = $this->httpBrowser->request('GET', $scraperUrl);
        sleep($this->seconds);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if ($this->filterXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $dayLabelFormat = '%s/div[2]/div[1]/ul/li[%s]/span/span';
        $gradeFormat = '%s/div[1]/div/div[2]';
        $titleFormat = '%s/div[1]/div/div[2]/h2';
        $subtitleDistanceFormat = '%s/div[2]/div[%s]/h3';
        $deadlineFormat = '%s/div[2]/div[2]/table/tbody/tr[1]/td[%s]';

        foreach (range(1, 14) as $index) {
            $dayLabelXPath = sprintf($dayLabelFormat, $this->baseXPath, $index);
            $dayLabel = $this->filterXPath($scraper, $dayLabelXPath);
            if ($dayLabel !== null) {
                break;
            }
        }

        $gradeXPath = sprintf($gradeFormat, $this->baseXPath);
        $titleXPath = sprintf($titleFormat, $this->baseXPath);
        $subtitleDistanceXPath = sprintf($subtitleDistanceFormat, $this->baseXPath, $this->baseLevel + 3);
        $deadlineXPath = sprintf($deadlineFormat, $this->baseXPath, $number + 1);

        $gradeLabel = $this->filterXPathForGradeLabel($scraper, $gradeXPath);
        $gradeNumber = $this->filterXPathForGradeNumber($scraper, $gradeXPath);
        $title = $this->filterXPath($scraper, $titleXPath);
        $subtitleDistance = $this->filterXPath($scraper, $subtitleDistanceXPath);
        $deadline = $this->filterXPath($scraper, $deadlineXPath);

        $raceClosedAt = null;
        if ($deadline !== null) {
            $raceClosedAt = $date->setTimeFromTimeString($deadline)->format('Y-m-d H:i:s');
        }

        $subtitleDistanceValues = $this->explodeSubtitleDistance($subtitleDistance);

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['number'] = $number;
        $response['closed_at'] = $raceClosedAt;
        $response['day_label'] = $dayLabel;
        $response['grade_label'] = $gradeLabel;
        $response['grade_number'] = $gradeNumber;
        $response['title'] = $title;
        $response['subtitle'] = $subtitleDistanceValues['subtitle'] ?? null;
        $response['distance'] = $subtitleDistanceValues['distance'] ?? null;

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

            $racerBoatNumber = Converter::convertToInt($racerBoatNumber ?? $index) ?? $index;
            $racerName = Converter::convertToName($racerName);

            $racerNumberClassValues = $this->explodeNumberClass($racerNumberClass);
            $racerBranchBirthplaceAgeWeightValues = $this->explodeBranchBirthplaceAgeWeight($racerBranchBirthplaceAgeWeight);
            $racerFlyingLateStartTimingValues = $this->explodeFlyingLateStartTiming($racerFlyingLateStartTiming);
            $racerNationalTop123Percent = $this->explodeNationalTop123Percent($racerNationalTop123Percent);
            $racerLocalTop123Percent = $this->explodeLocalTop123Percent($racerLocalTop123Percent);
            $racerAssignedMotorNumberMotorTop23Percent = $this->explodeAssignedMotorNumberMotorTop23Percent($racerAssignedMotorNumberMotorTop23Percent);
            $racerAssignedBoatNumberBoatTop23Percent = $this->explodeAssignedBoatNumberBoatTop23Percent($racerAssignedBoatNumberBoatTop23Percent);

            $response['boats'][$racerBoatNumber]['racer_boat_number'] = $racerBoatNumber;
            $response['boats'][$racerBoatNumber]['racer_name'] = $racerName;
            $response['boats'][$racerBoatNumber]['racer_number'] = $racerNumberClassValues['number'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_class_number'] = $racerNumberClassValues['classNumber'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_branch_number'] = $racerBranchBirthplaceAgeWeightValues['branchNumber'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_birthplace_number'] = $racerBranchBirthplaceAgeWeightValues['birthplaceNumber'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_age'] = $racerBranchBirthplaceAgeWeightValues['age'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_weight'] = $racerBranchBirthplaceAgeWeightValues['weight'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_flying_count'] = $racerFlyingLateStartTimingValues['flyingCount'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_late_count'] = $racerFlyingLateStartTimingValues['lateCount'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_average_start_timing'] = $racerFlyingLateStartTimingValues['averageStartTiming'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_national_top_1_percent'] = $racerNationalTop123Percent['nationalTop1Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_national_top_2_percent'] = $racerNationalTop123Percent['nationalTop2Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_national_top_3_percent'] = $racerNationalTop123Percent['nationalTop3Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_local_top_1_percent'] = $racerLocalTop123Percent['localTop1Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_local_top_2_percent'] = $racerLocalTop123Percent['localTop2Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_local_top_3_percent'] = $racerLocalTop123Percent['localTop3Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_number'] = $racerAssignedMotorNumberMotorTop23Percent['assignedMotorNumber'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_top_2_percent'] = $racerAssignedMotorNumberMotorTop23Percent['assignedMotorTop2Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_motor_top_3_percent'] = $racerAssignedMotorNumberMotorTop23Percent['assignedMotorTop3Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_number'] = $racerAssignedBoatNumberBoatTop23Percent['assignedBoatNumber'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_top_2_percent'] = $racerAssignedBoatNumberBoatTop23Percent['assignedBoatTop2Percent'] ?? null;
            $response['boats'][$racerBoatNumber]['racer_assigned_boat_top_3_percent'] = $racerAssignedBoatNumberBoatTop23Percent['assignedBoatTop3Percent'] ?? null;
        }

        return $response;
    }

    /**
     * @psalm-param ?string $subtitleDistance
     * @psalm-return array{
     *     subtitle: ?string,
     *     distance: ?int,
     * }
     *
     * @param ?string $subtitleDistance
     * @return array
     */
    private function explodeSubtitleDistance(?string $subtitleDistance = null): array
    {
        $subtitleDistanceKeys = [
            'subtitle',
            'distance',
        ];

        if ($subtitleDistance === null) {
            return array_fill_keys($subtitleDistanceKeys, null);
        }

        $subtitleDistance = Converter::convertToString($subtitleDistance);
        if ($subtitleDistance === null || $subtitleDistance === '') {
            return array_fill_keys($subtitleDistanceKeys, null);
        }

        $subtitleDistanceValues = array_filter($this->splitAndTrim($subtitleDistance, ' '));

        $distance = Converter::convertToInt(array_pop($subtitleDistanceValues));
        $subtitle = Converter::convertToString(implode($subtitleDistanceValues));

        return compact('subtitle', 'distance');
    }

    /**
     * @psalm-param ?string $numberClass
     * @psalm-return array{
     *     number: ?int,
     *     classNumber: ?int,
     * }
     *
     * @param ?string $numberClass
     * @return array
     */
    private function explodeNumberClass(?string $numberClass = null): array
    {
        $numberClassKeys = [
            'number',
            'classNumber',
        ];

        if ($numberClass === null) {
            return array_fill_keys($numberClassKeys, null);
        }

        $numberClass = Converter::convertToString($numberClass);
        if ($numberClass === null || $numberClass === '') {
            return array_fill_keys($numberClassKeys, null);
        }

        $numberClassValues = $this->splitAndTrim($numberClass, '/');

        $number = Converter::convertToInt($numberClassValues[0] ?? null);
        $classNumber = Converter::convertToClassNumber($numberClassValues[1] ?? null);

        return compact('number', 'classNumber');
    }

    /**
     * @psalm-param ?string $branchBirthplaceAgeWeight
     * @psalm-return array{
     *     branchNumber: ?int,
     *     birthplaceNumber: ?int,
     *     age: ?int,
     *     weight: ?float,
     * }
     *
     * @param ?string $branchBirthplaceAgeWeight
     * @return array
     */
    private function explodeBranchBirthplaceAgeWeight(?string $branchBirthplaceAgeWeight = null): array
    {
        $branchBirthplaceAgeWeightKeys = [
            'branchNumber',
            'birthplaceNumber',
            'age',
            'weight',
        ];

        if ($branchBirthplaceAgeWeight === null) {
            return array_fill_keys($branchBirthplaceAgeWeightKeys, null);
        }

        $branchBirthplaceAgeWeight = Converter::convertToString($branchBirthplaceAgeWeight);
        if ($branchBirthplaceAgeWeight === null || $branchBirthplaceAgeWeight === '') {
            return array_fill_keys($branchBirthplaceAgeWeightKeys, null);
        }

        $branchBirthplaceAgeWeightValues = $this->splitAndTrim($branchBirthplaceAgeWeight, ' ');

        $branchBirthplace = $branchBirthplaceAgeWeightValues[0] ?? null;
        if ($branchBirthplace === null || $branchBirthplace === '') {
            return array_fill_keys($branchBirthplaceAgeWeightKeys, null);
        }

        $ageWeight = $branchBirthplaceAgeWeightValues[1] ?? null;
        if ($ageWeight === null || $ageWeight === '') {
            return array_fill_keys($branchBirthplaceAgeWeightKeys, null);
        }

        $branchBirthplaceValues = $this->splitAndTrim($branchBirthplace, '/');
        $ageWeightValues = $this->splitAndTrim($ageWeight, '/');

        $branchNumber = Converter::convertToPrefectureNumber($branchBirthplaceValues[0] ?? null);
        $birthplaceNumber = Converter::convertToPrefectureNumber($branchBirthplaceValues[1] ?? null);
        $age = Converter::convertToInt($ageWeightValues[0] ?? null);
        $weight = Converter::convertToFloat($ageWeightValues[1] ?? null);

        return compact('branchNumber', 'birthplaceNumber', 'age', 'weight');
    }

    /**
     * @psalm-param ?string $flyingLateStartTiming
     * @psalm-return array{
     *     flyingCount: ?int,
     *     lateCount: ?int,
     *     averageStartTiming: ?float,
     * }
     *
     * @param ?string $flyingLateStartTiming
     * @return array
     */
    private function explodeFlyingLateStartTiming(?string $flyingLateStartTiming = null): array
    {
        $flyingLateStartTimingKeys = [
            'flyingCount',
            'lateCount',
            'averageStartTiming',
        ];

        if ($flyingLateStartTiming === null) {
            return array_fill_keys($flyingLateStartTimingKeys, null);
        }

        $flyingLateStartTiming = Converter::convertToString($flyingLateStartTiming);
        if ($flyingLateStartTiming === null || $flyingLateStartTiming === '') {
            return array_fill_keys($flyingLateStartTimingKeys, null);
        }

        $flyingLateStartTimingValues = $this->splitAndTrim($flyingLateStartTiming, ' ');

        $flyingCount = Converter::parseFlyingCount($flyingLateStartTimingValues[0] ?? null);
        $lateCount = Converter::parseLateCount($flyingLateStartTimingValues[1] ?? null);
        $averageStartTiming = Converter::parseStartTiming($flyingLateStartTimingValues[2] ?? null);

        return compact('flyingCount', 'lateCount', 'averageStartTiming');
    }

    /**
     * @psalm-param ?string $nationalTop123Percent
     * @psalm-return array{
     *     nationalTop1Percent: ?float,
     *     nationalTop2Percent: ?float,
     *     nationalTop3Percent: ?float,
     * }
     *
     * @param ?string $nationalTop123Percent
     * @return array
     */
    private function explodeNationalTop123Percent(?string $nationalTop123Percent = null): array
    {
        $nationalTop123PercentKeys = [
            'nationalTop1Percent',
            'nationalTop2Percent',
            'nationalTop3Percent',
        ];

        if ($nationalTop123Percent === null) {
            return array_fill_keys($nationalTop123PercentKeys, null);
        }

        $nationalTop123Percent = Converter::convertToString($nationalTop123Percent);
        if ($nationalTop123Percent === null || $nationalTop123Percent === '') {
            return array_fill_keys($nationalTop123PercentKeys, null);
        }

        $nationalTopValues = $this->splitAndTrim($nationalTop123Percent, ' ');

        $nationalTop1Percent = Converter::convertToFloat($nationalTopValues[0] ?? null);
        $nationalTop2Percent = Converter::convertToFloat($nationalTopValues[1] ?? null);
        $nationalTop3Percent = Converter::convertToFloat($nationalTopValues[2] ?? null);

        return compact('nationalTop1Percent', 'nationalTop2Percent', 'nationalTop3Percent');
    }

    /**
     * @psalm-param ?string $localTop123Percent
     * @psalm-return array{
     *     localTop1Percent: ?float,
     *     localTop2Percent: ?float,
     *     localTop3Percent: ?float,
     * }
     *
     * @param ?string $localTop123Percent
     * @return array
     */
    private function explodeLocalTop123Percent(?string $localTop123Percent = null): array
    {
        $localTop123PercentKeys = [
            'localTop1Percent',
            'localTop2Percent',
            'localTop3Percent',
        ];

        if ($localTop123Percent === null) {
            return array_fill_keys($localTop123PercentKeys, null);
        }

        $localTop123Percent = Converter::convertToString($localTop123Percent);
        if ($localTop123Percent === null || $localTop123Percent === '') {
            return array_fill_keys($localTop123PercentKeys, null);
        }

        $localTopValues = $this->splitAndTrim($localTop123Percent, ' ');

        $localTop1Percent = Converter::convertToFloat($localTopValues[0] ?? null);
        $localTop2Percent = Converter::convertToFloat($localTopValues[1] ?? null);
        $localTop3Percent = Converter::convertToFloat($localTopValues[2] ?? null);

        return compact('localTop1Percent', 'localTop2Percent', 'localTop3Percent');
    }

    /**
     * @psalm-param ?string $assignedMotorNumberMotorTop23Percent
     * @psalm-return array{
     *     assignedMotorNumber: ?int,
     *     assignedMotorTop2Percent: ?float,
     *     assignedMotorTop3Percent: ?float,
     * }
     *
     * @param ?string $assignedMotorNumberMotorTop23Percent
     * @return array
     */
    private function explodeAssignedMotorNumberMotorTop23Percent(?string $assignedMotorNumberMotorTop23Percent = null): array
    {
        $assignedMotorNumberMotorTop23PercentKeys = [
            'assignedMotorNumber',
            'assignedMotorTop2Percent',
            'assignedMotorTop3Percent',
        ];

        if ($assignedMotorNumberMotorTop23Percent === null) {
            return array_fill_keys($assignedMotorNumberMotorTop23PercentKeys, null);
        }

        $assignedMotorNumberMotorTop23Percent = Converter::convertToString($assignedMotorNumberMotorTop23Percent);
        if ($assignedMotorNumberMotorTop23Percent === null || $assignedMotorNumberMotorTop23Percent === '') {
            return array_fill_keys($assignedMotorNumberMotorTop23PercentKeys, null);
        }

        $assignedMotorValues = $this->splitAndTrim($assignedMotorNumberMotorTop23Percent, ' ');

        $assignedMotorNumber = Converter::convertToInt($assignedMotorValues[0] ?? null);
        $assignedMotorTop2Percent = Converter::convertToFloat($assignedMotorValues[1] ?? null);
        $assignedMotorTop3Percent = Converter::convertToFloat($assignedMotorValues[2] ?? null);

        return compact('assignedMotorNumber', 'assignedMotorTop2Percent', 'assignedMotorTop3Percent');
    }

    /**
     * @psalm-param ?string $assignedBoatNumberBoatTop23Percent
     * @psalm-return array{
     *     assignedBoatNumber: ?int,
     *     assignedBoatTop2Percent: ?float,
     *     assignedBoatTop3Percent: ?float,
     * }
     *
     * @param ?string $assignedBoatNumberBoatTop23Percent
     * @return array
     */
    private function explodeAssignedBoatNumberBoatTop23Percent(?string $assignedBoatNumberBoatTop23Percent = null): array
    {
        $assignedBoatNumberBoatTop23PercentKeys = [
            'assignedBoatNumber',
            'assignedBoatTop2Percent',
            'assignedBoatTop3Percent',
        ];

        if ($assignedBoatNumberBoatTop23Percent === null) {
            return array_fill_keys($assignedBoatNumberBoatTop23PercentKeys, null);
        }

        $assignedBoatNumberBoatTop23Percent = Converter::convertToString($assignedBoatNumberBoatTop23Percent);
        if ($assignedBoatNumberBoatTop23Percent === null || $assignedBoatNumberBoatTop23Percent === '') {
            return array_fill_keys($assignedBoatNumberBoatTop23PercentKeys, null);
        }

        $assignedBoatValues = $this->splitAndTrim($assignedBoatNumberBoatTop23Percent, ' ');

        $assignedBoatNumber = Converter::convertToInt($assignedBoatValues[0] ?? null);
        $assignedBoatTop2Percent = Converter::convertToFloat($assignedBoatValues[1] ?? null);
        $assignedBoatTop3Percent = Converter::convertToFloat($assignedBoatValues[2] ?? null);

        return compact('assignedBoatNumber', 'assignedBoatTop2Percent', 'assignedBoatTop3Percent');
    }

    /**
     * @psalm-param non-empty-string $value
     * @psalm-param non-empty-string $delimiter
     * @psalm-return list<?string>
     *
     * @param string $value
     * @param string $delimiter
     * @return array
     */
    protected function splitAndTrim(string $value, string $delimiter = '/'): array
    {
        return array_map(fn($value) => Trimmer::trim($value), explode($delimiter, $value));
    }
}
