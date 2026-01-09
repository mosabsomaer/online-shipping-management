<?php

namespace App\Helpers;

class ArabicHelper
{
    /**
     * Arabic letter forms mapping: [isolated, final, initial, medial]
     */
    private static array $letters = [
        'ا' => ['ﺍ', 'ﺎ', 'ﺍ', 'ﺎ'],
        'أ' => ['ﺃ', 'ﺄ', 'ﺃ', 'ﺄ'],
        'إ' => ['ﺇ', 'ﺈ', 'ﺇ', 'ﺈ'],
        'آ' => ['ﺁ', 'ﺂ', 'ﺁ', 'ﺂ'],
        'ب' => ['ﺏ', 'ﺐ', 'ﺑ', 'ﺒ'],
        'ت' => ['ﺕ', 'ﺖ', 'ﺗ', 'ﺘ'],
        'ث' => ['ﺙ', 'ﺚ', 'ﺛ', 'ﺜ'],
        'ج' => ['ﺝ', 'ﺞ', 'ﺟ', 'ﺠ'],
        'ح' => ['ﺡ', 'ﺢ', 'ﺣ', 'ﺤ'],
        'خ' => ['ﺥ', 'ﺦ', 'ﺧ', 'ﺨ'],
        'د' => ['ﺩ', 'ﺪ', 'ﺩ', 'ﺪ'],
        'ذ' => ['ﺫ', 'ﺬ', 'ﺫ', 'ﺬ'],
        'ر' => ['ﺭ', 'ﺮ', 'ﺭ', 'ﺮ'],
        'ز' => ['ﺯ', 'ﺰ', 'ﺯ', 'ﺰ'],
        'س' => ['ﺱ', 'ﺲ', 'ﺳ', 'ﺴ'],
        'ش' => ['ﺵ', 'ﺶ', 'ﺷ', 'ﺸ'],
        'ص' => ['ﺹ', 'ﺺ', 'ﺻ', 'ﺼ'],
        'ض' => ['ﺽ', 'ﺾ', 'ﺿ', 'ﻀ'],
        'ط' => ['ﻁ', 'ﻂ', 'ﻃ', 'ﻄ'],
        'ظ' => ['ﻅ', 'ﻆ', 'ﻇ', 'ﻈ'],
        'ع' => ['ﻉ', 'ﻊ', 'ﻋ', 'ﻌ'],
        'غ' => ['ﻍ', 'ﻎ', 'ﻏ', 'ﻐ'],
        'ف' => ['ﻑ', 'ﻒ', 'ﻓ', 'ﻔ'],
        'ق' => ['ﻕ', 'ﻖ', 'ﻗ', 'ﻘ'],
        'ك' => ['ﻙ', 'ﻚ', 'ﻛ', 'ﻜ'],
        'ل' => ['ﻝ', 'ﻞ', 'ﻟ', 'ﻠ'],
        'م' => ['ﻡ', 'ﻢ', 'ﻣ', 'ﻤ'],
        'ن' => ['ﻥ', 'ﻦ', 'ﻧ', 'ﻨ'],
        'ه' => ['ﻩ', 'ﻪ', 'ﻫ', 'ﻬ'],
        'و' => ['ﻭ', 'ﻮ', 'ﻭ', 'ﻮ'],
        'ي' => ['ﻱ', 'ﻲ', 'ﻳ', 'ﻴ'],
        'ى' => ['ﻯ', 'ﻰ', 'ﻯ', 'ﻰ'],
        'ئ' => ['ﺉ', 'ﺊ', 'ﺋ', 'ﺌ'],
        'ؤ' => ['ﺅ', 'ﺆ', 'ﺅ', 'ﺆ'],
        'ء' => ['ء', 'ء', 'ء', 'ء'],
        'ة' => ['ﺓ', 'ﺔ', 'ﺓ', 'ﺔ'],
        'لا' => ['ﻻ', 'ﻼ', 'ﻻ', 'ﻼ'],
        'لأ' => ['ﻷ', 'ﻸ', 'ﻷ', 'ﻸ'],
        'لإ' => ['ﻹ', 'ﻺ', 'ﻹ', 'ﻺ'],
        'لآ' => ['ﻵ', 'ﻶ', 'ﻵ', 'ﻶ'],
    ];

    /**
     * Letters that don't connect to the next letter
     */
    private static array $nonConnecting = ['ا', 'أ', 'إ', 'آ', 'د', 'ذ', 'ر', 'ز', 'و', 'ؤ', 'ة', 'ء'];

    /**
     * Check if a character is Arabic
     */
    private static function isArabic(string $char): bool
    {
        return isset(self::$letters[$char]) || preg_match('/[\x{0600}-\x{06FF}]/u', $char);
    }

