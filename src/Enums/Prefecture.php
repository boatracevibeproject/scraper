<?php

declare(strict_types=1);

namespace BVP\Scraper\Enums;

use ValueError;

/**
 * @author shimomo
 */
enum Prefecture: int
{
    case 北海道 = 1;
    case 青森県 = 2;
    case 岩手県 = 3;
    case 宮城県 = 4;
    case 秋田県 = 5;
    case 山形県 = 6;
    case 福島県 = 7;
    case 茨城県 = 8;
    case 栃木県 = 9;
    case 群馬県 = 10;
    case 埼玉県 = 11;
    case 千葉県 = 12;
    case 東京都 = 13;
    case 神奈川県 = 14;
    case 新潟県 = 15;
    case 富山県 = 16;
    case 石川県 = 17;
    case 福井県 = 18;
    case 山梨県 = 19;
    case 長野県 = 20;
    case 岐阜県 = 21;
    case 静岡県 = 22;
    case 愛知県 = 23;
    case 三重県 = 24;
    case 滋賀県 = 25;
    case 京都府 = 26;
    case 大阪府 = 27;
    case 兵庫県 = 28;
    case 奈良県 = 29;
    case 和歌山県 = 30;
    case 鳥取県 = 31;
    case 島根県 = 32;
    case 岡山県 = 33;
    case 広島県 = 34;
    case 山口県 = 35;
    case 徳島県 = 36;
    case 香川県 = 37;
    case 愛媛県 = 38;
    case 高知県 = 39;
    case 福岡県 = 40;
    case 佐賀県 = 41;
    case 長崎県 = 42;
    case 熊本県 = 43;
    case 大分県 = 44;
    case 宮崎県 = 45;
    case 鹿児島県 = 46;
    case 沖縄県 = 47;

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function shortName(): string
    {
        return match ($this) {
            self::北海道 => '北海道',
            self::青森県 => '青森',
            self::岩手県 => '岩手',
            self::宮城県 => '宮城',
            self::秋田県 => '秋田',
            self::山形県 => '山形',
            self::福島県 => '福島',
            self::茨城県 => '茨城',
            self::栃木県 => '栃木',
            self::群馬県 => '群馬',
            self::埼玉県 => '埼玉',
            self::千葉県 => '千葉',
            self::東京都 => '東京',
            self::神奈川県 => '神奈川',
            self::新潟県 => '新潟',
            self::富山県 => '富山',
            self::石川県 => '石川',
            self::福井県 => '福井',
            self::山梨県 => '山梨',
            self::長野県 => '長野',
            self::岐阜県 => '岐阜',
            self::静岡県 => '静岡',
            self::愛知県 => '愛知',
            self::三重県 => '三重',
            self::滋賀県 => '滋賀',
            self::京都府 => '京都',
            self::大阪府 => '大阪',
            self::兵庫県 => '兵庫',
            self::奈良県 => '奈良',
            self::和歌山県 => '和歌山',
            self::鳥取県 => '鳥取',
            self::島根県 => '島根',
            self::岡山県 => '岡山',
            self::広島県 => '広島',
            self::山口県 => '山口',
            self::徳島県 => '徳島',
            self::香川県 => '香川',
            self::愛媛県 => '愛媛',
            self::高知県 => '高知',
            self::福岡県 => '福岡',
            self::佐賀県 => '佐賀',
            self::長崎県 => '長崎',
            self::熊本県 => '熊本',
            self::大分県 => '大分',
            self::宮崎県 => '宮崎',
            self::鹿児島県 => '鹿児島',
            self::沖縄県 => '沖縄',
        };
    }

    /**
     * @param ?string $name
     * @return ?self
     * @throws \ValueError
     */
    public static function fromName(?string $name): ?self
    {
        if ($name === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->name() === $name) {
                return $case;
            }
        }

        throw new ValueError(
            "{$name} is not a valid name for enum " . self::class
        );
    }

    /**
     * @param ?string $shortName
     * @return ?self
     * @throws \ValueError
     */
    public static function fromShortName(?string $shortName): ?self
    {
        if ($shortName === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if ($case->shortName() === $shortName) {
                return $case;
            }
        }

        throw new ValueError(
            "{$shortName} is not a valid name for enum " . self::class
        );
    }

    /**
     * @return list<array{
     *     number: int,
     *     name: non-empty-string,
     *     short_name: non-empty-string,
     * }>
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'number' => $case->value,
            'name' => $case->name(),
            'short_name' => $case->shortName(),
        ], self::cases());
    }
}
