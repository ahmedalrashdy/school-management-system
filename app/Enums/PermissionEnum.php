<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // ==========================
    // 1. صلاحيات الوصول الأساسية
    // ==========================
    case AccessAdminPanel = 'access-admin-panel'; // السماح بالدخول للوحة التحكم
    case DashboardAccess = 'dashboard.access'; // السماح بالدخول للوحة التحكم

    // ==========================
    // 2. إعدادات النظام والمدرسة
    // ==========================
    case SettingsManage = 'settings.manage'; // إدارة إعدادات المدرسة (school_settings)
    case ActivityLogView = 'activity-log.view'; // عرض سجل النشاطات

    // ==========================
    // 3. إدارة المستخدمين والهيكل الإداري
    // ==========================
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersUpdate = 'users.update';
    case UsersDelete = 'users.delete';
    case UsersManageRoles = 'users.manage-roles';

    case RolesView = 'roles.view';
    case RolesCreate = 'roles.create';
    case RolesUpdate = 'roles.update';
    case RolesDelete = 'roles.delete';

    // ==========================
    // 4. الهيكل الأكاديمي (السنوات، المراحل، الصفوف)
    // ==========================
    case AcademicYearsView = 'academic-years.view';
    case AcademicYearsCreate = 'academic-years.create';
    case AcademicYearsUpdate = 'academic-years.update';
    case AcademicYearsDelete = 'academic-years.delete';
    case AcademicYearsActivate = 'academic-years.activate';

    case StagesView = 'stages.view';
    case StagesCreate = 'stages.create';
    case StagesUpdate = 'stages.update';
    case StagesDelete = 'stages.delete';

    case GradesView = 'grades.view';
    case GradesCreate = 'grades.create';
    case GradesUpdate = 'grades.update';
    case GradesDelete = 'grades.delete';

    case SectionsView = 'sections.view';
    case SectionsCreate = 'sections.create';
    case SectionsUpdate = 'sections.update';
    case SectionsDelete = 'sections.delete';
    case SectionsViewStudents = 'sections.view-students'; // عرض طلاب الشعبة

    case AcademicTermsView = 'academic-terms.view';
    case AcademicTermsCreate = 'academic-terms.create';
    case AcademicTermsUpdate = 'academic-terms.update';
    case AcademicTermsDelete = 'academic-terms.delete';
    case AcademicTermsActivate = 'academic-terms.activate';

    // ==========================
    // 5. إدارة المناهج والمواد
    // ==========================
    case SubjectsView = 'subjects.view';
    case SubjectsCreate = 'subjects.create';
    case SubjectsUpdate = 'subjects.update';
    case SubjectsDelete = 'subjects.delete';

    case CurriculumsView = 'curriculums.view';
    case CurriculumsCreate = 'curriculums.create';
    case CurriculumsUpdate = 'curriculums.update';
    case CurriculumsDelete = 'curriculums.delete';
    // يغطي جدول curriculum_subject
    case CurriculumsManageSubjects = 'curriculums.manage-subjects';

    // ==========================
    // 6. إدارة الأشخاص (الطلاب، المعلمين، الأولياء)
    // ==========================
    case StudentsView = 'students.view';
    case StudentsCreate = 'students.create';
    case StudentsUpdate = 'students.update';
    case StudentsDelete = 'students.delete';
    case StudentsManageGuardians = 'students.manage-guardians'; // جدول guardian_student

    case GuardiansView = 'guardians.view';
    case GuardiansCreate = 'guardians.create';
    case GuardiansUpdate = 'guardians.update';
    case GuardiansDelete = 'guardians.delete';

    case TeachersView = 'teachers.view';
    case TeachersCreate = 'teachers.create';
    case TeachersUpdate = 'teachers.update';
    case TeachersDelete = 'teachers.delete';

    // جدول teacher_assignments (توزيع المعلمين على المواد والشعب)
    case TeacherAssignmentsView = 'teacher-assignments.view';
    case TeacherAssignmentsCreate = 'teacher-assignments.create';
    case TeacherAssignmentsUpdate = 'teacher-assignments.update';
    case TeacherAssignmentsDelete = 'teacher-assignments.delete';

    // ==========================
    // 7. إدارة الجداول الدراسية والتقويم
    // ==========================
    // جدول timetable_settings (مهم جداً: إعدادات وقت الحصة، الفسحة)
    case TimetableSettingsManage = 'timetable-settings.manage';

    case TimetablesView = 'timetables.view';
    case TimetablesCreate = 'timetables.create'; // إنشاء هيكل الجدول
    case TimetablesUpdate = 'timetables.update'; // تعديل الحصص داخل الجدول
    case TimetablesDelete = 'timetables.delete';
    case TimetablesActivate = 'timetables.activate'; // تفعيل جدول معين للشعبة

    // جدول school_days (التقويم الدراسي، الإجازات، الأيام الدراسية)
    case SchoolDaysView = 'school-days.view';
    case SchoolDaysManage = 'school-days.manage'; // إنشاء أيام أو تحديد إجازات

    // ==========================
    // 8. الحضور والغياب
    // ==========================
    case AttendanceSheetsView = 'attendance-sheets.view'; // عرض كشوف التحضير
    case AttendanceSheetsCreate = 'attendance-sheets.create'; // إنشاء كشف جديد (فتح التحضير)

    case AttendancesTake = 'attendances.take'; // (للمعلم: تسجيل حضور طلابه)
    case AttendancesUpdate = 'attendances.update'; // (للمشرف: تعديل حالة بعد التسجيل)
    case AttendancesReport = 'attendances.report'; // عرض تقارير الحضور

    // ==========================
    // 9. الامتحانات وقواعد الدرجات
    // ==========================
    case ExamTypesManage = 'exam-types.manage'; // إدارة أنواع الامتحانات (نصفي، نهائي...)



    case GradingRulesView = 'grading-rules.view';
    case GradingRulesCreate = 'grading-rules.create';
    case GradingRulesUpdate = 'grading-rules.update';
    case GradingRulesDelete = 'grading-rules.delete';

    case ExamsView = 'exams.view';
    case ExamsCreate = 'exams.create'; // جدولة امتحان
    case ExamsUpdate = 'exams.update';
    case ExamsDelete = 'exams.delete';

    case MarksView = 'marks.view';
    case MarksEnter = 'marks.enter';
    case MarksUpdate = 'marks.update'; // (للمشرف: تعديل درجة بعد الاعتماد)

    // ==========================
    // 10. التقارير والمالية (مستقبلاً)
    // ==========================
    case ReportsAcademic = 'reports.academic';
    case ReportsFinancial = 'reports.financial';
    case ReportsAttendance = 'reports.attendance';

    /**
     * Get the permission label in Arabic.
     */
    public function label(): string
    {
        return match ($this) {
            self::AccessAdminPanel => 'الدخول الى لوحة الإدارة',
            self::DashboardAccess => 'الدخول الى صفحة التحكم',
            self::SettingsManage => 'إدارة إعدادات النظام',
            self::ActivityLogView => 'عرض سجل النشاطات',

            self::UsersView => 'عرض المستخدمين',
            self::UsersCreate => 'إنشاء مستخدمين',
            self::UsersUpdate => 'تعديل المستخدمين',
            self::UsersDelete => 'حذف المستخدمين',
            self::UsersManageRoles => 'إدارة صلاحيات المستخدمين',

            self::RolesView => 'عرض الأدوار',
            self::RolesCreate => 'إنشاء دور جديد',
            self::RolesUpdate => 'تعديل الأدوار',
            self::RolesDelete => 'حذف الأدوار',

            self::AcademicYearsView => 'عرض السنوات الدراسية',
            self::AcademicYearsCreate => 'إنشاء سنة دراسية',
            self::AcademicYearsUpdate => 'تعديل سنة دراسية',
            self::AcademicYearsDelete => 'حذف سنة دراسية',
            self::AcademicYearsActivate => 'تفعيل/تغيير  حالة السنة الدراسية',

            self::AcademicTermsView => 'عرض الفصول الدراسية (الترم)',
            self::AcademicTermsCreate => 'إنشاء فصل دراسي',
            self::AcademicTermsUpdate => 'تعديل فصل دراسي',
            self::AcademicTermsDelete => 'حذف فصل دراسي',
            self::AcademicTermsActivate => 'تفعيل/تغيير  حالة الفصل الدراسي',

            self::StagesView => 'عرض المراحل الدراسية',
            self::StagesCreate => 'إنشاء مرحلة',
            self::StagesUpdate => 'تعديل مرحلة',
            self::StagesDelete => 'حذف مرحلة',

            self::GradesView => 'عرض الصفوف الدراسية',
            self::GradesCreate => 'إنشاء صف',
            self::GradesUpdate => 'تعديل صف',
            self::GradesDelete => 'حذف صف',

            self::SectionsView => 'عرض الشعب الدراسية',
            self::SectionsCreate => 'إنشاء شعبة',
            self::SectionsUpdate => 'تعديل شعبة',
            self::SectionsDelete => 'حذف شعبة',
            self::SectionsViewStudents => 'عرض قائمة طلاب الشعبة',

            self::SubjectsView => 'عرض بنك المواد',
            self::SubjectsCreate => 'إنشاء مادة',
            self::SubjectsUpdate => 'تعديل مادة',
            self::SubjectsDelete => 'حذف مادة',

            self::CurriculumsView => 'عرض المناهج (توزيع المواد)',
            self::CurriculumsCreate => 'إنشاء خطة منهج',
            self::CurriculumsUpdate => 'تعديل خطة منهج',
            self::CurriculumsDelete => 'حذف خطة منهج',
            self::CurriculumsManageSubjects => 'إدارة مواد المنهج',

            self::StudentsView => 'عرض ملفات الطلاب',
            self::StudentsCreate => 'تسجيل طالب جديد',
            self::StudentsUpdate => 'تعديل بيانات طالب',
            self::StudentsDelete => 'حذف طالب',
            self::StudentsManageGuardians => 'ربط أولياء الأمور',

            self::GuardiansView => 'عرض أولياء الأمور',
            self::GuardiansCreate => 'إضافة ولي أمر',
            self::GuardiansUpdate => 'تعديل ولي أمر',
            self::GuardiansDelete => 'حذف ولي أمر',

            self::TeachersView => 'عرض ملفات المعلمين',
            self::TeachersCreate => 'إضافة معلم',
            self::TeachersUpdate => 'تعديل بيانات معلم',
            self::TeachersDelete => 'حذف معلم',

            self::TeacherAssignmentsView => 'عرض نصاب المعلمين',
            self::TeacherAssignmentsCreate => 'إسناد مادة لمعلم',
            self::TeacherAssignmentsUpdate => 'تعديل إسناد',
            self::TeacherAssignmentsDelete => 'إلغاء إسناد',

            self::TimetableSettingsManage => 'إعدادات هيكل الجدول (التوقيت)',
            self::TimetablesView => 'عرض الجداول الدراسية',
            self::TimetablesCreate => 'إنشاء جدول جديد',
            self::TimetablesUpdate => 'تعديل حصص الجدول',
            self::TimetablesDelete => 'حذف جدول',
            self::TimetablesActivate => 'نشر/تفعيل الجدول',

            self::SchoolDaysView => 'عرض التقويم الدراسي',
            self::SchoolDaysManage => 'إدارة الأيام والإجازات',

            self::AttendanceSheetsView => 'عرض سجلات التحضير',
            self::AttendanceSheetsCreate => 'إنشاء سجل تحضير يومي',
            self::AttendancesTake => 'رصد الحضور',
            self::AttendancesUpdate => 'تحديث الحضور',
            self::AttendancesReport => 'تقارير الحضور والغياب',

            self::ExamTypesManage => 'إعدادات أنواع الاختبارات',

            self::GradingRulesView => 'عرض قواعد توزيع الدرجات',
            self::GradingRulesCreate => 'إنشاء قاعدة درجات',
            self::GradingRulesUpdate => 'تعديل قاعدة درجات',
            self::GradingRulesDelete => 'حذف قاعدة درجات',

            self::ExamsView => 'عرض جدول الاختبارات',
            self::ExamsCreate => 'جدولة اختبار',
            self::ExamsUpdate => 'تعديل موعد اختبار',
            self::ExamsDelete => 'إلغاء اختبار',

            self::MarksView => 'عرض كشوف الدرجات',
            self::MarksEnter => 'رصد الدرجات',
            self::MarksUpdate => 'تعديل الدرجات المرصودة',

            self::ReportsAcademic => 'التقارير الأكاديمية',
            self::ReportsFinancial => 'التقارير المالية',
            self::ReportsAttendance => 'تقارير المواظبة',
        };
    }

    /**
     * Get all permissions grouped by resource.
     */
    public static function grouped(): array
    {
        $groups = [];

        // تجميع تلقائي بناءً على الـ Prefix (اختياري، أو يمكنك بناؤها يدوياً كما فعلت سابقاً)
        // سأقوم ببنائها يدوياً للوضوح والترتيب المنطقي في واجهة المستخدم

        return [
            'الوصول والإعدادات' => [
                self::AccessAdminPanel->value => self::AccessAdminPanel->label(),
                self::DashboardAccess->value => self::DashboardAccess->label(),
                self::SettingsManage->value => self::SettingsManage->label(),
                self::ActivityLogView->value => self::ActivityLogView->label(),
            ],
            'المستخدمين والصلاحيات' => [
                self::UsersView->value => self::UsersView->label(),
                self::UsersCreate->value => self::UsersCreate->label(),
                self::UsersUpdate->value => self::UsersUpdate->label(),
                self::UsersDelete->value => self::UsersDelete->label(),
                self::UsersManageRoles->value => self::UsersManageRoles->label(),
                self::RolesView->value => self::RolesView->label(),
                self::RolesCreate->value => self::RolesCreate->label(),
                self::RolesUpdate->value => self::RolesUpdate->label(),
                self::RolesDelete->value => self::RolesDelete->label(),
            ],
            'الهيكل الأكاديمي (سنة/مرحلة/صف/شعبة)' => [
                self::AcademicYearsView->value => self::AcademicYearsView->label(),
                self::AcademicYearsCreate->value => self::AcademicYearsCreate->label(),
                self::AcademicYearsActivate->value => self::AcademicYearsActivate->label(),
                self::AcademicTermsView->value => self::AcademicTermsView->label(),
                self::AcademicTermsCreate->value => self::AcademicTermsCreate->label(),
                self::StagesView->value => self::StagesView->label(),
                self::GradesView->value => self::GradesView->label(),
                self::SectionsView->value => self::SectionsView->label(),
                self::SectionsCreate->value => self::SectionsCreate->label(),
            ],
            'الطلاب وأولياء الأمور' => [
                self::StudentsView->value => self::StudentsView->label(),
                self::StudentsCreate->value => self::StudentsCreate->label(),
                self::StudentsUpdate->value => self::StudentsUpdate->label(),
                self::StudentsManageGuardians->value => self::StudentsManageGuardians->label(),
                self::GuardiansView->value => self::GuardiansView->label(),
                self::GuardiansCreate->value => self::GuardiansCreate->label(),
            ],
            'المعلمين والإسناد' => [
                self::TeachersView->value => self::TeachersView->label(),
                self::TeachersCreate->value => self::TeachersCreate->label(),
                self::TeacherAssignmentsView->value => self::TeacherAssignmentsView->label(),
                self::TeacherAssignmentsCreate->value => self::TeacherAssignmentsCreate->label(),
            ],
            'المناهج والمواد' => [
                self::SubjectsView->value => self::SubjectsView->label(),
                self::SubjectsCreate->value => self::SubjectsCreate->label(),
                self::CurriculumsView->value => self::CurriculumsView->label(),
                self::CurriculumsCreate->value => self::CurriculumsCreate->label(),
                self::CurriculumsManageSubjects->value => self::CurriculumsManageSubjects->label(),
            ],
            'الجداول المدرسية والتقويم' => [
                self::TimetableSettingsManage->value => self::TimetableSettingsManage->label(),
                self::TimetablesView->value => self::TimetablesView->label(),
                self::TimetablesCreate->value => self::TimetablesCreate->label(),
                self::TimetablesUpdate->value => self::TimetablesUpdate->label(),
                self::TimetablesActivate->value => self::TimetablesActivate->label(),
                self::SchoolDaysView->value => self::SchoolDaysView->label(),
                self::SchoolDaysManage->value => self::SchoolDaysManage->label(),
            ],
            'الحضور والغياب' => [
                self::AttendanceSheetsView->value => self::AttendanceSheetsView->label(),
                self::AttendanceSheetsCreate->value => self::AttendanceSheetsCreate->label(),
                self::AttendancesTake->value => self::AttendancesTake->label(),
                self::AttendancesUpdate->value => self::AttendancesUpdate->label(),
            ],
            'الاختبارات والدرجات' => [
                self::ExamTypesManage->value => self::ExamTypesManage->label(),
                self::GradingRulesView->value => self::GradingRulesView->label(),
                self::GradingRulesCreate->value => self::GradingRulesCreate->label(),
                self::ExamsView->value => self::ExamsView->label(),
                self::ExamsCreate->value => self::ExamsCreate->label(),
                self::MarksView->value => self::MarksView->label(),
                self::MarksEnter->value => self::MarksEnter->label(),
                self::MarksUpdate->value => self::MarksUpdate->label(),
            ],
            'التقارير' => [
                self::ReportsAcademic->value => self::ReportsAcademic->label(),
                self::ReportsFinancial->value => self::ReportsFinancial->label(),
                self::ReportsAttendance->value => self::ReportsAttendance->label(),
            ],
        ];
    }

    public function values()
    {
        return array_column($this->cases(), 'value');
    }
}
