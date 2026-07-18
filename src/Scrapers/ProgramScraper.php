<?php

declare(strict_types=1);

namespace BVP\Scraper\Scrapers;

use BVP\Scraper\Contracts\Scraper;
use BVP\Scraper\Converters\Converter;
use BVP\Scraper\Filters\Filter;
use BVP\Scraper\Filters\GradeFilter;
use BVP\Scraper\Parsers\Parser;
use BVP\Scraper\Parsers\ProgramParser;
use Carbon\CarbonInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author shimomo
 */
final class ProgramScraper extends BaseScraper implements Scraper
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
        $scraperFormat = '%s/owpc/pc/race/racelist?hd=%s&jcd=%02d&rno=%d';
        $scraperUrl = sprintf($scraperFormat, $this->baseUrl, $date->format('Ymd'), $stadiumNumber, $raceNumber);
        $scraper = $this->requestAndClearCookies('GET', $scraperUrl);

        $levelFormat = '%s/div[2]/div[3]/ul/li';
        $levelXPath = sprintf($levelFormat, $this->baseXPath);

        $this->baseLevel = 0;
        if (Filter::byXPath($scraper, $levelXPath) !== null) {
            $this->baseLevel = 1;
        }

        $closeTimeFormat = '%s/div[2]/div[2]/table/tbody/tr[1]/td[%s]';
        $closeTimeXPath = sprintf($closeTimeFormat, $this->baseXPath, $raceNumber + 1);
        $closeTimeSource = Filter::byXPath($scraper, $closeTimeXPath);

        $closedAt = null;
        if ($closeTimeSource !== null) {
            $closedAt = $date->setTimeFromTimeString($closeTimeSource)->format('Y-m-d H:i:s');
        }

        $gradeFormat = '%s/div[1]/div/div[2]';
        $gradeXPath = sprintf($gradeFormat, $this->baseXPath);
        $gradeSource = GradeFilter::byXPath($scraper, $gradeXPath);
        $grade = ProgramParser::parseGrade($gradeSource);

        $titleFormat = '%s/div[1]/div/div[2]/h2';
        $titleXPath = sprintf($titleFormat, $this->baseXPath);
        $titleSource = Filter::byXPath($scraper, $titleXPath);
        $title = ProgramParser::parseTitle($titleSource);

        $subtitleAndDistanceFormat = '%s/div[2]/div[%d]/h3';
        $subtitleAndDistanceXPath = sprintf($subtitleAndDistanceFormat, $this->baseXPath, $this->baseLevel + 3);
        $subtitleAndDistanceSource = Filter::byXPath($scraper, $subtitleAndDistanceXPath);
        $subtitleAndDistance = ProgramParser::parseSubtitleAndDistance($subtitleAndDistanceSource);

        $response = [];

        $response['date'] = $date->format('Y-m-d');
        $response['stadium_number'] = $stadiumNumber;
        $response['race_number'] = $raceNumber;
        $response['closed_at'] = $closedAt;

        $response += $grade;
        $response += $title;
        $response += $subtitleAndDistance;

        $response += $this->resolveDayNumber($scraper);
        $response += $this->scrapeRacers($scraper);

        return $response;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array{
     *     day_number_source: ?string,
     *     day_number: ?int,
     * }
     */
    private function resolveDayNumber(Crawler $scraper): array
    {
        $dayNumberSourceFormat = '%s/div[2]/div[1]/ul/li[%s]/span/span';

        foreach (range(1, 14) as $index) {
            $dayNumberSourceXPath = sprintf($dayNumberSourceFormat, $this->baseXPath, $index);
            $dayNumberSource = Filter::byXPath($scraper, $dayNumberSourceXPath);

            if ($dayNumberSource !== null) {
                $dayNumber = match ($dayNumberSource) {
                    '初日' => 1,
                    '最終日' => $this->resolveLastDayNumber($scraper, $index),
                    default => preg_match('/[\p{Nd}]+/u', $dayNumberSource, $matches)
                        ? Converter::toDayNumber($matches[0])
                        : null,
                };

                return ['day_number_source' => $dayNumberSource, 'day_number' => $dayNumber];
            }
        }

        return ['day_number_source' => null, 'day_number' => null];
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @param int $index
     * @return ?int
     */
    private function resolveLastDayNumber(Crawler $scraper, int $index): ?int
    {
        $previousDayNumberSourceFormat = '%s/div[2]/div[1]/ul/li[%s]/a/span';

        foreach (range(1, 14) as $previousIndex) {
            $previousDayNumberSourceXPath = sprintf(
                $previousDayNumberSourceFormat,
                $this->baseXPath,
                $index - $previousIndex,
            );
            $previousDayNumberSource = Filter::byXPath($scraper, $previousDayNumberSourceXPath);

            if ($previousDayNumberSource === null) {
                continue;
            }

            if (preg_match('/[\p{Nd}]+/u', $previousDayNumberSource, $matches)) {
                if (is_int($previousDayNumber = Converter::toDayNumber($matches[0]))) {
                    return $previousDayNumber + 1;
                }
            }
        }

        return null;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $scraper
     * @return array<non-empty-string, mixed>
     */
    private function scrapeRacers(Crawler $scraper): array
    {
        $response = ['racers' => []];

        foreach (range(1, 6) as $index) {
            $entryNumberFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[1]';
            $entryNumberXPath = sprintf($entryNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $entryNumber = Parser::parseEntryNumber(Filter::byXPath($scraper, $entryNumberXPath));

            $nameFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[3]/div[2]/a';
            $nameXPath = sprintf($nameFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $name = Parser::parseName(Filter::byXPath($scraper, $nameXPath));

            $numberAndRankNumberFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[3]/div[1]';
            $numberAndRankNumberXPath = sprintf($numberAndRankNumberFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $numberAndRankNumber = ProgramParser::parseNumberAndRankNumber(
                Filter::byXPath($scraper, $numberAndRankNumberXPath)
            );

            $branchBirthplaceAgeWeightFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[3]/div[3]';
            $branchBirthplaceAgeWeightXPath = sprintf($branchBirthplaceAgeWeightFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $branchBirthplaceAgeWeight = ProgramParser::parseBranchNumberAndBirthplaceNumberAndAgeAndWeight(
                Filter::byXPath($scraper, $branchBirthplaceAgeWeightXPath)
            );

            $flyingLateStartTimingFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[4]';
            $flyingLateStartTimingXPath = sprintf($flyingLateStartTimingFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $flyingLateStartTiming = ProgramParser::parseFlyingCountAndLateCountAndAverageStartTiming(
                Filter::byXPath($scraper, $flyingLateStartTimingXPath)
            );

            $nationalWinRateAndTop23PercentFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[5]';
            $nationalWinRateAndTop23PercentXPath = sprintf($nationalWinRateAndTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $nationalWinRateAndTop23Percent = ProgramParser::parseNationalWinRateAndNationalTop23Percent(
                Filter::byXPath($scraper, $nationalWinRateAndTop23PercentXPath)
            );

            $localWinRateAndTop23PercentFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[6]';
            $localWinRateAndTop23PercentXPath = sprintf($localWinRateAndTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $localWinRateAndTop23Percent = ProgramParser::parseLocalWinRateAndLocalTop23Percent(
                Filter::byXPath($scraper, $localWinRateAndTop23PercentXPath)
            );

            $motorNumberAndTop23PercentFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[7]';
            $motorNumberAndTop23PercentXPath = sprintf($motorNumberAndTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $motorNumberAndTop23Percent = ProgramParser::parseMotorNumberAndMotorTop23Percent(
                Filter::byXPath($scraper, $motorNumberAndTop23PercentXPath)
            );

            $boatNumberAndTop23PercentFormat = '%s/div[2]/div[%d]/table/tbody[%s]/tr[1]/td[8]';
            $boatNumberAndTop23PercentXPath = sprintf($boatNumberAndTop23PercentFormat, $this->baseXPath, $this->baseLevel + 5, $index);
            $boatNumberAndTop23Percent = ProgramParser::parseBoatNumberAndBoatTop23Percent(
                Filter::byXPath($scraper, $boatNumberAndTop23PercentXPath)
            );

            if (!isset($entryNumber['entry_number'])) {
                $entryNumber['entry_number'] = $index;
            }

            $entryNumberKey = $entryNumber['entry_number'] ?? $index;

            if (!in_array($entryNumberKey, range(1, 6), true)) {
                continue;
            }

            $response['racers'][$entryNumberKey] ??= [];
            $response['racers'][$entryNumberKey] += $entryNumber;
            $response['racers'][$entryNumberKey] += $name;
            $response['racers'][$entryNumberKey] += $numberAndRankNumber;
            $response['racers'][$entryNumberKey] += $branchBirthplaceAgeWeight;
            $response['racers'][$entryNumberKey] += $flyingLateStartTiming;
            $response['racers'][$entryNumberKey] += $nationalWinRateAndTop23Percent;
            $response['racers'][$entryNumberKey] += $localWinRateAndTop23Percent;
            $response['racers'][$entryNumberKey] += $motorNumberAndTop23Percent;
            $response['racers'][$entryNumberKey] += $boatNumberAndTop23Percent;
        }

        ksort($response['racers'], SORT_NUMERIC);

        return $response;
    }
}
