<?php

namespace App\View\Components\ActivityLog;

use App\Models\Activity;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class VisualDiff extends Component
{
    public function __construct(public Activity $activity) {}

    /**
     * feilds formats
     */
    protected function formats(): array
    {
        // يمكننا دمج إعدادات عامة مع إعدادات خاصة بالموديل (Subject)
        $modelFormats = [];

        // SubjectType::logFormats
        if ($this->activity->subject_type && method_exists($this->activity->subject_type, 'logFormats')) {
            $modelFormats = call_user_func([$this->activity->subject_type, 'logFormats']);
        }

        return array_merge([
            'amount' => 'currency',
            'price' => 'currency',
            'joined_at' => 'datetime',
            'created_at' => 'datetime',
            'roles' => function ($value) {
                return is_array($value)
                    ? implode('، ', array_map(fn ($r) => "<span class='badge'>$r</span>", $value))
                    : $value;
            },
        ], $modelFormats);
    }

    public function formattedProperties()
    {
        $properties = $this->activity->properties;
        $attributes = $properties['attributes'] ?? [];
        $old = $properties['old'] ?? [];

        $keys = array_keys(
            $this->activity->event === 'updated'
            ? array_intersect_key($attributes, $old)
            : ($attributes ?: $old)
        );

        $data = [];
        $formats = $this->formats();

        foreach ($keys as $key) {
            if (in_array($key, ['created_at', 'updated_at', 'deleted_at', 'password', 'remember_token', 'id'])) {
                continue;
            }

            // نمرر المفتاح ($key) لدالة التنسيق لنعرف كيف نعالجه
            $data[] = [
                'key' => $key,
                'label' => $this->getFieldLabel($key),
                'old' => $this->formatValue($key, $old[$key] ?? null, $formats),
                'new' => $this->formatValue($key, $attributes[$key] ?? null, $formats),
                'value' => $this->formatValue($key, $attributes[$key] ?? $old[$key] ?? null, $formats),
            ];
        }

        return $data;
    }

    private function getFieldLabel($key)
    {
        $transKey = "validation.attributes.{$key}";
        $label = __($transKey);

        return ($label === $transKey) ? Str::title(str_replace('_', ' ', $key)) : $label;
    }

    /**
     * الدالة الذكية لتنسيق القيم
     */
    private function formatValue($key, $value, array $formats)
    {
        // 1. التعامل مع القيم الفارغة
        if (is_null($value) || (is_string($value) && trim($value) === '')) {
            return '<em class="text-gray-400 text-xs">فارغ</em>';
        }

        // 2. التحقق مما إذا كان هناك قاعدة تنسيق لهذا الحقل
        $format = $formats[$key] ?? null;

        // --- الحالة A: دالة مخصصة (Closure) ---
        if ($format instanceof Closure) {
            return $format($value);
        }

        // --- الحالة B: التعامل مع Enum ---
        // الدعم لنوعين: تحديد 'enum' كنص (نحاول التخمين) أو تمرير اسم الكلاس مباشرة
        if ($this->isEnum($format)) {
            return $this->formatEnum($value, $format);
        }

        // --- الحالة C: تنسيقات نصية محددة مسبقاً ---
        if (is_string($format)) {
            return match ($format) {
                'date' => $this->formatDate($value, 'Y-m-d'),
                'datetime' => $this->formatDate($value, 'Y-m-d h:i A'),
                'time' => $this->formatDate($value, 'h:i A'),
                'boolean' => $value ? '<span class="text-green-600">نعم</span>' : '<span class="text-red-600">لا</span>',
                'currency' => number_format((float) $value, 2).' ر.س', // أو عملة ديناميكية
                'json' => '<pre class="text-xs dir-ltr">'.json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>',
                default => $value,
            };
        }

        // --- الحالة D: الافتراضي (معالجة المصفوفات والأنواع الأساسية) ---
        if (is_array($value)) {
            return collect($value)->map(fn ($v) => is_array($v) ? '...' : $v)->implode(', ');
        }

        if (is_bool($value)) {
            return $value ? 'نعم' : 'لا';
        }

        return $value;
    }

    // --- دوال مساعدة ---

    private function formatDate($value, $format)
    {
        try {
            return Carbon::parse($value)->translatedFormat($format);
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function isEnum($format)
    {
        // هل هو اسم كلاس Enum موجود؟
        return is_string($format) && enum_exists($format);
    }

    private function formatEnum($value, $enumClass)
    {
        try {
            // محاولة إيجاد الحالة من القيمة (BackedEnum)
            $case = $enumClass::tryFrom($value);

            if (! $case) {
                return $value;
            }

            // إذا كان للـ Enum دالة label() أو trans() أو getLabel()
            if (method_exists($case, 'label')) {
                return $case->label();
            }
            if (method_exists($case, 'getLabel')) {
                return $case->getLabel();
            }
            if (method_exists($case, 'trans')) {
                return $case->trans();
            }

            // العودة للاسم الافتراضي
            return $case->name;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.activity-log.visual-diff');
    }
}
