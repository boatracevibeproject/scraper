# Scraper for Boatrace Venture Project

[![security](https://github.com/shimomo/bvp-scraper/actions/workflows/security.yml/badge.svg)](https://github.com/shimomo/bvp-scraper/actions/workflows/security.yml)
[![test](https://github.com/shimomo/bvp-scraper/actions/workflows/test.yml/badge.svg)](https://github.com/shimomo/bvp-scraper/actions/workflows/test.yml)
[![codecov](https://codecov.io/gh/shimomo/bvp-scraper/graph/badge.svg?token=1J6TVAC5FR)](https://codecov.io/gh/shimomo/bvp-scraper)
[![php](https://poser.pugx.org/bvp/scraper/require/php)](https://packagist.org/packages/bvp/scraper)
[![stable](https://poser.pugx.org/bvp/scraper/v/stable)](https://packagist.org/packages/bvp/scraper)
[![license](https://poser.pugx.org/bvp/scraper/license)](https://packagist.org/packages/bvp/scraper)

BVP Scraper は、ボートレースの公式サイトから出走表、直前情報、オッズ、結果をスクレイピングするための PHP ライブラリです。

v10 では、後継ライブラリである [turnmark/scraper](https://github.com/turnmark/turnmark) とは異なる方向性を持つ派生として、以下の3点に力を入れています。

- **自己完結**: `bvp/converter`・`bvp/trimmer` への外部依存を廃し、変換・ドメインモデル（Enum）を内製化。
- **インスタンス単位の並行実行**: レート制御・キャッシュ参照をインスタンススコープに保持するため、プロキシやワーカーごとに複数の `Scraper` インスタンスを同一プロセス内で干渉なく並行運用できます。
- **鮮度に応じたキャッシュ**: 確定済みの過去日のレースは不変とみなし、キャッシュに永続化。バックフィル用途で同じ日付を何度も取り直す必要がなくなります。

## 📦 Requirements

- php: ^8.2
- nesbot/carbon: ^2.63 || ^3.0
- psr/simple-cache: ^3.0
- symfony/browser-kit: ^6.0 || ^7.0 || ^8.0
- symfony/cache: ^6.0 || ^7.0 || ^8.0
- symfony/css-selector: ^6.0 || ^7.0 || ^8.0
- symfony/http-client: ^6.0 || ^7.0 || ^8.0

## 💾 Installation

```bash
composer require bvp/scraper
```

## ⚡ Usage

### サポートメソッド一覧

`Scraper` はインスタンスベースの API です。静的なシングルトンファサードは提供していません（後述）。

| メソッド | 説明 | 引数 |
|---|---|---|
| `scrapeProgram($date, $stadiumNumber, $raceNumber)` | 出走表を取得 | `$date` : Carbon対応日付文字列またはCarbonインスタンス<br>`$stadiumNumber` : 1〜24<br>`$raceNumber` : 1〜12 |
| `scrapePreview($date, $stadiumNumber, $raceNumber)` | 直前情報を取得 | 同上 |
| `scrapeOdds($date, $stadiumNumber, $raceNumber)` | 全オッズ（7種）を取得 | 同上 |
| `scrapeWin` / `scrapePlace` / `scrapeExacta` / `scrapeQuinella` / `scrapeQuinellaPlace` / `scrapeTrifecta` / `scrapeTrio` | 単勝・複勝・2連単・2連複・拡連複・3連単・3連複のオッズを個別に取得 | 同上 |
| `scrapeResult($date, $stadiumNumber, $raceNumber)` | 結果を取得 | 同上 |
| `scrapeStadium($date)` | 開催中の場を取得 | `$date` のみ |
| `scrapeProgramBulk($date, $stadiumNumbers = [], $raceNumbers = [])` | 出走表を一括取得 | `$stadiumNumbers`/`$raceNumbers` 省略時はその日開催している全場・全レース |
| `scrapePreviewBulk` / `scrapeOddsBulk` / `scrapeResultBulk` | 同上の一括取得版 | 同上 |

**$date の例**
- `'2025-01-01'`
- `'2025/01/01'`
- `'yesterday'`
- `Carbon::now()->subDay()`

### 基本的な使い方

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use BVP\Scraper\Scraper;

$scraper = new Scraper();

// 出走表を取得
$program = $scraper->scrapeProgram('2025-01-01', 24, 1);

// 直前情報を取得
$preview = $scraper->scrapePreview('2025-01-01', 24, 1);

// オッズを取得
$odds = $scraper->scrapeOdds('2025-01-01', 24, 1);

// 結果を取得
$result = $scraper->scrapeResult('2025-01-01', 24, 1);

print_r($program);
print_r($preview);
print_r($odds);
print_r($result);
```

### 一括取得

```php
// その日開催している全場・全レースの結果を取得
$results = $scraper->scrapeResultBulk('2025-01-01');

// 開催場・レースを絞り込む
$results = $scraper->scrapeResultBulk('2025-01-01', [24], [1, 2, 3]);
```

### レスポンス形式（`_source` / 変換済み値）

各フィールドは、公式サイトから取得した生の文字列（`{field}_source`）と、型変換・Enum変換済みの値（`{field}`）のペアで返されます。生データが常に残るため、変換ロジックの検証やデバッグがしやすくなっています。

```php
$result = $scraper->scrapeResult('2017-03-31', 24, 1);

// $result の抜粋
[
    'weather_number_source' => '雨',
    'weather_number' => 3,
    'wind_direction_number_source' => '南西',
    'wind_direction_number' => 11,
    'racers' => [
        1 => [
            'name' => '中辻 博訓',
            'number_source' => '3833',
            'number' => 3833,
            // ...
        ],
        // ...
    ],
];
```

### キャッシュ

過去日（実行日より前の日付）のスクレイピング結果は、既定でファイルシステムベースの PSR-16 キャッシュに無期限保存されます。当日・未来日はキャッシュされません（レース情報自体が変動しうるため）。

```php
use BVP\Scraper\Caching\CacheFactory;

// キャッシュディレクトリを指定
$scraper = new Scraper(cache: CacheFactory::createDefault('/path/to/cache'));

// 1回目: ネットワークにアクセス
$scraper->scrapeResult('2017-03-31', 24, 1);

// 2回目: キャッシュから即座に返る
$scraper->scrapeResult('2017-03-31', 24, 1);
```

`Psr\SimpleCache\CacheInterface` を実装した任意のバックエンド（Redis や APCu など）や、キャッシュ対象の判定ロジック（`BVP\Scraper\Caching\CachePolicyInterface`）を差し替えることもできます。

### 並行実行・マルチテナンシー

レート制御はインスタンスごとに保持されるため、プロキシやアカウントが異なる複数の `Scraper` インスタンスを同一プロセス内で並行運用しても、互いのペース配分を食い合いません。

```php
use BVP\Scraper\RateLimiting\ThrottleRateLimiter;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

// ワーカー1: プロキシA経由で今日分を取得（3秒間隔）
$scraperA = new Scraper(
    httpBrowser: new HttpBrowser(HttpClient::create(['proxy' => 'http://proxy-a:8080'])),
    rateLimiter: new ThrottleRateLimiter(3.0),
);

// ワーカー2: プロキシB経由で過去分をバックフィル（1秒間隔）
$scraperB = new Scraper(
    httpBrowser: new HttpBrowser(HttpClient::create(['proxy' => 'http://proxy-b:8080'])),
    rateLimiter: new ThrottleRateLimiter(1.0),
);

// 両者は独立したレート状態を持つため、同一プロセス内で並行運用しても
// 互いのペース配分を食い合わない
```

## ⚠️ Notes

- v10 は v6 系との後方互換性を意図的に持たない大きな設計変更（インスタンスベース API・レスポンススキーマの変更）を含みます。既存の利用箇所は `bvp/scraper: ^6.0` に固定してください。
- **スクレイピング対象の公式サイトの構造が変更された場合**、正しくデータを取得できなくなる可能性があります。
- 利用時は対象サイトの利用規約を遵守してください。

## 📄 License

Scraper は [MIT license](LICENSE) の元で公開されています。
