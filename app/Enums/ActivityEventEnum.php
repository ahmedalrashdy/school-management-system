<?php

namespace App\Enums;

enum ActivityEventEnum: string
{
    // --- عمليات البيانات الأساسية (Standard Eloquent Events) ---
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case ForceDeleted = 'force_deleted'; // جديد
    case Retrieved = 'retrieved'; // جديد
    case Saved = 'saved'; // جديد (غالباً مرادف لـ created/updated)

    // --- عمليات العلاقات (Pivot/Relationships) ---
    case Attached = 'attached'; // جديد (إضافة علاقة Many-to-Many)
    case Detached = 'detached'; // جديد (إزالة علاقة)
    case Synced = 'synced';     // جديد (مزامنة علاقات)

    // --- العمليات الأكاديمية (Custom School Logic) ---
    case Admission = 'admission';
    case Enrollment = 'enrollment';
    case GradePromotion = 'promotion';
    case ExamResult = 'exam_result';

    // --- الأمان والمستخدمين ---
    case Login = 'login';
    case Logout = 'logout';
    case PasswordReset = 'password_reset';
    case Security = 'security';

    // --- أخرى ---
    case System = 'system';
    case Default = 'default';

    public function label(): string
    {
        return match ($this) {
            self::Created => trans('activity_log.events.created'),
            self::Updated => trans('activity_log.events.updated'),
            self::Deleted => trans('activity_log.events.deleted'),
            self::Restored => trans('activity_log.events.restored'),
            self::ForceDeleted => trans('activity_log.events.force_deleted'),

            self::Attached => trans('activity_log.events.attached'),
            self::Detached => trans('activity_log.events.detached'),
            self::Synced => trans('activity_log.events.synced'),

            self::Retrieved => trans('activity_log.events.retrieved'),
            self::Saved => trans('activity_log.events.saved'),

            self::Admission => trans('activity_log.events.admission'),
            self::Enrollment => trans('activity_log.events.enrollment'),
            self::GradePromotion => trans('activity_log.events.promotion'),
            self::ExamResult => trans('activity_log.events.exam_result'),

            self::Login => trans('activity_log.events.login'),
            self::Logout => trans('activity_log.events.logout'),
            self::PasswordReset => trans('activity_log.events.password_reset'),
            self::Security => trans('activity_log.events.security'),

            self::System => trans('activity_log.events.system'),
            self::Default => trans('activity_log.events.default'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Created => 'fas fa-plus',
            self::Updated, self::Saved => 'fas fa-pen',
            self::Deleted => 'fas fa-trash-alt',
            self::ForceDeleted => 'fas fa-ban', // أيقونة الحظر للحذف النهائي
            self::Restored => 'fas fa-trash-restore',

            self::Attached, self::Synced => 'fas fa-link', // أيقونة الربط
            self::Detached => 'fas fa-unlink', // أيقونة فك الربط

            self::Retrieved => 'fas fa-eye',

            self::Admission => 'fas fa-user-graduate',
            self::Enrollment => 'fas fa-clipboard-list',
            self::GradePromotion => 'fas fa-level-up-alt',
            self::ExamResult => 'fas fa-chart-bar',

            self::Login => 'fas fa-sign-in-alt',
            self::Logout => 'fas fa-sign-out-alt',
            self::PasswordReset => 'fas fa-key',
            self::Security => 'fas fa-shield-alt',

            self::System => 'fas fa-cogs',
            self::Default => 'fas fa-history',
        };
    }

    public function color(): string
    {
        return match ($this) {
                // الأخضر: إنشاء، حفظ، إرفاق، مزامنة، قبول
            self::Created, self::Saved, self::Attached, self::Synced, self::Admission => 'bg-green-100 text-green-600 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',

                // الأزرق: تحديث، تسجيل، ترحيل
            self::Updated, self::Enrollment, self::GradePromotion => 'bg-blue-100 text-blue-600 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',

                // الأحمر الداكن: حذف نهائي
            self::ForceDeleted => 'bg-red-200 text-red-800 border-red-300 dark:bg-red-900/50 dark:text-red-200 dark:border-red-700',

                // الأحمر العادي: حذف، فك ارتباط، أمان
            self::Deleted, self::Detached, self::Security => 'bg-red-100 text-red-600 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',

                // البرتقالي: استعادة، تغيير كلمة سر
            self::Restored, self::PasswordReset => 'bg-amber-100 text-amber-600 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',

                // الرمادي الفاتح: عرض، دخول، خروج، نظام
            self::Retrieved, self::Login, self::Logout, self::System, self::Default => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700/50 dark:text-gray-400 dark:border-gray-600',

                // البنفسجي للنتائج
            self::ExamResult => 'bg-purple-100 text-purple-600 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800',
        };
    }
    public static function options(): array
    {
        return array_reduce(
            self::cases(),
            fn($carry, $case) => $carry + [$case->value => $case->label()],
            []
        );
    }
}