    /**
     * Reshape Arabic text for proper display in PDF
     * Handles mixed Arabic/English text properly
     */
    public static function reshape(string $text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Split text into segments of Arabic and non-Arabic
        $segments = [];
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $currentSegment = '';
        $isCurrentArabic = null;

        foreach ($chars as $char) {
            $charIsArabic = self::isArabic($char);

            if ($isCurrentArabic === null) {
                $isCurrentArabic = $charIsArabic;
            }

            if ($charIsArabic === $isCurrentArabic || $char === ' ') {
                $currentSegment .= $char;
            } else {
                if ($currentSegment !== '') {
                    $segments[] = ['text' => $currentSegment, 'arabic' => $isCurrentArabic];
                }
                $currentSegment = $char;
                $isCurrentArabic = $charIsArabic;
            }
        }

        if ($currentSegment !== '') {
            $segments[] = ['text' => $currentSegment, 'arabic' => $isCurrentArabic];
        }

        // Process each segment
        $result = '';
        foreach ($segments as $segment) {
            if ($segment['arabic']) {
                $result .= self::reshapeArabicSegment($segment['text']);
            } else {
                $result .= $segment['text'];
            }
        }

        return $result;
    }

    /**
     * Reshape a pure Arabic text segment
     */
    private static function reshapeArabicSegment(string $text): string
    {
        // Handle ligatures first (لا, لأ, لإ, لآ)
        $text = str_replace(['لا', 'لأ', 'لإ', 'لآ'], ['__LA__', '__LAH__', '__LAE__', '__LAA__'], $text);

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $result = [];
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];

            // Handle ligature placeholders
            if ($char === '_' && isset($chars[$i + 1]) && $chars[$i + 1] === '_') {
                $ligature = '';
                $j = $i;
                while ($j < $count && ($chars[$j] === '_' || ctype_alpha($chars[$j]))) {
                    $ligature .= $chars[$j];
                    $j++;
                }
                if (str_starts_with($ligature, '__LA__')) {
                    $prev = $i > 0 ? ($chars[$i - 1] ?? null) : null;
                    $prevConnects = $prev && isset(self::$letters[$prev]) && ! in_array($prev, self::$nonConnecting);
                    $form = $prevConnects ? 1 : 0;
                    $result[] = self::$letters['لا'][$form];
                    $i += 5;

                    continue;
                } elseif (str_starts_with($ligature, '__LAH__')) {
                    $prev = $i > 0 ? ($chars[$i - 1] ?? null) : null;
                    $prevConnects = $prev && isset(self::$letters[$prev]) && ! in_array($prev, self::$nonConnecting);
                    $form = $prevConnects ? 1 : 0;
                    $result[] = self::$letters['لأ'][$form];
                    $i += 6;

                    continue;
                } elseif (str_starts_with($ligature, '__LAE__')) {
                    $prev = $i > 0 ? ($chars[$i - 1] ?? null) : null;
                    $prevConnects = $prev && isset(self::$letters[$prev]) && ! in_array($prev, self::$nonConnecting);
                    $form = $prevConnects ? 1 : 0;
                    $result[] = self::$letters['لإ'][$form];
                    $i += 6;

                    continue;
                } elseif (str_starts_with($ligature, '__LAA__')) {
                    $prev = $i > 0 ? ($chars[$i - 1] ?? null) : null;
                    $prevConnects = $prev && isset(self::$letters[$prev]) && ! in_array($prev, self::$nonConnecting);
                    $form = $prevConnects ? 1 : 0;
                    $result[] = self::$letters['لآ'][$form];
                    $i += 6;

                    continue;
                }
            }

            // Not an Arabic letter - keep as is
            if (! isset(self::$letters[$char])) {
                $result[] = $char;

                continue;
            }

            $prev = $i > 0 ? ($chars[$i - 1] ?? null) : null;
            $next = $chars[$i + 1] ?? null;

            // Check if previous letter connects to this one
            $prevConnects = $prev && isset(self::$letters[$prev]) && ! in_array($prev, self::$nonConnecting);

            // Check if this letter connects to next
            $nextConnects = $next && isset(self::$letters[$next]) && ! in_array($char, self::$nonConnecting);

            // Determine form: 0=isolated, 1=final, 2=initial, 3=medial
            if ($prevConnects && $nextConnects) {
                $form = 3; // medial
            } elseif ($prevConnects) {
                $form = 1; // final
            } elseif ($nextConnects) {
                $form = 2; // initial
            } else {
                $form = 0; // isolated
            }

            $result[] = self::$letters[$char][$form];
        }

        // Reverse only the Arabic segment for RTL display in PDF
        return implode('', array_reverse($result));
    }

    /**
     * Check if text contains Arabic characters
     */
    public static function containsArabic(string $text): bool
    {
        return (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Reshape text only if it contains Arabic
     */
    public static function reshapeIfArabic(string $text): string
    {
        if (self::containsArabic($text)) {
            return self::reshape($text);
        }

        return $text;
    }
}
