# Scraper for Boatrace Venture Project

[![security](https://github.com/shimomo/bvp-scraper/actions/workflows/security.yml/badge.svg)](https://github.com/shimomo/bvp-scraper/actions/workflows/security.yml)
[![test](https://github.com/shimomo/bvp-scraper/actions/workflows/test.yml/badge.svg)](https://github.com/shimomo/bvp-scraper/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/shimomo/bvp-scraper/graph/badge.svg?token=1J6TVAC5FR)](https://codecov.io/gh/shimomo/bvp-scraper)
[![php](https://poser.pugx.org/bvp/scraper/require/php)](https://packagist.org/packages/bvp/scraper)
[![stable](https://poser.pugx.org/bvp/scraper/v/stable)](https://packagist.org/packages/bvp/scraper)
[![license](https://poser.pugx.org/bvp/scraper/license)](https://packagist.org/packages/bvp/scraper)

BVP Scraper は、ボートレースの公式サイトから出走表、直前情報、オッズ、結果をスクレイピングするための PHP ライブラリです。

## 📦 Requirements

- php: ^8.2
- bvp/converter: ^0.2
- bvp/trimmer: ^0.1
- nesbot/carbon: ^2.63 || ^3.0
- symfony/browser-kit: ^6.0 || ^7.0
- symfony/css-selector: ^6.0 || ^7.0
- symfony/http-client: ^6.0 || ^7.0

## 💾 Installation

```bash
composer require bvp/scraper
```

## ⚡ Usage

### サポートメソッド一覧

| メソッド | 説明 | 引数 |
|---|---|---|
| `Scraper::scrapePrograms(`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceDate = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceStadiumNumber = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceNumber = null`<br>`)` | 出走表を取得 | `$raceDate` : Carbon対応日付文字列<br>&nbsp;&nbsp;&nbsp;&nbsp;またはCarbonインスタンス（省略時は当日）<br>`$raceStadiumNumber` : 1〜12（省略時は全開催場）<br>`$raceNumber` : 1〜12（省略時は全レース） |
| `Scraper::scrapePreviews(`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceDate = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceStadiumNumber = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceNumber = null`<br>`)` | 直前情報を取得 | 同上 |
| `Scraper::scrapeOdds(`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceDate = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceStadiumNumber = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceNumber = null`<br>`)` | オッズを取得 | 同上 |
| `Scraper::scrapeResults(`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceDate = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceStadiumNumber = null,`<br>&nbsp;&nbsp;&nbsp;&nbsp;`$raceNumber = null`<br>`)` | 結果を取得 | 同上 |

**$raceDate の例**
- `'2025-01-01'`
- `'2025/01/01'`
- `'yesterday'`
- `Carbon::now()->subDay()`

### 基本的な使い方

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use BVP\Scraper\Scraper;

// 出走表を取得
$programs = Scraper::scrapePrograms('2025-01-01', 24, 1);

// 直前情報を取得
$previews = Scraper::scrapePreviews('2025-01-01', 24, 1);

// オッズを取得
$odds = Scraper::scrapeOdds('2025-01-01', 24, 1);

// 結果を取得
$results = Scraper::scrapeResults('2025-01-01', 24, 1);

print_r($programs);
print_r($previews);
print_r($odds);
print_r($results);
```

### Scraper::scrapePrograms()

```php
// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの出走表を取得
$programs = Scraper::scrapePrograms('2025-01-01', 24, 1);
print_r($programs);
```

<details>
<summary>取得結果</summary>

```php
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
                    [race_day_label] => 初日
                    [race_grade_label] => 一般
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
```

</details>

### Scraper::scrapePreviews()

```php
// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの直前情報を取得
$previews = Scraper::scrapePreviews('2025-01-01', 24, 1);
print_r($previews);
```

<details>
<summary>取得結果</summary>

```php
Array
(
    [24] => Array
        (
            [1] => Array
                (
                    [race_date] => 2025-01-01
                    [race_stadium_number] => 24
                    [race_number] => 1
                    [race_wind] => 1
                    [race_wind_direction_number] => 13
                    [race_wave] => 1
                    [race_weather_number] => 1
                    [race_temperature] => 6
                    [race_water_temperature] => 10
                    [boats] => Array
                        (
                            [1] => Array
                                (
                                    [racer_boat_number] => 1
                                    [racer_course_number] => 1
                                    [racer_start_timing] => 0.09
                                    [racer_weight] => 52.3
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.87
                                    [racer_tilt_adjustment] => 0
                                )

                            [2] => Array
                                (
                                    [racer_boat_number] => 2
                                    [racer_course_number] => 2
                                    [racer_start_timing] => -0.08
                                    [racer_weight] => 52.4
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.82
                                    [racer_tilt_adjustment] => -0.5
                                )

                            [3] => Array
                                (
                                    [racer_boat_number] => 3
                                    [racer_course_number] => 3
                                    [racer_start_timing] => -0.04
                                    [racer_weight] => 53.3
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.9
                                    [racer_tilt_adjustment] => 0
                                )

                            [4] => Array
                                (
                                    [racer_boat_number] => 4
                                    [racer_course_number] => 5
                                    [racer_start_timing] => -0.07
                                    [racer_weight] => 52.8
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.86
                                    [racer_tilt_adjustment] => 0.5
                                )

                            [5] => Array
                                (
                                    [racer_boat_number] => 5
                                    [racer_course_number] => 6
                                    [racer_start_timing] => 0.05
                                    [racer_weight] => 47
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.91
                                    [racer_tilt_adjustment] => -0.5
                                )

                            [6] => Array
                                (
                                    [racer_boat_number] => 6
                                    [racer_course_number] => 4
                                    [racer_start_timing] => -0.02
                                    [racer_weight] => 58.6
                                    [racer_weight_adjustment] => 0
                                    [racer_exhibition_time] => 6.88
                                    [racer_tilt_adjustment] => -0.5
                                )

                        )

                )

        )

)
```

</details>

### Scraper::scrapeOdds()

```php
// 例: ボートレースの公式サイトから2025年01月01日の大村1レースのオッズを取得
$odds = Scraper::scrapeOdds('2025-01-01', 24, 1);
print_r($odds);
```

<details>
<summary>取得結果</summary>

```php
Array
(
    [24] => Array
        (
            [1] => Array
                (
                    [race_date] => 2025-01-01
                    [race_stadium_number] => 24
                    [race_number] => 1
                    [win_odds] => Array
                        (
                            [1] => 1.6
                            [2] => 3.7
                            [3] => 6
                            [4] => 7.6
                            [5] => 13
                            [6] => 12.2
                        )

                    [place_odds] => Array
                        (
                            [1] => Array
                                (
                                    [lower_limit] => 1.3
                                    [upper_limit] => 1.5
                                )

                            [2] => Array
                                (
                                    [lower_limit] => 1.5
                                    [upper_limit] => 1.9
                                )

                            [3] => Array
                                (
                                    [lower_limit] => 2.2
                                    [upper_limit] => 2.7
                                )

                            [4] => Array
                                (
                                    [lower_limit] => 2.3
                                    [upper_limit] => 2.8
                                )

                            [5] => Array
                                (
                                    [lower_limit] => 2.5
                                    [upper_limit] => 3.2
                                )

                            [6] => Array
                                (
                                    [lower_limit] => 3.5
                                    [upper_limit] => 4.2
                                )

                        )

                    [exacta_odds] => Array
                        (
                            [1] => Array
                                (
                                    [2] => 2.5
                                    [3] => 4.6
                                    [4] => 10.2
                                    [5] => 45.9
                                    [6] => 13
                                )

                            [2] => Array
                                (
                                    [1] => 6.9
                                    [3] => 22.2
                                    [4] => 26.7
                                    [5] => 142.8
                                    [6] => 28.1
                                )

                            [3] => Array
                                (
                                    [1] => 28.2
                                    [2] => 49.6
                                    [4] => 73
                                    [5] => 188.5
                                    [6] => 138.6
                                )

                            [4] => Array
                                (
                                    [1] => 25.2
                                    [2] => 36.2
                                    [3] => 68.3
                                    [5] => 74.8
                                    [6] => 57.8
                                )

                            [5] => Array
                                (
                                    [1] => 134.6
                                    [2] => 261.8
                                    [3] => 285.6
                                    [4] => 200.5
                                    [6] => 409.8
                                )

                            [6] => Array
                                (
                                    [1] => 84.1
                                    [2] => 94.2
                                    [3] => 209.4
                                    [4] => 127.3
                                    [5] => 362.5
                                )

                        )

                    [quinella_odds] => Array
                        (
                            [1] => Array
                                (
                                    [2] => 2.3
                                    [3] => 3.7
                                    [4] => 6.5
                                    [5] => 25.5
                                    [6] => 11.4
                                )

                            [2] => Array
                                (
                                    [3] => 12.4
                                    [4] => 12.2
                                    [5] => 43.9
                                    [6] => 22.6
                                )

                            [3] => Array
                                (
                                    [4] => 20.9
                                    [5] => 63.8
                                    [6] => 54
                                )

                            [4] => Array
                                (
                                    [5] => 39
                                    [6] => 39
                                )

                            [5] => Array
                                (
                                    [6] => 140.5
                                )

                        )

                    [quinella_place_odds] => Array
                        (
                            [1] => Array
                                (
                                    [2] => Array
                                        (
                                            [lower_limit] => 1.4
                                            [upper_limit] => 1.7
                                        )

                                    [3] => Array
                                        (
                                            [lower_limit] => 1.9
                                            [upper_limit] => 2.3
                                        )

                                    [4] => Array
                                        (
                                            [lower_limit] => 1.3
                                            [upper_limit] => 1.6
                                        )

                                    [5] => Array
                                        (
                                            [lower_limit] => 7.6
                                            [upper_limit] => 9
                                        )

                                    [6] => Array
                                        (
                                            [lower_limit] => 2.8
                                            [upper_limit] => 3.4
                                        )

                                )

                            [2] => Array
                                (
                                    [3] => Array
                                        (
                                            [lower_limit] => 3
                                            [upper_limit] => 4
                                        )

                                    [4] => Array
                                        (
                                            [lower_limit] => 2.6
                                            [upper_limit] => 3.8
                                        )

                                    [5] => Array
                                        (
                                            [lower_limit] => 7.9
                                            [upper_limit] => 9.4
                                        )

                                    [6] => Array
                                        (
                                            [lower_limit] => 3.7
                                            [upper_limit] => 4.8
                                        )

                                )

                            [3] => Array
                                (
                                    [4] => Array
                                        (
                                            [lower_limit] => 5
                                            [upper_limit] => 7
                                        )

                                    [5] => Array
                                        (
                                            [lower_limit] => 14.4
                                            [upper_limit] => 16.5
                                        )

                                    [6] => Array
                                        (
                                            [lower_limit] => 8.2
                                            [upper_limit] => 10
                                        )

                                )

                            [4] => Array
                                (
                                    [5] => Array
                                        (
                                            [lower_limit] => 6.5
                                            [upper_limit] => 8
                                        )

                                    [6] => Array
                                        (
                                            [lower_limit] => 7.6
                                            [upper_limit] => 10.1
                                        )

                                )

                            [5] => Array
                                (
                                    [6] => Array
                                        (
                                            [lower_limit] => 24.5
                                            [upper_limit] => 26.3
                                        )

                                )

                        )

                    [trifecta_odds] => Array
                        (
                            [1] => Array
                                (
                                    [2] => Array
                                        (
                                            [3] => 5.6
                                            [4] => 9.1
                                            [5] => 73.5
                                            [6] => 11.8
                                        )

                                    [3] => Array
                                        (
                                            [2] => 10
                                            [4] => 13.7
                                            [5] => 82.7
                                            [6] => 18.3
                                        )

                                    [4] => Array
                                        (
                                            [2] => 21.9
                                            [3] => 27.9
                                            [5] => 105.4
                                            [6] => 41.3
                                        )

                                    [5] => Array
                                        (
                                            [2] => 215.6
                                            [3] => 223.8
                                            [4] => 234.5
                                            [6] => 299.6
                                        )

                                    [6] => Array
                                        (
                                            [2] => 31.8
                                            [3] => 39.8
                                            [4] => 54.1
                                            [5] => 223
                                        )

                                )

                            [2] => Array
                                (
                                    [1] => Array
                                        (
                                            [3] => 20.3
                                            [4] => 22.7
                                            [5] => 124
                                            [6] => 29.9
                                        )

                                    [3] => Array
                                        (
                                            [1] => 55
                                            [4] => 82
                                            [5] => 353.4
                                            [6] => 97.8
                                        )

                                    [4] => Array
                                        (
                                            [1] => 71.7
                                            [3] => 108.8
                                            [5] => 318.1
                                            [6] => 110.3
                                        )

                                    [5] => Array
                                        (
                                            [1] => 449.5
                                            [3] => 759.4
                                            [4] => 670.7
                                            [6] => 812.5
                                        )

                                    [6] => Array
                                        (
                                            [1] => 102.4
                                            [3] => 139.9
                                            [4] => 131.8
                                            [5] => 517
                                        )

                                )

                            [3] => Array
                                (
                                    [1] => Array
                                        (
                                            [2] => 69.5
                                            [4] => 82
                                            [5] => 247.3
                                            [6] => 117.1
                                        )

                                    [2] => Array
                                        (
                                            [1] => 111.7
                                            [4] => 174
                                            [5] => 612.3
                                            [6] => 217.7
                                        )

                                    [4] => Array
                                        (
                                            [1] => 194.3
                                            [2] => 224.9
                                            [5] => 652.8
                                            [6] => 314.4
                                        )

                                    [5] => Array
                                        (
                                            [1] => 628.1
                                            [2] => 1070
                                            [4] => 1207
                                            [6] => 1130
                                        )

                                    [6] => Array
                                        (
                                            [1] => 252.6
                                            [2] => 297.7
                                            [4] => 357.5
                                            [5] => 968.3
                                        )

                                )

                            [4] => Array
                                (
                                    [1] => Array
                                        (
                                            [2] => 64.7
                                            [3] => 95
                                            [5] => 146.1
                                            [6] => 117.2
                                        )

                                    [2] => Array
                                        (
                                            [1] => 87.1
                                            [3] => 156.8
                                            [5] => 213.4
                                            [6] => 163
                                        )

                                    [3] => Array
                                        (
                                            [1] => 188.1
                                            [2] => 245.1
                                            [5] => 548.7
                                            [6] => 347.1
                                        )

                                    [5] => Array
                                        (
                                            [1] => 193.3
                                            [2] => 256
                                            [3] => 447.3
                                            [6] => 454.3
                                        )

                                    [6] => Array
                                        (
                                            [1] => 227.9
                                            [2] => 252
                                            [3] => 390.5
                                            [5] => 576.6
                                        )

                                )

                            [5] => Array
                                (
                                    [1] => Array
                                        (
                                            [2] => 689.6
                                            [3] => 873.6
                                            [4] => 872
                                            [6] => 1355
                                        )

                                    [2] => Array
                                        (
                                            [1] => 1053
                                            [3] => 1499
                                            [4] => 1383
                                            [6] => 1740
                                        )

                                    [3] => Array
                                        (
                                            [1] => 1242
                                            [2] => 2003
                                            [4] => 1740
                                            [6] => 2213
                                        )

                                    [4] => Array
                                        (
                                            [1] => 984.7
                                            [2] => 1153
                                            [3] => 1399
                                            [6] => 1559
                                        )

                                    [6] => Array
                                        (
                                            [1] => 1977
                                            [2] => 2300
                                            [3] => 2047
                                            [4] => 2074
                                        )

                                )

                            [6] => Array
                                (
                                    [1] => Array
                                        (
                                            [2] => 197
                                            [3] => 310
                                            [4] => 293.4
                                            [5] => 958.3
                                        )

                                    [2] => Array
                                        (
                                            [1] => 257.6
                                            [3] => 380.3
                                            [4] => 358.9
                                            [5] => 1216
                                        )

                                    [3] => Array
                                        (
                                            [1] => 472.3
                                            [2] => 485.6
                                            [4] => 646.4
                                            [5] => 1430
                                        )

                                    [4] => Array
                                        (
                                            [1] => 390.2
                                            [2] => 403.1
                                            [3] => 642.8
                                            [5] => 1185
                                        )

                                    [5] => Array
                                        (
                                            [1] => 1654
                                            [2] => 1684
                                            [3] => 1690
                                            [4] => 1602
                                        )

                                )

                        )

                    [trio_odds] => Array
                        (
                            [1] => Array
                                (
                                    [2] => Array
                                        (
                                            [3] => 2.6
                                            [4] => 4.4
                                            [5] => 24.9
                                            [6] => 6.6
                                        )

                                    [3] => Array
                                        (
                                            [4] => 7.6
                                            [5] => 35.3
                                            [6] => 9.6
                                        )

                                    [4] => Array
                                        (
                                            [5] => 38
                                            [6] => 18.2
                                        )

                                    [5] => Array
                                        (
                                            [6] => 58.2
                                        )

                                )

                            [2] => Array
                                (
                                    [3] => Array
                                        (
                                            [4] => 24.8
                                            [5] => 74.5
                                            [6] => 38.5
                                        )

                                    [4] => Array
                                        (
                                            [5] => 64.1
                                            [6] => 30.5
                                        )

                                    [5] => Array
                                        (
                                            [6] => 121.6
                                        )

                                )

                            [3] => Array
                                (
                                    [4] => Array
                                        (
                                            [5] => 94.9
                                            [6] => 58.2
                                        )

                                    [5] => Array
                                        (
                                            [6] => 133.3
                                        )

                                )

                            [4] => Array
                                (
                                    [5] => Array
                                        (
                                            [6] => 105
                                        )

                                )

                        )

                )

        )

)
```

</details>

### Scraper::scrapeResults()

```php
// 例: ボートレースの公式サイトから2025年01月01日の大村1レースの結果を取得
$results = Scraper::scrapeResults('2025-01-01', 24, 1);
print_r($results);
```

<details>
<summary>取得結果</summary>

```php
Array
(
    [24] => Array
        (
            [1] => Array
                (
                    [race_date] => 2025-01-01
                    [race_stadium_number] => 24
                    [race_number] => 1
                    [race_wind] => 2
                    [race_wind_direction_number] => 9
                    [race_wave] => 1
                    [race_weather_number] => 1
                    [race_temperature] => 10
                    [race_water_temperature] => 10
                    [race_technique_number] => 1
                    [boats] => Array
                        (
                            [1] => Array
                                (
                                    [racer_boat_number] => 1
                                    [racer_course_number] => 1
                                    [racer_start_timing] => 0.13
                                    [racer_place_number] => 1
                                    [racer_number] => 3527
                                    [racer_name] => 中嶋 誠一郎
                                )

                            [2] => Array
                                (
                                    [racer_boat_number] => 2
                                    [racer_course_number] => 2
                                    [racer_start_timing] => 0.1
                                    [racer_place_number] => 2
                                    [racer_number] => 4603
                                    [racer_name] => 谷川 将太
                                )

                            [3] => Array
                                (
                                    [racer_boat_number] => 3
                                    [racer_course_number] => 3
                                    [racer_start_timing] => 0.14
                                    [racer_place_number] => 4
                                    [racer_number] => 3843
                                    [racer_name] => 上之 晃弘
                                )

                            [4] => Array
                                (
                                    [racer_boat_number] => 4
                                    [racer_course_number] => 5
                                    [racer_start_timing] => 0.16
                                    [racer_place_number] => 3
                                    [racer_number] => 5048
                                    [racer_name] => 眞鳥 康太
                                )

                            [5] => Array
                                (
                                    [racer_boat_number] => 5
                                    [racer_course_number] => 6
                                    [racer_start_timing] => 0.27
                                    [racer_place_number] => 6
                                    [racer_number] => 5335
                                    [racer_name] => 森 陽多
                                )

                            [6] => Array
                                (
                                    [racer_boat_number] => 6
                                    [racer_course_number] => 4
                                    [racer_start_timing] => 0.18
                                    [racer_place_number] => 5
                                    [racer_number] => 3906
                                    [racer_name] => 飯山 晃三
                                )

                        )

                    [payouts] => Array
                        (
                            [trifecta] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1-2-4
                                            [payout] => 910
                                        )

                                )

                            [trio] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1=2=4
                                            [payout] => 440
                                        )

                                )

                            [exacta] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1-2
                                            [payout] => 250
                                        )

                                )

                            [quinella] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1=2
                                            [payout] => 230
                                        )

                                )

                            [quinella_place] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1=2
                                            [payout] => 140
                                        )

                                    [1] => Array
                                        (
                                            [combination] => 1=4
                                            [payout] => 130
                                        )

                                    [2] => Array
                                        (
                                            [combination] => 2=4
                                            [payout] => 260
                                        )

                                )

                            [win] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1
                                            [payout] => 160
                                        )

                                )

                            [place] => Array
                                (
                                    [0] => Array
                                        (
                                            [combination] => 1
                                            [payout] => 130
                                        )

                                    [1] => Array
                                        (
                                            [combination] => 2
                                            [payout] => 150
                                        )

                                )

                        )

                )

        )

)
```

</details>

## ⚠️ Notes

- **スクレイピング対象の公式サイトの構造が変更された場合**、正しくデータを取得できなくなる可能性があります。
- 利用時は対象サイトの利用規約を遵守してください。

## 📄 License

Scraper は [MIT license](LICENSE) の元で公開されています。
