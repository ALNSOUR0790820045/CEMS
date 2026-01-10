<?php

namespace App\Services;

class AmountToWordsService
{
    private static $arabicOnes = [
        '', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
        'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر',
        'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'
    ];

    private static $arabicTens = [
        '', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'
    ];

    private static $arabicHundreds = [
        '', 'مئة', 'مئتان', 'ثلاثمئة', 'أربعمئة', 'خمسمئة', 'ستمئة', 'سبعمئة', 'ثمانمئة', 'تسعمئة'
    ];

    private static $englishOnes = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
        'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];

    private static $englishTens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    /**
     * Convert amount to words in Arabic
     */
    public static function convertToArabic($amount, $currencyCode = 'JOD', $decimalPlaces = 3)
    {
        $currencies = self::getCurrencyNames();
        $currencyData = $currencies[$currencyCode] ?? $currencies['JOD'];

        // Split into integer and decimal parts
        $parts = explode('.', number_format($amount, $decimalPlaces, '.', ''));
        $integerPart = (int) $parts[0];
        $decimalPart = isset($parts[1]) ? (int) $parts[1] : 0;

        $result = '';

        // Convert integer part
        if ($integerPart == 0) {
            $result = 'صفر';
        } else {
            $result = self::convertIntegerToArabic($integerPart);
        }

        // Add main currency name
        $result .= ' ' . $currencyData['main'];

        // Convert decimal part if exists
        if ($decimalPart > 0) {
            $result .= ' و' . self::convertIntegerToArabic($decimalPart);
            $result .= ' ' . $currencyData['fraction'];
        }

        return $result . ' فقط لا غير';
    }

    /**
     * Convert amount to words in English
     */
    public static function convertToEnglish($amount, $currencyCode = 'JOD', $decimalPlaces = 3)
    {
        $currencies = self::getCurrencyNamesEnglish();
        $currencyData = $currencies[$currencyCode] ?? $currencies['JOD'];

        // Split into integer and decimal parts
        $parts = explode('.', number_format($amount, $decimalPlaces, '.', ''));
        $integerPart = (int) $parts[0];
        $decimalPart = isset($parts[1]) ? (int) $parts[1] : 0;

        $result = '';

        // Convert integer part
        if ($integerPart == 0) {
            $result = 'Zero';
        } else {
            $result = self::convertIntegerToEnglish($integerPart);
        }

        // Add main currency name
        $result .= ' ' . $currencyData['main'];

        // Convert decimal part if exists
        if ($decimalPart > 0) {
            $result .= ' and ' . self::convertIntegerToEnglish($decimalPart);
            $result .= ' ' . $currencyData['fraction'];
        }

        return $result . ' Only';
    }

    /**
     * Convert integer to Arabic words
     */
    private static function convertIntegerToArabic($number)
    {
        if ($number == 0) {
            return 'صفر';
        }

        if ($number < 20) {
            return self::$arabicOnes[$number];
        }

        if ($number < 100) {
            $tens = (int) ($number / 10);
            $ones = $number % 10;
            return self::$arabicTens[$tens] . ($ones > 0 ? ' و' . self::$arabicOnes[$ones] : '');
        }

        if ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            return self::$arabicHundreds[$hundreds] . ($remainder > 0 ? ' و' . self::convertIntegerToArabic($remainder) : '');
        }

        if ($number < 1000000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            
            $result = '';
            if ($thousands == 1) {
                $result = 'ألف';
            } elseif ($thousands == 2) {
                $result = 'ألفان';
            } elseif ($thousands < 11) {
                $result = self::convertIntegerToArabic($thousands) . ' آلاف';
            } else {
                $result = self::convertIntegerToArabic($thousands) . ' ألفاً';
            }
            
            return $result . ($remainder > 0 ? ' و' . self::convertIntegerToArabic($remainder) : '');
        }

