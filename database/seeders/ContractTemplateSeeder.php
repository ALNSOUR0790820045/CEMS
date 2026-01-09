<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractTemplate;
use App\Models\ContractTemplateClause;
use App\Models\ContractTemplateVariable;
use App\Models\ContractTemplateSpecialCondition;

class ContractTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // JEA-01 Template
        $jea01 = ContractTemplate::create([
            'code' => 'JEA-01',
            'name' => 'عقد نقابة المقاولين الأردنيين - أعمال البناء',
            'name_en' => 'Jordanian Engineers Association Contract - Construction Works',
            'type' => 'jea_01',
            'version' => '2024',
            'year' => 2024,
            'description' => 'عقد موحد معتمد من نقابة المقاولين الأردنيين لأعمال البناء والتشييد. يشمل جميع البنود القانونية والفنية المطلوبة لتنظيم العلاقة بين المقاول وصاحب العمل.',
            'is_active' => true,
        ]);

        // JEA-01 Clauses
        $clauses = [
            [
                'clause_number' => '1.1',
                'title' => 'التعاريف والتفسيرات',
                'title_en' => 'Definitions and Interpretations',
                'content' => 'في هذا العقد تكون للكلمات والتعابير التالية المعاني المبينة قرين كل منها ما لم يقتض السياق خلاف ذلك.',
                'category' => 'general',
                'sort_order' => 1,
            ],
            [
                'clause_number' => '2.1',
                'title' => 'التزامات المقاول',
                'title_en' => 'Contractor Obligations',
                'content' => 'يلتزم المقاول بتنفيذ وإتمام الأعمال وإصلاح أية عيوب فيها وفقاً للعقد وتعليمات المهندس.',
                'category' => 'contractor_obligations',
                'sort_order' => 2,
            ],
            [
                'clause_number' => '4.1',
                'title' => 'مدة التنفيذ',
                'title_en' => 'Time for Completion',
                'content' => 'يجب على المقاول البدء في تنفيذ الأعمال خلال المدة المحددة في ملحق العطاء وإنجاز الأعمال ضمن المدة المحددة.',
                'category' => 'time',
                'has_time_bar' => true,
                'time_bar_days' => 28,
                'time_bar_description' => 'يجب على المقاول تقديم طلب التمديد خلال 28 يوماً من حدوث التأخير وإلا سقط حقه في المطالبة.',
                'sort_order' => 3,
            ],
            [
                'clause_number' => '5.1',
                'title' => 'المستخلصات والدفعات',
                'title_en' => 'Payment Certificates',
                'content' => 'يقدم المقاول طلبات الدفع الشهرية إلى المهندس للموافقة عليها وإصدار شهادات الدفع المؤقتة.',
                'category' => 'payment',
                'sort_order' => 4,
            ],
            [
                'clause_number' => '13.1',
                'title' => 'التغييرات والأوامر الإضافية',
                'title_en' => 'Variations and Additional Orders',
                'content' => 'يحق للمهندس إصدار أوامر تغيير في أي وقت قبل أو أثناء تنفيذ الأعمال.',
                'category' => 'variations',
                'sort_order' => 5,
            ],
            [
                'clause_number' => '20.1',
                'title' => 'المطالبات',
                'title_en' => 'Claims',
                'content' => 'إذا اعتبر المقاول أن له الحق في الحصول على وقت إضافي أو أموال إضافية، عليه تقديم إشعار بذلك.',
                'category' => 'claims',
                'has_time_bar' => true,
                'time_bar_days' => 28,
                'time_bar_description' => 'يجب على المقاول إرسال إشعار خطي خلال 28 يوماً من معرفته بالحدث أو ظرف المطالبة.',
                'sort_order' => 6,
            ],
        ];

        foreach ($clauses as $clauseData) {
            $clauseData['template_id'] = $jea01->id;
            ContractTemplateClause::create($clauseData);
        }

        // JEA-01 Variables
        $variables = [
            [
                'variable_key' => '{{employer_name}}',
                'variable_label' => 'اسم صاحب العمل',
                'variable_label_en' => 'Employer Name',
                'data_type' => 'text',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{contractor_name}}',
                'variable_label' => 'اسم المقاول',
                'variable_label_en' => 'Contractor Name',
                'data_type' => 'text',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{contract_value}}',
                'variable_label' => 'قيمة العقد',
                'variable_label_en' => 'Contract Value',
                'data_type' => 'currency',
                'is_required' => true,
                'description' => 'قيمة العقد بالدينار الأردني',
            ],
            [
                'variable_key' => '{{start_date}}',
                'variable_label' => 'تاريخ البدء',
                'variable_label_en' => 'Start Date',
                'data_type' => 'date',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{completion_period}}',
                'variable_label' => 'مدة التنفيذ (بالأيام)',
                'variable_label_en' => 'Completion Period (Days)',
                'data_type' => 'number',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{retention_percentage}}',
                'variable_label' => 'نسبة المحبوسات',
                'variable_label_en' => 'Retention Percentage',
                'data_type' => 'percentage',
                'is_required' => true,
                'default_value' => '10',
            ],
        ];

        foreach ($variables as $variableData) {
            $variableData['template_id'] = $jea01->id;
            ContractTemplateVariable::create($variableData);
        }

        // JEA-01 Special Conditions
        ContractTemplateSpecialCondition::create([
            'template_id' => $jea01->id,
            'condition_number' => 'SC1',
            'title' => 'ساعات العمل',
            'content' => 'يجب أن يكون العمل في الموقع من الساعة 7 صباحاً حتى 5 مساءً من الأحد إلى الخميس.',
            'modifies_clause' => '2.1',
            'sort_order' => 1,
        ]);

        // JEA-02 Template
        $jea02 = ContractTemplate::create([
            'code' => 'JEA-02',
            'name' => 'عقد نقابة المقاولين الأردنيين - الأعمال الميكانيكية',
            'name_en' => 'Jordanian Engineers Association Contract - Mechanical Works',
            'type' => 'jea_02',
            'version' => '2024',
            'year' => 2024,
            'description' => 'عقد موحد معتمد من نقابة المقاولين الأردنيين للأعمال الميكانيكية والكهربائية. يشمل بنوداً خاصة بالتركيبات الميكانيكية وأنظمة التكييف.',
            'is_active' => true,
        ]);

        // JEA-02 Clauses
        $clausesJea02 = [
            [
                'clause_number' => '1.1',
                'title' => 'التعاريف',
                'title_en' => 'Definitions',
                'content' => 'في هذا العقد تكون للكلمات والتعابير التالية المعاني المبينة قرين كل منها.',
                'category' => 'general',
                'sort_order' => 1,
            ],
            [
                'clause_number' => '2.1',
                'title' => 'التزامات المقاول الميكانيكي',
                'title_en' => 'Mechanical Contractor Obligations',
                'content' => 'يلتزم المقاول بتوريد وتركيب جميع الأنظمة الميكانيكية والكهربائية وفقاً للمواصفات الفنية.',
                'category' => 'contractor_obligations',
                'sort_order' => 2,
            ],
            [
                'clause_number' => '3.1',
                'title' => 'الاختبارات والتشغيل',
                'title_en' => 'Testing and Commissioning',
                'content' => 'يقوم المقاول بإجراء جميع الاختبارات اللازمة للتأكد من سلامة التركيبات وكفاءة التشغيل.',
                'category' => 'contractor_obligations',
                'sort_order' => 3,
            ],
            [
                'clause_number' => '4.1',
                'title' => 'فترة الصيانة',
                'title_en' => 'Maintenance Period',
                'content' => 'يلتزم المقاول بصيانة الأنظمة لمدة سنة واحدة من تاريخ الاستلام النهائي.',
                'category' => 'contractor_obligations',
                'sort_order' => 4,
            ],
        ];

        foreach ($clausesJea02 as $clauseData) {
            $clauseData['template_id'] = $jea02->id;
            ContractTemplateClause::create($clauseData);
        }

        // JEA-02 Variables
        $variablesJea02 = [
            [
                'variable_key' => '{{employer_name}}',
                'variable_label' => 'اسم صاحب العمل',
                'variable_label_en' => 'Employer Name',
                'data_type' => 'text',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{contractor_name}}',
                'variable_label' => 'اسم المقاول',
                'variable_label_en' => 'Contractor Name',
                'data_type' => 'text',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{contract_value}}',
                'variable_label' => 'قيمة العقد',
                'variable_label_en' => 'Contract Value',
                'data_type' => 'currency',
                'is_required' => true,
            ],
            [
                'variable_key' => '{{warranty_period}}',
                'variable_label' => 'فترة الضمان (بالأشهر)',
                'variable_label_en' => 'Warranty Period (Months)',
                'data_type' => 'number',
                'is_required' => true,
                'default_value' => '12',
            ],
        ];

        foreach ($variablesJea02 as $variableData) {
            $variableData['template_id'] = $jea02->id;
            ContractTemplateVariable::create($variableData);
        }
    }
}
