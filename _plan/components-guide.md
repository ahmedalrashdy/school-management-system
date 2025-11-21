# 🎨 دليل استخدام المكونات (Components Guide)

دليل سريع لاستخدام مكونات Blade المخصصة في المشروع.

---

## 📝 مكونات النماذج (Form Components)

### 1. Input Component

```blade
<x-form.input
    name="first_name"
    label="الاسم الأول"
    type="text"
    placeholder="أدخل الاسم الأول"
    icon="fas fa-user"
    required
/>
```

**الخصائص:**

-   `name` (إجباري): اسم الحقل
-   `label` (اختياري): عنوان الحقل
-   `type` (افتراضي: text): نوع الحقل
-   `placeholder` (اختياري): النص التوضيحي
-   `icon` (اختياري): أيقونة Font Awesome
-   `required` (اختياري): هل الحقل إجباري
-   `model` (اختياري): للربط مع Livewire

---

### 2. Textarea Component

```blade
<x-form.textarea
    name="description"
    label="الوصف"
    rows="5"
    placeholder="أدخل الوصف"
    required
/>
```

---

### 3. Select Component

```blade
<x-form.select
    name="grade_id"
    label="الصف"
    :options="[1 => 'الصف الأول', 2 => 'الصف الثاني']"
    placeholder="اختر الصف"
    required
/>
```

---

### 4. Checkbox Component

```blade
<x-form.checkbox
    name="is_active"
    label="نشط"
    :checked="true"
/>
```

---

### 5. Button Component

```blade
<x-ui.button
    type="submit"
    variant="primary"
    size="lg"
    icon="fas fa-save"
>
    حفظ
</x-ui.button>
```

**الأنماط (Variants):**

-   `primary`: أزرق
-   `secondary`: أخضر
-   `danger`: أحمر
-   `success`: أخضر فاتح
-   `warning`: برتقالي
-   `outline`: شفاف بحدود

**الأحجام:**

-   `sm`: صغير
-   `md`: متوسط (افتراضي)
-   `lg`: كبير

---

## 🎨 مكونات واجهة المستخدم (UI Components)

### 1. Card Component

```blade
<x-ui.card title="العنوان" icon="fas fa-users">
    المحتوى هنا
</x-ui.card>
```

**بدون Padding:**

```blade
<x-ui.card :padding="false">
    <table>...</table>
</x-ui.card>
```

---

### 2. Alert Component

```blade
<x-ui.alert type="success" dismissible>
    تم الحفظ بنجاح!
</x-ui.alert>
```

**الأنواع:**

-   `info`: معلومات (أزرق)
-   `success`: نجاح (أخضر)
-   `warning`: تحذير (برتقالي)
-   `danger`: خطر (أحمر)

---

### 3. Modal Component

```blade
<x-ui.modal name="create-user-modal" title="إضافة مستخدم جديد" max-width="lg" wire:model="showModal">
    <form>
        <!-- Form content -->
    </form>
</x-ui.modal>
```

**فتح المودال (Livewire):**

```php
public $showModal = false;

public function openModal()
{
    $this->showModal = true;
}
```

---

### 4. Badge Component

```blade
<x-ui.badge variant="success" size="md">
    نشط
</x-ui.badge>
```

---

### 5. Stat Card Component

```blade
<x-ui.stat-card
    title="إجمالي الطلاب"
    value="245"
    icon="fas fa-user-graduate"
    color="primary"
    trend="up"
    trend-value="+12%"
/>
```

---

### 6. Sidebar Component

```blade
<x-ui.sidebar :menu-items="$menuItems" />
```

**مثال على `$menuItems`:**

```php
$menuItems = [
    ['header' => 'القائمة الرئيسية'],
    [
        'label' => 'لوحة التحكم',
        'route' => route('dashboard'),
        'icon' => 'fas fa-home',
        'active' => 'dashboard',
    ],
    ['separator' => true],
    [
        'label' => 'الطلاب',
        'route' => route('students.index'),
        'icon' => 'fas fa-user-graduate',
        'active' => 'students.*',
        'badge' => 5, // اختياري
    ],
];
```

---

### 7. Navbar Component

```blade
<x-ui.navbar title="لوحة التحكم" />
```

---

## 🎨 الألوان المخصصة (Custom Colors)

يمكن استخدام الألوان المخصصة في أي مكون:

```blade
<div class="bg-primary-500 text-white">
    نص أبيض على خلفية زرقاء
</div>

<button class="bg-secondary-600 hover:bg-secondary-700">
    زر أخضر
</button>

<span class="text-danger-600">
    نص أحمر
</span>
```

**الألوان المتاحة:**

-   `primary-*`: أزرق (50-950)
-   `secondary-*`: أخضر (50-900)
-   `accent-*`: بنفسجي (50-900)
-   `danger-*`: أحمر (50-700)
-   `warning-*`: برتقالي (50-600)
-   `info-*`: سماوي (50-600)
-   `success-*`: أخضر (50-600)

---

## 🎯 الـ Layouts

### Auth Layout

للصفحات غير المصرح بها (تسجيل دخول، نسيان كلمة مرور...):

```blade
<x-layouts.auth title="تسجيل الدخول" subtitle="مرحباً بك">
    <!-- Form content -->
</x-layouts.auth>
```

---

### Dashboard Layout

للصفحات المصرح بها:

```blade
<x-layouts.dashboard :title="$pageTitle" :menu-items="$menuItems" :page-title="$pageTitle">
    <x-slot name="header">
        <h2>العنوان</h2>
    </x-slot>

    <!-- Page content -->
</x-layouts.dashboard>
```

---

## 💡 نصائح مهمة

1. **استخدم المكونات دائماً** بدلاً من كتابة HTML يدوياً
2. **الأيقونات**: استخدم Font Awesome 6.5.1 classes
3. **الألوان**: استخدم الألوان المخصصة للحفاظ على التناسق
4. **RTL Support**: جميع المكونات تدعم RTL تلقائياً
5. **Dark Mode**: جميع المكونات تدعم Dark Mode

---

## 🔗 مراجع إضافية

-   [Tailwind CSS v4 Docs](https://tailwindcss.com/docs)
-   [Alpine.js Docs](https://alpinejs.dev/)
-   [Font Awesome Icons](https://fontawesome.com/icons)
-   [Laravel Blade Components](https://laravel.com/docs/blade#components)

---

**آخر تحديث:** 13 نوفمبر 2025
