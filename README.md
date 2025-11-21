# نظام إدارة مدرسة (School Management System)

نظام لإدارة البيانات الأكاديمية والطلابية داخل المدرسة، يشمل الهيكل الأكاديمي، إدارة الطلاب والمعلمين وأولياء الأمور، الجداول الدراسية، الحضور، والدرجات والتقارير.

## نظرة عامة سريعة
- إدارة السنوات الدراسية والفصول (الترم) والمراحل والصفوف والشعب.
- إدارة المستخدمين (طلاب/معلمين/أولياء أمور) وربطهم بالبيانات الأكاديمية.
- بناء المناهج وتوزيع المواد على الصفوف وربط المعلمين بالشعب.
- جدولة الحصص وإعدادات الجدول الزمني.
- متابعة الحضور (يومي/حسب الحصص/جزئي).
- إدارة الاختبارات والدرجات وقواعد الاحتساب وإصدار الكشوف.

## التقنيات المستخدمة
- Laravel 12 + PHP 8.2+
- Livewire 3
- Tailwind CSS 4 + Alpine.js
- SQLite (افتراضياً) مع إمكانية تغيير قاعدة البيانات من `.env`
- Spatie (Permissions + Activity Log + PDF)

## نطاقات البيانات (من `tables.php`)
### الهيكل الأكاديمي
- `academic_years` سنوات دراسية.
- `academic_terms` فصول دراسية مرتبطة بسنة.
- `stages` مراحل دراسية.
- `grades` صفوف مرتبطة بمراحل.
- `sections` شعب مرتبطة بسنة/صف/ترم.
- `section_students` ربط الطالب بالشعبة.
- `enrollments` تسجيل الطالب في سنة/صف.

### الأشخاص
- `users` الحسابات الأساسية (طالب/ولي أمر/معلم/إدارة).
- `students` بيانات الطالب الأكاديمية.
- `guardians` بيانات ولي الأمر.
- `guardian_student` علاقة الطالب بولي الأمر.
- `teachers` بيانات المعلم الأكاديمية.

### المناهج والمواد والتكليفات
- `subjects` المواد.
- `curriculums` المنهج حسب (سنة/صف/ترم).
- `curriculum_subject` ربط المواد بالمنهج.
- `teacher_assignments` تعيين المعلم لمادة داخل شعبة.

### الجداول الدراسية
- `timetable_settings` إعدادات الجدول (عدد الحصص/الراحة...).
- `timetables` الجداول لكل شعبة.
- `timetable_slots` الحصص التفصيلية (يوم/حصة/معلم/مادة).

### الحضور والانصراف
- `school_days` أيام الدراسة وحالتها (دوام/إجازة/جزئي).
- `attendance_sheets` سندات التحضير (حسب الوضع).
- `attendances` حضور الطلاب الفعلي.

### الاختبارات والدرجات
- `exam_types` أنواع الاختبارات.
- `exams` الاختبارات وربطها بالمناهج/الشعب.
- `marks` درجات الطلاب.
- `grading_rules` قواعد احتساب الدرجات لكل شعبة ومادة.
- `grading_rule_items` بنود أعمال السنة ونِسبها.

### إعدادات المدرسة
- `school_settings` مفاتيح إعدادات عامة (مثل أوضاع الحضور وعطلة نهاية الأسبوع...).

## الخدمات الأساسية (app/Services)
### سياق المدرسة والإعدادات
- `app/Services/SchoolContextService.php`:
  - السنة الدراسية النشطة والقادمة.
  - الترم النشط.
  - إعدادات المدرسة (JSON/Array).
  - وضع الحضور الحالي.

### توليد أيام الدراسة
- `app/Services/SchoolDayService.php`:
  - توليد أيام الدراسة تلقائياً من نطاق الترم.
  - مزامنة التعديلات مع حفظ السجلات.

### الحضور
- `app/Services/Attendances/AttendanceSheetService.php`:
  - إنشاء/جلب سجل الحضور بحسب وضع الحضور:
    - `PerPeriod` (حسب الحصة)
    - `Daily` (يومي)
    - `SplitDaily` (نصف يوم)
- `app/Services/Attendances/AttendanceTrackingService.php`:
  - إحصاءات يومية أو حسب الحصص.
- `app/Services/Attendances/AttendanceReportService.php`:
  - إحصاءات وتجميعات الحضور على مستوى الأيام والشعب.
- `app/Services/Attendances/AttendanceSectionService.php`:
  - تقارير تفصيلية لحضور طالب/شعبة.

### الدرجات والكشوف
- `app/Services/Marksheets/GradingDataRepository.php`:
  - بناء هيكل المنهج وقواعد الاحتساب وجلب الدرجات.
- `app/Services/Marksheets/MarkCalculatorService.php`:
  - حساب النتائج النهائية والنسب والتقديرات.
- `app/Services/Marksheets/SectionMarksheetService.php`:
  - كشف شعبة كامل لجميع الطلاب.
- `app/Services/Marksheets/StudentMarksheetService.php`:
  - كشف تفصيلي لطالب واحد.

### خدمات مساعدة
- `app/Services/LookupService.php`:
  - جلب القوائم المرجعية (سنوات/مراحل/صفوف).
- `app/helpers.php`:
  - `school()` للوصول لسياق المدرسة.
  - `lookup()` للقوائم المرجعية.

## مداخل التطبيق المهمة
- `routes/web.php` المسارات الأساسية + التنبيهات.
- `routes/auth.php` مصادقة المستخدمين (Laravel Breeze).
- `app/Livewire` مكونات الواجهة (مثل إحصائيات الحضور).
- `tables.php` مرجع موحد لكل الجداول والعلاقات الأساسية.

## التهيئة والتشغيل
### المتطلبات
- PHP 8.2+
- Composer
- Node.js + npm

### الإعداد السريع
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run dev
```

### إعداد SQLite (افتراضي)
```bash
touch database/database.sqlite
```
ثم عدّل `.env`:
```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### أوامر مفيدة
- `composer run setup` إعداد كامل (مهاجرات + npm build).
- `composer run dev` تشغيل بيئة التطوير (خادم + queue + vite).
- `composer run test` تشغيل الاختبارات.

## ملاحظات هندسية مهمة
- وضع الحضور يُقرأ من إعداد `attendence_mode` في جدول `school_settings`.
- أيام نهاية الأسبوع تُقرأ من `weekend_days` في الإعدادات.
- الحضور الجزئي يعتمد على `day_part` في `school_days`.
- قيود فريدة كثيرة لضمان سلامة البيانات (مثل عدم تكرار الطلاب في نفس الشعبة).

## هيكل المجلدات (مختصر)
- `app/Models` نماذج البيانات.
- `app/Services` منطق الأعمال الرئيسي.
- `app/Livewire` مكونات الواجهة التفاعلية.
- `database/migrations` ملفات الترحيل الأصلية.
- `resources/views` واجهات Blade/Livewire.

---

إذا أردت توسيع الـ README بأمثلة تشغيل محددة أو وصف واجهات/شاشات إضافية، أخبرني بما تريد تفصيله.
