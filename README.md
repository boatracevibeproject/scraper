# BVP Boatrace Scraper

[![Build Status](https://github.com/shimomo/bvp-boatrace-scraper/workflows/Tests/badge.svg)](https://github.com/shimomo/bvp-boatrace-scraper/actions?query=workflow%3Atests)
[![codecov](https://codecov.io/gh/shimomo/bvp-boatrace-scraper/branch/5.x/graph/badge.svg?token=1J6TVAC5FR)](https://codecov.io/gh/shimomo/bvp-boatrace-scraper)
[![Latest Stable Version](https://poser.pugx.org/bvp/boatrace-scraper/v/stable)](https://packagist.org/packages/bvp/boatrace-scraper)
[![Latest Unstable Version](https://poser.pugx.org/bvp/boatrace-scraper/v/unstable)](https://packagist.org/packages/bvp/boatrace-scraper#5.x-dev)
[![License](https://poser.pugx.org/bvp/boatrace-scraper/license)](https://packagist.org/packages/bvp/boatrace-scraper)

## Installation
```bash
composer require bvp/boatrace-scraper
```

## Usage
```php
<?php

require __DIR__ . '/vendor/autoload.php';

use BVP\BoatraceScraper\Scraper;

$stadiums = Scraper::scrapeStadiums('2017-03-31');        // 2017年03月31日の開催場

$programs = Scraper::scrapePrograms('2017-03-31');        // 2017年03月31日の出走表
$programs = Scraper::scrapePrograms('2017-03-31', 24);    // 2017年03月31日 大村の出走表
$programs = Scraper::scrapePrograms('2017-03-31', 24, 1); // 2017年03月31日 大村 1Rの出走表

$previews = Scraper::scrapePreviews('2017-03-31');        // 2017年03月31日の直前情報
$previews = Scraper::scrapePreviews('2017-03-31', 24);    // 2017年03月31日 大村の直前情報
$previews = Scraper::scrapePreviews('2017-03-31', 24, 1); // 2017年03月31日 大村 1Rの直前情報

$odds = Scraper::scrapeOdds('2017-03-31');                // 2017年03月31日のオッズ
$odds = Scraper::scrapeOdds('2017-03-31', 24);            // 2017年03月31日 大村のオッズ
$odds = Scraper::scrapeOdds('2017-03-31', 24, 1);         // 2017年03月31日 大村 1Rのオッズ

$results = Scraper::scrapeResults('2017-03-31');          // 2017年03月31日の結果
$results = Scraper::scrapeResults('2017-03-31', 24);      // 2017年03月31日 大村の結果
$results = Scraper::scrapeResults('2017-03-31', 24, 1);   // 2017年03月31日 大村 1Rの結果
```

## License
The BVP Boatrace Scraper package is open source software licensed under the [MIT license](LICENSE).
