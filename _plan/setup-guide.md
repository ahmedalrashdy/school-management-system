# 🚀 دليل الإعداد والتشغيل

دليل خطوة بخطوة لإعداد وتشغيل نظام إدارة المدرسة المتكامل.

---

## ✅ المتطلبات الأساسية

- PHP 8.4+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Git

---

## 📦 خطوات التثبيت

### 1. استنساخ المشروع (إذا لزم الأمر)

```bash
git clone [repository-url]
cd school-management-system
```

### 2. تثبيت Dependencies

```bash
# تثبيت PHP dependencies
composer install

# تثبيت Node dependencies
npm install
```

### 3. إعداد ملف البيئة

```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate
```

### 4. إعداد قاعدة البيانات

افتح ملف `.env` وعدّل إعدادات قاعدة البيانات:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_management
DB_USERNAME=root
DB_PASSWORD=
```

### 5. تشغيل الـ Migrations

```bash
# إنشاء الجداول
php artisan migrate

# (اختياري) تعبئة البيانات التجريبية
php artisan db:seed
```

### 6. بناء الأصول (Assets)

```bash
# للتطوير (مع المراقبة التلقائية)
npm run dev

# أو للإنتاج
npm run build
```

### 7. تشغيل السيرفر

```bash
php artisan serve
```

الآن يمكنك الوصول للتطبيق عبر: `http://localhost:8000`

---

## 👤 الحساب الافتراضي للمسؤول

بعد تشغيل الـ Seeders، يمكنك استخدام:

```
البريد الإلكتروني: admin@school.com
كلمة المرور: password
```

> ⚠️ **مهم جداً:** غيّر كلمة المرور الافتراضية فوراً في بيئة الإنتاج!

---

## 🔧 أوامر مفيدة

### تطوير الواجهة الأمامية

```bash
# تشغيل Vite مع Hot Reload
npm run dev

# بناء للإنتاج
npm run build
```

### إدارة قاعدة البيانات

```bash
# إعادة تعيين قاعدة البيانات
php artisan migrate:fresh

# مع البيانات التجريبية
php artisan migrate:fresh --seed
```

### تنسيق الكود

```bash
# تنسيق كل الملفات
vendor/bin/pint

# تنسيق الملفات المعدلة فقط
vendor/bin/pint --dirty
```

### Cache Management

```bash
# مسح جميع الـ Cache
php artisan optimize:clear

# إنشاء Cache للإنتاج
php artisan optimize
```

---

## 🐛 حل المشاكل الشائعة

### 1. خطأ "Class not found"

```bash
composer dump-autoload
php artisan config:clear
```

### 2. خطأ "Mix manifest does not exist"

```bash
npm run build
```

### 3. مشاكل الأذونات (Linux/Mac)

```bash
chmod -R 755 storage bootstrap/cache
```

### 4. خطأ في الـ CSS/JS

```bash
# حذف الملفات القديمة
rm -rf node_modules package-lock.json

# إعادة التثبيت
npm install
npm run build
```

---

## 🔐 إعداد الصلاحيات (Spatie Permission)

### تثبيت Package

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### إنشاء الصلاحيات الأساسية

سيتم إضافة Seeder خاص بالصلاحيات في المستقبل.

---

## 📊 البيانات التجريبية (Seeders)

لإضافة بيانات تجريبية للتطوير:

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=StudentSeeder
php artisan db:seed --class=TeacherSeeder
```

---

## 🌐 الإنتاج (Production)

### قبل النشر:

1. **تحديث ملف `.env`:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   ```

2. **تحسين الأداء:**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```

3. **الأمان:**
   - غيّر `APP_KEY`
   - استخدم HTTPS فقط
   - قم بإعداد CORS بشكل صحيح
   - فعّل Rate Limiting

---

## 📝 ملاحظات إضافية

### تفعيل Dark Mode

Dark Mode يعمل تلقائياً ويحفظ التفضيلات في `localStorage`.

### إضافة خطوط عربية أفضل

الخط الحالي هو Tajawal. لتغييره، عدّل في:
```blade
<!-- resources/views/layouts/auth.blade.php -->
<!-- resources/views/layouts/dashboard.blade.php -->
<link href="https://fonts.bunny.net/css?family=[font-name]" />
```

### التعامل مع الصور

مجلد الصور العامة:
```
public/images/
```

استخدام Storage Laravel:
```php
Storage::disk('public')->put('avatars/user.jpg', $file);
```

لا تنسَ:
```bash
php artisan storage:link
```

---

## 🆘 الدعم والمساعدة

للمشاكل أو الأسئلة:
1. تحقق من ملفات التوثيق في `_plan/`
2. راجع Laravel Docs: https://laravel.com/docs
3. تواصل مع فريق التطوير

---

## ✅ قائمة التحقق قبل البدء

- [ ] تم تثبيت جميع المتطلبات
- [ ] تم إعداد قاعدة البيانات
- [ ] تم تشغيل الـ Migrations
- [ ] تم بناء الأصول (npm run build)
- [ ] تم اختبار تسجيل الدخول
- [ ] تم التحقق من عمل Dark Mode
- [ ] تم التحقق من الـ RTL
- [ ] تم التحقق من الاستجابة (Responsive)

---

**آخر تحديث:** 13 نوفمبر 2025  
**الإصدار:** 1.0.0

