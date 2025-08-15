# BVP Boatrace Scraper

[![tests](https://github.com/shimomo/bvp-boatrace-scraper/actions/workflows/tests.yml/badge.svg)](https://github.com/shimomo/bvp-boatrace-scraper/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/shimomo/bvp-boatrace-scraper/graph/badge.svg?token=1J6TVAC5FR)](https://codecov.io/gh/shimomo/bvp-boatrace-scraper)
[![php](https://poser.pugx.org/bvp/boatrace-scraper/require/php)](https://packagist.org/packages/bvp/boatrace-scraper)
[![stable](https://poser.pugx.org/bvp/boatrace-scraper/v/stable)](https://packagist.org/packages/bvp/boatrace-scraper)
[![unstable](https://poser.pugx.org/bvp/boatrace-scraper/v/unstable)](https://packagist.org/packages/bvp/boatrace-scraper#5.x-dev)
[![license](https://poser.pugx.org/bvp/boatrace-scraper/license)](https://packagist.org/packages/bvp/boatrace-scraper)

## Installation
```bash
composer require bvp/boatrace-scraper
```

## Usage
```php
<?php

require __DIR__ . '/vendor/autoload.php';

use BVP\BoatraceScraper\Scraper;

// ------------------------------
// 基本的な使い方
// ------------------------------

// scrapeStadiums($raceDate)
// scrapePrograms($raceDate, $raceStadiumNumber = null, $raceNumber = null)
// scrapePreviews($raceDate, $raceStadiumNumber = null, $raceNumber = null)
// scrapeOdds($raceDate, $raceStadiumNumber = null, $raceNumber = null)
// scrapeResults($raceDate, $raceStadiumNumber = null, $raceNumber = null)
//
// $raceDate          : レース開催日
//   - 文字列の場合: Carbon::parse() が解釈できる任意の形式（例: '2025-01-01', '2025/01/01', 'yesterday'）
//   - Carbonインスタンスも可
// $raceStadiumNumber : レース開催場番号 (1〜24)（省略時は全開催場）
// $raceNumber        : レース番号 (1〜12)（省略時は全レース）

// 例: ボートレースの公式サイトから2025年01月01日の開催場を取得
$stadiums = Scraper::scrapeStadiums('2025-01-01');

// 取得結果を表示
print_r($stadiums);

/*
Array
(
    [1] => 桐生
    [6] => 浜名湖
    [8] => 常滑
    [9] => 津
    [14] => 鳴門
    [15] => 丸亀
    [16] => 児島
    [17] => 宮島
    [18] => 徳山
    [19] => 下関
    [20] => 若松
    [21] => 芦屋
    [23] => 唐津
    [24] => 大村
)
*/

// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの出走表を取得
$programs = Scraper::scrapePrograms('2025-01-01', 24, 1);

// 取得結果を表示
print_r($programs);

/*
Array
(
    [24] => Array
        (
            [1] => Array
                (
                    [race_date] => 2025-01-01
                    [race_stadium_number] => 24
                    [race_number] => 1
                    [race_closed_at] => 2025-01-01 17:41:00
                    [race_grade_number] => 5
                    [race_title] => ミッドナイトボートレースin大村7th 新春特選R
                    [race_subtitle] => 予選
                    [race_distance] => 1800
                    [boats] => Array
                        (
                            [1] => Array
                                (
                                    [racer_boat_number] => 1
                                    [racer_name] => 中嶋 誠一郎
                                    [racer_number] => 3527
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 53
                                    [racer_weight] => 52.3
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.17
                                    [racer_national_top_1_percent] => 5.43
                                    [racer_national_top_2_percent] => 32.65
                                    [racer_national_top_3_percent] => 53.06
                                    [racer_local_top_1_percent] => 6.09
                                    [racer_local_top_2_percent] => 40
                                    [racer_local_top_3_percent] => 60.87
                                    [racer_assigned_motor_number] => 57
                                    [racer_assigned_motor_top_2_percent] => 29.2
                                    [racer_assigned_motor_top_3_percent] => 45.13
                                    [racer_assigned_boat_number] => 59
                                    [racer_assigned_boat_top_2_percent] => 36.21
                                    [racer_assigned_boat_top_3_percent] => 53.45
                                )

                            [2] => Array
                                (
                                    [racer_boat_number] => 2
                                    [racer_name] => 谷川 将太
                                    [racer_number] => 4603
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 36
                                    [racer_weight] => 52.4
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.14
                                    [racer_national_top_1_percent] => 4.98
                                    [racer_national_top_2_percent] => 31.4
                                    [racer_national_top_3_percent] => 47.67
                                    [racer_local_top_1_percent] => 5.19
                                    [racer_local_top_2_percent] => 31.82
                                    [racer_local_top_3_percent] => 50.91
                                    [racer_assigned_motor_number] => 29
                                    [racer_assigned_motor_top_2_percent] => 39.2
                                    [racer_assigned_motor_top_3_percent] => 56.8
                                    [racer_assigned_boat_number] => 53
                                    [racer_assigned_boat_top_2_percent] => 39.32
                                    [racer_assigned_boat_top_3_percent] => 58.12
                                )

                            [3] => Array
                                (
                                    [racer_boat_number] => 3
                                    [racer_name] => 上之 晃弘
                                    [racer_number] => 3843
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 48
                                    [racer_weight] => 53.3
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.16
                                    [racer_national_top_1_percent] => 4.74
                                    [racer_national_top_2_percent] => 28.16
                                    [racer_national_top_3_percent] => 41.75
                                    [racer_local_top_1_percent] => 5.11
                                    [racer_local_top_2_percent] => 28.33
                                    [racer_local_top_3_percent] => 50
                                    [racer_assigned_motor_number] => 43
                                    [racer_assigned_motor_top_2_percent] => 28.35
                                    [racer_assigned_motor_top_3_percent] => 44.09
                                    [racer_assigned_boat_number] => 48
                                    [racer_assigned_boat_top_2_percent] => 36.13
                                    [racer_assigned_boat_top_3_percent] => 47.06
                                )

                            [4] => Array
                                (
                                    [racer_boat_number] => 4
                                    [racer_name] => 眞鳥 康太
                                    [racer_number] => 5048
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 30
                                    [racer_weight] => 52.8
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.17
                                    [racer_national_top_1_percent] => 4.32
                                    [racer_national_top_2_percent] => 24.19
                                    [racer_national_top_3_percent] => 40.32
                                    [racer_local_top_1_percent] => 4.82
                                    [racer_local_top_2_percent] => 27.55
                                    [racer_local_top_3_percent] => 46.94
                                    [racer_assigned_motor_number] => 40
                                    [racer_assigned_motor_top_2_percent] => 24.55
                                    [racer_assigned_motor_top_3_percent] => 34.55
                                    [racer_assigned_boat_number] => 51
                                    [racer_assigned_boat_top_2_percent] => 35.29
                                    [racer_assigned_boat_top_3_percent] => 62.18
                                )

                            [5] => Array
                                (
                                    [racer_boat_number] => 5
                                    [racer_name] => 森 陽多
                                    [racer_number] => 5335
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 20
                                    [racer_weight] => 47
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.18
                                    [racer_national_top_1_percent] => 2.38
                                    [racer_national_top_2_percent] => 6.67
                                    [racer_national_top_3_percent] => 14.44
                                    [racer_local_top_1_percent] => 2.48
                                    [racer_local_top_2_percent] => 10.87
                                    [racer_local_top_3_percent] => 17.39
                                    [racer_assigned_motor_number] => 65
                                    [racer_assigned_motor_top_2_percent] => 30.58
                                    [racer_assigned_motor_top_3_percent] => 42.98
                                    [racer_assigned_boat_number] => 65
                                    [racer_assigned_boat_top_2_percent] => 37.7
                                    [racer_assigned_boat_top_3_percent] => 54.1
                                )

                            [6] => Array
                                (
                                    [racer_boat_number] => 6
                                    [racer_name] => 飯山 晃三
                                    [racer_number] => 3906
                                    [racer_class_number] => 3
                                    [racer_branch_number] => 42
                                    [racer_birthplace_number] => 42
                                    [racer_age] => 47
                                    [racer_weight] => 58.6
                                    [racer_flying_count] => 0
                                    [racer_late_count] => 0
                                    [racer_average_start_timing] => 0.2
                                    [racer_national_top_1_percent] => 4.03
                                    [racer_national_top_2_percent] => 24.04
                                    [racer_national_top_3_percent] => 35.58
                                    [racer_local_top_1_percent] => 4.61
                                    [racer_local_top_2_percent] => 29.35
                                    [racer_local_top_3_percent] => 42.39
                                    [racer_assigned_motor_number] => 75
                                    [racer_assigned_motor_top_2_percent] => 35
                                    [racer_assigned_motor_top_3_percent] => 57.5
                                    [racer_assigned_boat_number] => 75
                                    [racer_assigned_boat_top_2_percent] => 32.77
                                    [racer_assigned_boat_top_3_percent] => 50.42
                                )

                        )

                )

        )

)
*/

// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの直前情報を取得
$previews = Scraper::scrapePreviews('2025-01-01', 24, 1);

// 例: ボートレースの公式サイトから2025年01月01日の大村1レースのオッズを取得
$odds = Scraper::scrapeOdds('2025-01-01', 24, 1);

// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの結果を取得
$results = Scraper::scrapeResults('2025-01-01', 24, 1);
```

## License
The BVP Boatrace Scraper is open source software licensed under the [MIT license](LICENSE).