        if ($number < 1000000000) {
            $millions = (int) ($number / 1000000);
            $remainder = $number % 1000000;
            
            $result = '';
            if ($millions == 1) {
                $result = 'مليون';
            } elseif ($millions == 2) {
                $result = 'مليونان';
            } elseif ($millions < 11) {
                $result = self::convertIntegerToArabic($millions) . ' ملايين';
            } else {
                $result = self::convertIntegerToArabic($millions) . ' مليون';
            }
            
            return $result . ($remainder > 0 ? ' و' . self::convertIntegerToArabic($remainder) : '');
        }

        return (string) $number; // Fallback for very large numbers
    }

    /**
     * Convert integer to English words
     */
    private static function convertIntegerToEnglish($number)
    {
        if ($number == 0) {
            return 'Zero';
        }

        if ($number < 20) {
            return self::$englishOnes[$number];
        }

        if ($number < 100) {
            $tens = (int) ($number / 10);
            $ones = $number % 10;
            return self::$englishTens[$tens] . ($ones > 0 ? '-' . self::$englishOnes[$ones] : '');
        }

        if ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            return self::$englishOnes[$hundreds] . ' Hundred' . 
                   ($remainder > 0 ? ' and ' . self::convertIntegerToEnglish($remainder) : '');
        }

        if ($number < 1000000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            return self::convertIntegerToEnglish($thousands) . ' Thousand' . 
                   ($remainder > 0 ? ' ' . self::convertIntegerToEnglish($remainder) : '');
        }

        if ($number < 1000000000) {
            $millions = (int) ($number / 1000000);
            $remainder = $number % 1000000;
            return self::convertIntegerToEnglish($millions) . ' Million' . 
                   ($remainder > 0 ? ' ' . self::convertIntegerToEnglish($remainder) : '');
        }

        return (string) $number; // Fallback for very large numbers
    }

    /**
     * Get currency names in Arabic
     */
    private static function getCurrencyNames()
    {
        return [
            'JOD' => ['main' => 'دينار أردني', 'fraction' => 'فلس'],
            'SAR' => ['main' => 'ريال سعودي', 'fraction' => 'هللة'],
            'USD' => ['main' => 'دولار أمريكي', 'fraction' => 'سنت'],
            'EUR' => ['main' => 'يورو', 'fraction' => 'سنت'],
            'AED' => ['main' => 'درهم إماراتي', 'fraction' => 'فلس'],
            'EGP' => ['main' => 'جنيه مصري', 'fraction' => 'قرش'],
            'QAR' => ['main' => 'ريال قطري', 'fraction' => 'درهم'],
            'KWD' => ['main' => 'دينار كويتي', 'fraction' => 'فلس'],
            'BHD' => ['main' => 'دينار بحريني', 'fraction' => 'فلس'],
            'OMR' => ['main' => 'ريال عماني', 'fraction' => 'بيسة'],
        ];
    }

    /**
     * Get currency names in English
     */
    private static function getCurrencyNamesEnglish()
    {
        return [
            'JOD' => ['main' => 'Jordanian Dinar', 'fraction' => 'Fils'],
            'SAR' => ['main' => 'Saudi Riyal', 'fraction' => 'Halala'],
            'USD' => ['main' => 'US Dollar', 'fraction' => 'Cent'],
            'EUR' => ['main' => 'Euro', 'fraction' => 'Cent'],
            'AED' => ['main' => 'UAE Dirham', 'fraction' => 'Fils'],
            'EGP' => ['main' => 'Egyptian Pound', 'fraction' => 'Piastre'],
            'QAR' => ['main' => 'Qatari Riyal', 'fraction' => 'Dirham'],
            'KWD' => ['main' => 'Kuwaiti Dinar', 'fraction' => 'Fils'],
            'BHD' => ['main' => 'Bahraini Dinar', 'fraction' => 'Fils'],
            'OMR' => ['main' => 'Omani Rial', 'fraction' => 'Baisa'],
        ];
    }
}
