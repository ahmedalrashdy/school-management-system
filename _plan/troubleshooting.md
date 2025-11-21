# 🔧 حل المشاكل الشائعة (Troubleshooting)

دليل لحل المشاكل الشائعة التي قد تواجهها أثناء تطوير النظام.

---

## ❌ المشكلة: "Unable to locate a class or view for component"

### الأعراض
```
Unable to locate a class or view for component [layouts.dashboard].
```

### الأسباب المحتملة
1. عدم وجود `@props` في بداية ملف الـ Component
2. الـ View Cache قديم
3. الملف غير موجود في المسار الصحيح
4. خطأ في التسمية

### ✅ الحل

#### 1. التأكد من وجود `@props` في الـ Layout

جميع ملفات الـ Layout Components يجب أن تبدأ بـ `@props`:

```blade
@props([
    'title' => config('app.name', 'Laravel'),
    'menuItems' => [],
    'pageTitle' => '',
])

<!DOCTYPE html>
...
```

#### 2. مسح الـ Cache

```bash
# مسح View Cache
php artisan view:clear

# مسح كل أنواع الـ Cache
php artisan optimize:clear

# أو
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

#### 3. إعادة تشغيل السيرفر

```bash
# أوقف السيرفر (Ctrl+C)
# ثم شغله مرة أخرى
php artisan serve
```

#### 4. التحقق من وجود الملف

```bash
ls -la resources/views/layouts/dashboard.blade.php
```

---

## ❌ المشكلة: "Mix manifest does not exist"

### الحل

```bash
npm run build
```

---

## ❌ المشكلة: الـ CSS/JS لا يتحدث

### الحل

```bash
# إيقاف npm run dev إذا كان يعمل
# ثم
npm run build

# أو إذا كنت في وضع التطوير
npm run dev
```

---

## ❌ المشكلة: الألوان المخصصة لا تعمل

### الأعراض
الألوان مثل `bg-primary-500` لا تظهر بشكل صحيح

### الحل

1. تأكد من أن ملف `app.css` يحتوي على `@theme`
2. قم ببناء الأصول:

```bash
npm run build
```

3. مسح الـ Cache:

```bash
php artisan optimize:clear
```

---

## ❌ المشكلة: Font Awesome Icons لا تظهر

### الحل

1. تأكد من وجود الرابط في الـ `<head>`:

```blade
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
```

2. تأكد من أن اسم الـ Icon صحيح:

```blade
<!-- ✅ صحيح -->
<i class="fas fa-home"></i>

<!-- ❌ خطأ -->
<i class="fa fa-home"></i>
```

---

## ❌ المشكلة: Alpine.js لا يعمل

### الأعراض
- المودالات لا تُفتح
- القوائم المنسدلة لا تعمل
- Dark Mode Toggle لا يعمل

### الحل

1. تأكد من وجود Alpine.js في `resources/js/app.js`:

```javascript
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
```

2. تأكد من بناء الـ JS:

```bash
npm run build
```

3. تأكد من أن `@vite` موجود في الـ Layout:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

---

## ❌ المشكلة: Dark Mode لا يعمل

### الحل

1. تأكد من وجود Alpine.js initialization في `<html>`:

```blade
<html x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" 
      :class="{ 'dark': darkMode }">
```

2. تأكد من أن Tailwind مُعد للـ Dark Mode في `tailwind.config.js`:

```javascript
export default {
  darkMode: 'class',
  // ...
}
```

---

## ❌ المشكلة: RTL لا يعمل بشكل صحيح

### الحل

تأكد من أن `dir="rtl"` موجود في `<html>`:

```blade
<html dir="rtl" lang="ar">
```

---

## ❌ المشكلة: خطأ في الـ Sidebar (Component not found)

### الحل

1. تأكد من وجود الملف:

```bash
ls -la resources/views/components/ui/sidebar.blade.php
```

2. مسح الـ Cache:

```bash
php artisan view:clear
```

3. تأكد من استخدام الاسم الصحيح:

```blade
<!-- ✅ صحيح -->
<x-ui.sidebar :menu-items="$menuItems" />

<!-- ❌ خطأ -->
<x-sidebar :menu-items="$menuItems" />
```

---

## ❌ المشكلة: الـ Form Components لا تعرض الأخطاء

### الحل

تأكد من أن الـ `name` و `model` متطابقان:

```blade
<x-form.input
    name="email"
    model="email"
    label="البريد الإلكتروني"
/>
```

أو استخدم فقط `name` إذا لم تكن تستخدم Livewire:

```blade
<x-form.input
    name="email"
    label="البريد الإلكتروني"
/>
```

---

## ❌ المشكلة: الصور/الأصول لا تظهر

### الحل

1. للصور العامة في `public/`:

```blade
<img src="{{ asset('images/logo.png') }}" />
```

2. للصور في `storage/`:

```bash
# أولاً: أنشئ الرابط الرمزي
php artisan storage:link
```

```blade
<img src="{{ Storage::url('avatars/user.jpg') }}" />
```

---

## 📝 نصائح عامة لتجنب المشاكل

### 1. بعد أي تعديل في الـ Views

```bash
php artisan view:clear
```

### 2. بعد أي تعديل في الـ Config

```bash
php artisan config:clear
```

### 3. بعد أي تعديل في الـ CSS/JS

```bash
npm run build
```

### 4. إذا كان كل شيء لا يعمل

```bash
# الحل السحري 🪄
php artisan optimize:clear
composer dump-autoload
npm run build
php artisan serve
```

---

## 🆘 إذا استمرت المشكلة

1. تحقق من الـ Laravel Logs:

```bash
tail -f storage/logs/laravel.log
```

2. تحقق من الـ Browser Console (F12)

3. تحقق من صيغة الملف (UTF-8 بدون BOM)

4. تأكد من أن Laravel و Composer و NPM محدّثة:

```bash
php artisan --version
composer --version
npm --version
```

---

## 📚 مراجع مفيدة

- [Laravel Blade Components Docs](https://laravel.com/docs/blade#components)
- [Tailwind CSS v4 Docs](https://tailwindcss.com/docs)
- [Alpine.js Docs](https://alpinejs.dev/)
- [Font Awesome Icons](https://fontawesome.com/icons)

---

**آخر تحديث:** 13 نوفمبر 2025

