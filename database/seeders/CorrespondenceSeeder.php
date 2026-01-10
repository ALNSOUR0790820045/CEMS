<?php

namespace Database\Seeders;

use App\Models\Correspondence;
use App\Models\CorrespondenceRegister;
use App\Models\CorrespondenceTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class CorrespondenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user if not exists
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
                'password' => bcrypt('password'),
            ]
        );

        // Create registers for 2026
        $incomingRegister = CorrespondenceRegister::create([
            'register_number' => 'IN-2026',
            'name' => 'سجل الوارد 2026',
            'type' => 'incoming',
            'year' => 2026,
            'prefix' => 'IN',
            'last_sequence' => 0,
            'is_active' => true,
        ]);

        $outgoingRegister = CorrespondenceRegister::create([
            'register_number' => 'OUT-2026',
            'name' => 'سجل الصادر 2026',
            'type' => 'outgoing',
            'year' => 2026,
            'prefix' => 'OUT',
            'last_sequence' => 0,
            'is_active' => true,
        ]);

        // Create sample templates
        CorrespondenceTemplate::create([
            'name' => 'خطاب رسمي',
            'name_en' => 'Official Letter',
            'type' => 'outgoing',
            'category' => 'letter',
            'subject_template' => 'خطاب بشأن: [الموضوع]',
            'content_template' => "السادة/ [الجهة]\nتحية طيبة وبعد،\n\n[المحتوى]\n\nوتفضلوا بقبول فائق الاحترام والتقدير",
            'is_active' => true,
        ]);

        CorrespondenceTemplate::create([
            'name' => 'مذكرة داخلية',
            'name_en' => 'Internal Memo',
            'type' => 'outgoing',
            'category' => 'memo',
            'subject_template' => 'مذكرة بخصوص: [الموضوع]',
            'content_template' => "إلى/ [الجهة]\n\nالموضوع: [الموضوع]\n\n[المحتوى]",
            'is_active' => true,
        ]);

        // Create sample incoming correspondence
        for ($i = 1; $i <= 5; $i++) {
            Correspondence::create([
                'reference_number' => $incomingRegister->generateReferenceNumber(),
                'type' => 'incoming',
                'category' => ['letter', 'memo', 'request', 'notice'][$i % 4],
                'priority' => ['normal', 'urgent', 'very_urgent'][$i % 3],
                'subject' => "مراسلة واردة رقم $i - " . ['طلب معلومات', 'استفسار عن مشروع', 'طلب اجتماع', 'طلب موافقة', 'تقرير دوري'][$i % 5],
                'summary' => "ملخص المراسلة الواردة رقم $i",
                'content' => "محتوى المراسلة الواردة رقم $i.\n\nيتضمن هذا المحتوى تفاصيل المراسلة والمعلومات المطلوبة.",
                'from_entity' => ['وزارة التجارة', 'الهيئة العامة للاستثمار', 'شركة المقاولات الكبرى', 'مكتب الاستشارات الهندسية', 'البلدية'][$i % 5],
                'from_person' => "المهندس محمد أحمد",
                'from_position' => "مدير المشاريع",
                'to_entity' => "شركة سيمز للمقاولات",
                'to_person' => "المهندس خالد سعيد",
                'to_position' => "المدير العام",
                'document_date' => now()->subDays($i),
                'received_date' => now()->subDays($i),
                'response_required_date' => $i <= 2 ? now()->addDays(7 - $i) : null,
                'status' => $i === 1 ? 'pending_response' : ($i === 2 ? 'received' : 'responded'),
                'requires_response' => $i <= 3,
                'is_confidential' => $i === 1,
                'created_by' => $user->id,
            ]);
        }

        // Create sample outgoing correspondence
        for ($i = 1; $i <= 5; $i++) {
            Correspondence::create([
                'reference_number' => $outgoingRegister->generateReferenceNumber(),
                'type' => 'outgoing',
                'category' => ['letter', 'memo', 'approval', 'report'][$i % 4],
                'priority' => ['normal', 'urgent'][$i % 2],
                'subject' => "مراسلة صادرة رقم $i - " . ['موافقة على المشروع', 'تقرير الإنجاز', 'خطاب رسمي', 'طلب معلومات إضافية', 'تأكيد الموعد'][$i % 5],
                'summary' => "ملخص المراسلة الصادرة رقم $i",
                'content' => "محتوى المراسلة الصادرة رقم $i.\n\nنفيدكم بأنه تم الموافقة/الرد على طلبكم.",
                'from_entity' => "شركة سيمز للمقاولات",
                'from_person' => "المهندس خالد سعيد",
                'from_position' => "المدير العام",
                'to_entity' => ['وزارة التجارة', 'الهيئة العامة للاستثمار', 'شركة المقاولات الكبرى', 'البلدية', 'العميل'][$i % 5],
                'to_person' => "المهندس أحمد محمد",
                'to_position' => "مدير الإدارة",
                'document_date' => now()->subDays($i - 1),
                'sent_date' => $i > 2 ? now()->subDays($i - 2) : null,
                'status' => $i > 2 ? 'sent' : ($i === 2 ? 'approved' : 'draft'),
                'requires_response' => false,
                'is_confidential' => false,
                'created_by' => $user->id,
                'approved_by' => $i > 1 ? $user->id : null,
            ]);
        }
    }
}

