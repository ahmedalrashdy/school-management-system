<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $school = school();

        // إحصائيات حية من قاعدة البيانات
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_graduates' => 3420, // يمكن جلبها من قاعدة البيانات لاحقاً
        ];

        // بيانات المدرسة من إعدادات النظام
        $schoolData = [
            'name' => $school->schoolSetting('school_name', config('app.name')),
            'email' => $school->schoolSetting('school_email', ''),
            'phone' => $school->schoolSetting('school_phone', ''),
            'address' => $school->schoolSetting('school_address', ''),
            'social_links' => $school->getArrayData('social_links', []),
        ];

        // أخبار وفعاليات (بيانات تجريبية - يمكن استبدالها بجدول في قاعدة البيانات)
        $news = [
            [
                'title' => 'رحلة علمية لطلاب الصف الثالث',
                'date' => '2024-12-15',
                'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800',
                'excerpt' => 'نظمت المدرسة رحلة علمية مميزة لطلاب الصف الثالث إلى المتحف العلمي',
            ],
            [
                'title' => 'مسابقة القرآن الكريم',
                'date' => '2024-12-10',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800',
                'excerpt' => 'اختتمت مسابقة القرآن الكريم السنوية بتكريم الفائزين',
            ],
            [
                'title' => 'تكريم الأوائل للفصل الدراسي الأول',
                'date' => '2024-12-05',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800',
                'excerpt' => 'احتفلت المدرسة بتكريم الطلاب المتفوقين في الفصل الدراسي الأول',
            ],
        ];

        // المرافق (بيانات تجريبية)
        $facilities = [
            [
                'name' => 'المختبرات العلمية',
                'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=600',
                'description' => 'مختبرات مجهزة بأحدث الأجهزة العلمية',
            ],
            [
                'name' => 'المكتبة',
                'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600',
                'description' => 'مكتبة شاملة تضم آلاف الكتب والمراجع',
            ],
            [
                'name' => 'الملاعب الرياضية',
                'image' => 'https://images.unsplash.com/photo-1576678927484-cc907957088c?w=600',
                'description' => 'ملاعب متعددة الأغراض لجميع الأنشطة الرياضية',
            ],
            [
                'name' => 'القاعات الدراسية',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600',
                'description' => 'قاعات دراسية واسعة ومجهزة بأحدث التقنيات',
            ],
        ];

        return view('home', [
            'stats' => $stats,
            'news' => $news,
            'facilities' => $facilities,
            'school' => $schoolData,
        ]);
    }
}
