<?php

declare(strict_types=1);

namespace BVP\Scraper\Parsers;

use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Enums\Grade;
use BVP\Scraper\Enums\Prefecture;
use BVP\Scraper\Enums\Rank;

/**
 * @author shimomo
 */
final class ProgramParser
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array GRADE_NUMBER_KEYS = [
        'grade_number_source',
        'grade_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array TITLE_KEYS = [
        'title',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array SUBTITLE_AND_DISTANCE_KEYS = [
        'subtitle',
        'distance_source',
        'distance',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NUMBER_AND_RANK_NUMBER_KEYS = [
        'number',
        'rank_number_source',
        'rank_number',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array BRANCH_NUMBER_AND_BIRTHPLACE_NUMBER_AND_AGE_AND_WEIGHT_KEYS = [
        'branch_number_source',
        'branch_number',
        'birthplace_number_source',
        'birthplace_number',
        'age_source',
        'age',
        'weight_source',
        'weight',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array FLYING_COUNT_AND_LATE_COUNT_AND_AVERAGE_START_TIMING_KEYS = [
        'flying_count_source',
        'flying_count',
        'late_count_source',
        'late_count',
        'average_start_timing',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array NATIONAL_WIN_RATE_AND_NATIONAL_TOP_2_3_PERCENT_KEYS = [
        'national_win_rate',
        'national_top_2_percent',
        'national_top_3_percent',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array LOCAL_WIN_RATE_AND_LOCAL_TOP_2_3_PERCENT_KEYS = [
        'local_win_rate',
        'local_top_2_percent',
        'local_top_3_percent',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array MOTOR_NUMBER_AND_MOTOR_TOP_2_3_PERCENT_KEYS = [
        'motor_number',
        'motor_top_2_percent',
        'motor_top_3_percent',
    ];

    /**
     * @var non-empty-list<non-empty-string>
     */
    private const array BOAT_NUMBER_AND_BOAT_TOP_2_3_PERCENT_KEYS = [
        'boat_number',
        'boat_top_2_percent',
        'boat_top_3_percent',
    ];

    /**
     * @param ?string $value
     * @return array{
     *     grade_number_source: ?string,
     *     grade_number: ?int,
     * }
     */
    public static function parseGrade(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::GRADE_NUMBER_KEYS, null);
        }

        $gradeMap = [
            'SGA' => Grade::SG,
            'SGB' => Grade::SG,
            'G1A' => Grade::PG1,
            'G1B' => Grade::G1,
            'G2A' => Grade::G2,
            'G2B' => Grade::G2,
            'G3A' => Grade::G3,
            'G3B' => Grade::G3,
            'IPPAN' => Grade::IPPAN,
        ];

        return array_combine(self::GRADE_NUMBER_KEYS, [
            Converter::toString($value),
            Converter::toInt(($gradeMap[mb_strtoupper($value)] ?? null)?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     title: ?string,
     * }
     */
    public static function parseTitle(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::TITLE_KEYS, null);
        }

        return array_combine(self::TITLE_KEYS, [
            Converter::toString($value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     subtitle: ?string,
     *     distance_source: ?string,
     *     distance: ?int,
     * }
     */
    public static function parseSubtitleAndDistance(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::SUBTITLE_AND_DISTANCE_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $subtitleSource = array_shift($values);
        $distanceSource = array_pop($values);

        return array_combine(self::SUBTITLE_AND_DISTANCE_KEYS, [
            Converter::toString($subtitleSource),
            Converter::toString($distanceSource),
            Converter::toInt($distanceSource),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     number: ?int,
     *     rank_number_source: ?string,
     *     rank_number: ?int,
     * }
     */
    public static function parseNumberAndRankNumber(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NUMBER_AND_RANK_NUMBER_KEYS, null);
        }

        $values = self::splitAndTrim($value, '/');

        $numberSource = array_shift($values);
        $rankNumberSource = array_pop($values);

        return array_combine(self::NUMBER_AND_RANK_NUMBER_KEYS, [
            Converter::toInt($numberSource),
            Converter::toString($rankNumberSource),
            Converter::toInt(Converter::toEnumOrNull(fn() => Rank::fromShortName($rankNumberSource))?->value),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     branch_number_source: ?string,
     *     branch_number: ?int,
     *     birthplace_number_source: ?string,
     *     birthplace_number: ?int,
     *     age_source: ?string,
     *     age: ?int,
     *     weight_source: ?string,
     *     weight: ?float,
     * }
     */
    public static function parseBranchNumberAndBirthplaceNumberAndAgeAndWeight(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::BRANCH_NUMBER_AND_BIRTHPLACE_NUMBER_AND_AGE_AND_WEIGHT_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $branchNumberAndBirthplaceNumber = array_shift($values);
        if ($branchNumberAndBirthplaceNumber === null || $branchNumberAndBirthplaceNumber === '') {
            return array_fill_keys(self::BRANCH_NUMBER_AND_BIRTHPLACE_NUMBER_AND_AGE_AND_WEIGHT_KEYS, null);
        }

        $ageAndWeight = array_pop($values);
        if ($ageAndWeight === null || $ageAndWeight === '') {
            return array_fill_keys(self::BRANCH_NUMBER_AND_BIRTHPLACE_NUMBER_AND_AGE_AND_WEIGHT_KEYS, null);
        }

        $branchNumberAndBirthplaceNumberValues = self::splitAndTrim($branchNumberAndBirthplaceNumber, '/');
        $branchNumberSource = array_shift($branchNumberAndBirthplaceNumberValues);
        $birthplaceNumberSource = array_pop($branchNumberAndBirthplaceNumberValues);

        $ageAndWeightValues = self::splitAndTrim($ageAndWeight, '/');
        $ageSource = array_shift($ageAndWeightValues);
        $weightSource = array_pop($ageAndWeightValues);

        return array_combine(self::BRANCH_NUMBER_AND_BIRTHPLACE_NUMBER_AND_AGE_AND_WEIGHT_KEYS, [
            Converter::toString($branchNumberSource),
            Converter::toInt(Converter::toEnumOrNull(fn() => Prefecture::fromShortName($branchNumberSource))?->value),
            Converter::toString($birthplaceNumberSource),
            Converter::toInt(Converter::toEnumOrNull(fn() => Prefecture::fromShortName($birthplaceNumberSource))?->value),
            Converter::toString($ageSource),
            Converter::toInt($ageSource),
            Converter::toString($weightSource),
            Converter::toFloat($weightSource),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     flying_count_source: ?string,
     *     flying_count: ?int,
     *     late_count_source: ?string,
     *     late_count: ?int,
     *     average_start_timing: ?float,
     * }
     */
    public static function parseFlyingCountAndLateCountAndAverageStartTiming(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::FLYING_COUNT_AND_LATE_COUNT_AND_AVERAGE_START_TIMING_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $flyingCountSource = array_shift($values);
        $lateCountSource = array_shift($values);
        $averageStartTimingSource = array_shift($values);

        return array_combine(self::FLYING_COUNT_AND_LATE_COUNT_AND_AVERAGE_START_TIMING_KEYS, [
            Converter::toString($flyingCountSource),
            Converter::toInt($flyingCountSource === null ? null : ltrim($flyingCountSource, 'F')),
            Converter::toString($lateCountSource),
            Converter::toInt($lateCountSource === null ? null : ltrim($lateCountSource, 'L')),
            Converter::toFloat($averageStartTimingSource),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     national_win_rate: ?float,
     *     national_top_2_percent: ?float,
     *     national_top_3_percent: ?float,
     * }
     */
    public static function parseNationalWinRateAndNationalTop23Percent(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::NATIONAL_WIN_RATE_AND_NATIONAL_TOP_2_3_PERCENT_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $nationalWinRate = array_shift($values);
        $nationalTop2Percent = array_shift($values);
        $nationalTop3Percent = array_shift($values);

        return array_combine(self::NATIONAL_WIN_RATE_AND_NATIONAL_TOP_2_3_PERCENT_KEYS, [
            Converter::toFloat($nationalWinRate),
            Converter::toFloat($nationalTop2Percent),
            Converter::toFloat($nationalTop3Percent),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     local_win_rate: ?float,
     *     local_top_2_percent: ?float,
     *     local_top_3_percent: ?float,
     * }
     */
    public static function parseLocalWinRateAndLocalTop23Percent(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::LOCAL_WIN_RATE_AND_LOCAL_TOP_2_3_PERCENT_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $localWinRate = array_shift($values);
        $localTop2Percent = array_shift($values);
        $localTop3Percent = array_shift($values);

        return array_combine(self::LOCAL_WIN_RATE_AND_LOCAL_TOP_2_3_PERCENT_KEYS, [
            Converter::toFloat($localWinRate),
            Converter::toFloat($localTop2Percent),
            Converter::toFloat($localTop3Percent),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     motor_number: ?int,
     *     motor_top_2_percent: ?float,
     *     motor_top_3_percent: ?float,
     * }
     */
    public static function parseMotorNumberAndMotorTop23Percent(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::MOTOR_NUMBER_AND_MOTOR_TOP_2_3_PERCENT_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $motorNumber = array_shift($values);
        $motorTop2Percent = array_shift($values);
        $motorTop3Percent = array_shift($values);

        return array_combine(self::MOTOR_NUMBER_AND_MOTOR_TOP_2_3_PERCENT_KEYS, [
            Converter::toInt($motorNumber),
            Converter::toFloat($motorTop2Percent),
            Converter::toFloat($motorTop3Percent),
        ]);
    }

    /**
     * @param ?string $value
     * @return array{
     *     boat_number: ?int,
     *     boat_top_2_percent: ?float,
     *     boat_top_3_percent: ?float,
     * }
     */
    public static function parseBoatNumberAndBoatTop23Percent(?string $value): array
    {
        if ($value === null || $value === '') {
            return array_fill_keys(self::BOAT_NUMBER_AND_BOAT_TOP_2_3_PERCENT_KEYS, null);
        }

        $values = self::splitAndTrim($value, ' ');

        $boatNumber = array_shift($values);
        $boatTop2Percent = array_shift($values);
        $boatTop3Percent = array_shift($values);

        return array_combine(self::BOAT_NUMBER_AND_BOAT_TOP_2_3_PERCENT_KEYS, [
            Converter::toInt($boatNumber),
            Converter::toFloat($boatTop2Percent),
            Converter::toFloat($boatTop3Percent),
        ]);
    }

    /**
     * @param non-empty-string $value
     * @param non-empty-string $delimiter
     * @return list<?string>
     */
    private static function splitAndTrim(string $value, string $delimiter = '/'): array
    {
        return array_map(fn($v) => Converter::trim($v), explode($delimiter, $value));
    }
}
