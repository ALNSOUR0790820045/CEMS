<?php

namespace Tests\Unit;

use App\Services\AmountToWordsService;
use Tests\TestCase;

class AmountToWordsServiceTest extends TestCase
{
    /**
     * Test Arabic conversion for JOD (3 decimals)
     */
    public function test_arabic_conversion_jod()
    {
        $result = AmountToWordsService::convertToArabic(15000.500, 'JOD', 3);
        
        $this->assertStringContainsString('خمسة عشر', $result);
        $this->assertStringContainsString('ألف', $result);
        $this->assertStringContainsString('دينار أردني', $result);
        $this->assertStringContainsString('فلس', $result);
        $this->assertStringContainsString('فقط لا غير', $result);
    }

    /**
     * Test Arabic conversion for SAR (2 decimals)
     */
    public function test_arabic_conversion_sar()
    {
        $result = AmountToWordsService::convertToArabic(50000.00, 'SAR', 2);
        
        $this->assertStringContainsString('خمسون', $result);
        $this->assertStringContainsString('ألف', $result);
        $this->assertStringContainsString('ريال سعودي', $result);
        $this->assertStringContainsString('فقط لا غير', $result);
    }

    /**
     * Test English conversion for USD
     */
    public function test_english_conversion_usd()
    {
        $result = AmountToWordsService::convertToEnglish(25000.00, 'USD', 2);
        
        $this->assertStringContainsString('Twenty', $result);
        $this->assertStringContainsString('Five', $result);
        $this->assertStringContainsString('Thousand', $result);
        $this->assertStringContainsString('US Dollar', $result);
        $this->assertStringContainsString('Only', $result);
    }

    /**
     * Test Arabic conversion for small amount
     */
    public function test_arabic_conversion_small_amount()
    {
        $result = AmountToWordsService::convertToArabic(15, 'SAR', 2);
        
        $this->assertStringContainsString('خمسة عشر', $result);
        $this->assertStringContainsString('ريال سعودي', $result);
    }

    /**
     * Test English conversion for small amount
     */
    public function test_english_conversion_small_amount()
    {
        $result = AmountToWordsService::convertToEnglish(99, 'USD', 2);
        
        $this->assertStringContainsString('Ninety', $result);
        $this->assertStringContainsString('Nine', $result);
        $this->assertStringContainsString('US Dollar', $result);
    }

    /**
     * Test Arabic conversion for hundreds
     */
    public function test_arabic_conversion_hundreds()
    {
        $result = AmountToWordsService::convertToArabic(500, 'SAR', 2);
        
        $this->assertStringContainsString('خمسمئة', $result);
        $this->assertStringContainsString('ريال سعودي', $result);
    }

    /**
     * Test English conversion for millions
     */
    public function test_english_conversion_millions()
    {
        $result = AmountToWordsService::convertToEnglish(1000000, 'USD', 2);
        
        $this->assertStringContainsString('One', $result);
        $this->assertStringContainsString('Million', $result);
        $this->assertStringContainsString('US Dollar', $result);
    }

    /**
     * Test zero amount
     */
    public function test_zero_amount_arabic()
    {
        $result = AmountToWordsService::convertToArabic(0, 'JOD', 3);
        
        $this->assertStringContainsString('صفر', $result);
        $this->assertStringContainsString('دينار أردني', $result);
    }

    /**
     * Test zero amount English
     */
    public function test_zero_amount_english()
    {
        $result = AmountToWordsService::convertToEnglish(0, 'USD', 2);
        
        $this->assertStringContainsString('Zero', $result);
        $this->assertStringContainsString('US Dollar', $result);
    }

    /**
     * Test with decimal fraction
     */
    public function test_arabic_with_fraction()
    {
        $result = AmountToWordsService::convertToArabic(1234.567, 'JOD', 3);
        
        $this->assertStringContainsString('ألف', $result);
        $this->assertStringContainsString('مئتان', $result);
        $this->assertStringContainsString('دينار أردني', $result);
        $this->assertStringContainsString('فلس', $result);
    }

    /**
     * Test various currencies
     */
    public function test_multiple_currencies()
    {
        $currencies = ['JOD', 'SAR', 'USD', 'EUR', 'AED', 'KWD', 'BHD'];
        
        foreach ($currencies as $currency) {
            $resultAr = AmountToWordsService::convertToArabic(1000, $currency, 2);
            $resultEn = AmountToWordsService::convertToEnglish(1000, $currency, 2);
            
            $this->assertNotEmpty($resultAr);
            $this->assertNotEmpty($resultEn);
            $this->assertStringContainsString('فقط لا غير', $resultAr);
            $this->assertStringContainsString('Only', $resultEn);
        }
    }
}
