<?php

namespace Database\Seeders;

use App\Models\PaymentTemplate;
use Illuminate\Database\Seeder;

class PaymentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'قالب شيك - عربي',
                'name_en' => 'Check Template - Arabic',
                'type' => 'check',
                'category' => 'general',
                'content' => $this->getCheckTemplateArabic(),
                'variables' => json_encode([
                    'company_name', 'check_number', 'check_date', 'amount', 'amount_words',
                    'currency_symbol', 'beneficiary', 'bank_name', 'account_number'
                ]),
                'styles' => $this->getDefaultStyles(),
                'is_default' => true,
                'language' => 'ar',
                'paper_size' => 'A4',
                'orientation' => 'landscape',
                'margins' => json_encode(['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20]),
                'status' => 'active',
            ],
            [
                'name' => 'قالب كمبيالة - عربي',
                'name_en' => 'Promissory Note Template - Arabic',
                'type' => 'promissory_note',
                'category' => 'general',
                'content' => $this->getPromissoryNoteTemplateArabic(),
                'variables' => json_encode([
                    'company_name', 'note_number', 'issue_date', 'maturity_date', 'amount',
                    'amount_words', 'issuer_name', 'payee_name', 'place_of_issue', 'purpose'
                ]),
                'styles' => $this->getDefaultStyles(),
                'is_default' => true,
                'language' => 'ar',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => json_encode(['top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30]),
                'status' => 'active',
            ],
            [
                'name' => 'قالب ضمان حسن تنفيذ - عربي',
                'name_en' => 'Performance Guarantee Template - Arabic',
                'type' => 'guarantee',
                'category' => 'performance',
                'content' => $this->getPerformanceGuaranteeTemplateArabic(),
                'variables' => json_encode([
                    'company_name', 'guarantee_number', 'contractor_name', 'project_name',
                    'amount', 'amount_words', 'start_date', 'end_date', 'bank_name', 'purpose'
                ]),
                'styles' => $this->getDefaultStyles(),
                'is_default' => true,
                'language' => 'ar',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => json_encode(['top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30]),
                'status' => 'active',
            ],
            [
                'name' => 'قالب ضمان دفعة مقدمة - عربي',
                'name_en' => 'Advance Payment Guarantee Template - Arabic',
                'type' => 'guarantee',
                'category' => 'advance_payment',
                'content' => $this->getAdvancePaymentGuaranteeTemplateArabic(),
                'variables' => json_encode([
                    'company_name', 'guarantee_number', 'contractor_name', 'project_name',
                    'amount', 'amount_words', 'start_date', 'end_date', 'bank_name', 'purpose'
                ]),
                'styles' => $this->getDefaultStyles(),
                'is_default' => true,
                'language' => 'ar',
                'paper_size' => 'A4',
                'orientation' => 'portrait',
                'margins' => json_encode(['top' => 30, 'right' => 30, 'bottom' => 30, 'left' => 30]),
                'status' => 'active',
            ],
        ];

        foreach ($templates as $template) {
            PaymentTemplate::updateOrCreate(
                [
                    'type' => $template['type'],
                    'category' => $template['category'],
                    'is_default' => true
                ],
                $template
            );
        }
    }

    private function getDefaultStyles()
    {
        return '
            body { font-family: "Arial", sans-serif; direction: rtl; }
            .header { text-align: center; margin-bottom: 30px; }
            .content { margin: 20px 0; }
            .footer { margin-top: 50px; text-align: center; }
            .amount { font-size: 18px; font-weight: bold; }
        ';
    }

    private function getCheckTemplateArabic()
    {
        return '
            <div class="header">
                <h1>{{company_name}}</h1>
                <h2>شيك بنكي</h2>
            </div>
            <div class="content">
                <p><strong>رقم الشيك:</strong> {{check_number}}</p>
                <p><strong>التاريخ:</strong> {{check_date}}</p>
                <p><strong>المستفيد:</strong> {{beneficiary}}</p>
                <p class="amount"><strong>المبلغ:</strong> {{currency_symbol}} {{amount}}</p>
                <p><strong>المبلغ بالكتابة:</strong> {{amount_words}}</p>
                <p><strong>البنك:</strong> {{bank_name}}</p>
                <p><strong>رقم الحساب:</strong> {{account_number}}</p>
            </div>
            <div class="footer">
                <p>التوقيع: _______________</p>
            </div>
        ';
    }

    private function getPromissoryNoteTemplateArabic()
    {
        return '
            <div class="header">
                <h1>كمبيالة</h1>
            </div>
            <div class="content">
                <p><strong>رقم الكمبيالة:</strong> {{note_number}}</p>
                <p><strong>تاريخ الإصدار:</strong> {{issue_date}}</p>
                <p><strong>تاريخ الاستحقاق:</strong> {{maturity_date}}</p>
                <p><strong>مكان الإصدار:</strong> {{place_of_issue}}</p>
                <p class="amount"><strong>المبلغ:</strong> {{amount}}</p>
                <p><strong>المبلغ بالكتابة:</strong> {{amount_words}}</p>
                <p>أتعهد أنا الموقع أدناه {{issuer_name}} بأن أدفع بموجب هذه الكمبيالة لأمر {{payee_name}} مبلغ وقدره {{amount_words}}.</p>
                <p><strong>الغرض:</strong> {{purpose}}</p>
            </div>
            <div class="footer">
                <p>اسم المصدر: {{issuer_name}}</p>
                <p>التوقيع: _______________</p>
            </div>
        ';
    }

    private function getPerformanceGuaranteeTemplateArabic()
    {
        return '
            <div class="header">
                <h1>{{company_name}}</h1>
                <h2>خطاب ضمان حسن تنفيذ</h2>
            </div>
            <div class="content">
                <p><strong>رقم الضمان:</strong> {{guarantee_number}}</p>
                <p><strong>المقاول:</strong> {{contractor_name}}</p>
                <p><strong>المشروع:</strong> {{project_name}}</p>
                <p class="amount"><strong>قيمة الضمان:</strong> {{amount}}</p>
                <p><strong>المبلغ بالكتابة:</strong> {{amount_words}}</p>
                <p><strong>تاريخ البداية:</strong> {{start_date}}</p>
                <p><strong>تاريخ الانتهاء:</strong> {{end_date}}</p>
                <p><strong>البنك المصدر:</strong> {{bank_name}}</p>
                <p><strong>الغرض:</strong> {{purpose}}</p>
                <p>يضمن هذا الخطاب حسن تنفيذ المقاول لجميع التزاماته التعاقدية المتعلقة بالمشروع المذكور أعلاه.</p>
            </div>
            <div class="footer">
                <p>التوقيع والختم</p>
            </div>
        ';
    }

    private function getAdvancePaymentGuaranteeTemplateArabic()
    {
        return '
            <div class="header">
                <h1>{{company_name}}</h1>
                <h2>خطاب ضمان دفعة مقدمة</h2>
            </div>
            <div class="content">
                <p><strong>رقم الضمان:</strong> {{guarantee_number}}</p>
                <p><strong>المقاول:</strong> {{contractor_name}}</p>
                <p><strong>المشروع:</strong> {{project_name}}</p>
                <p class="amount"><strong>قيمة الضمان:</strong> {{amount}}</p>
                <p><strong>المبلغ بالكتابة:</strong> {{amount_words}}</p>
                <p><strong>تاريخ البداية:</strong> {{start_date}}</p>
                <p><strong>تاريخ الانتهاء:</strong> {{end_date}}</p>
                <p><strong>البنك المصدر:</strong> {{bank_name}}</p>
                <p><strong>الغرض:</strong> {{purpose}}</p>
                <p>يضمن هذا الخطاب استرداد الدفعة المقدمة في حال عدم التزام المقاول بتنفيذ الأعمال المتعاقد عليها.</p>
            </div>
            <div class="footer">
                <p>التوقيع والختم</p>
            </div>
        ';
    }
}
