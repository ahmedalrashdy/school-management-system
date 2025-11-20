<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مرحباً بك في نظام إدارة المدرسة</title>
    <style>
        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .header-icon::before {
            content: "👨‍👩‍👧‍👦";
            font-size: 32px;
        }
        h1 {
            color: #047857;
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .greeting {
            font-size: 18px;
            color: #4b5563;
            margin-bottom: 30px;
        }
        .content {
            color: #374151;
            font-size: 16px;
            margin-bottom: 25px;
        }
        .highlight {
            background-color: #f0fdf4;
            border-right: 4px solid #10b981;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .highlight-title {
            color: #047857;
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .signature {
            margin-top: 30px;
            color: #4b5563;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon"></div>
            <h1>مرحباً بك في نظام إدارة المدرسة</h1>
        </div>

        <div class="greeting">
            عزيزي ولي الأمر <strong>{{ $user->full_name }}</strong>،
        </div>

        <div class="content">
            <p>نحن سعداء جداً بانضمامك إلى منصة التواصل التعليمي! 🎉</p>
            
            <p>يسرنا أن نرحب بك في نظام إدارة المدرسة المتكامل، الذي سيكون جسر التواصل بينك وبين المدرسة. من خلال هذا النظام، ستتمكن من:</p>
            
            <ul style="margin: 20px 0; padding-right: 20px; color: #4b5563;">
                <li>متابعة الأداء الأكاديمي لأبنائك بشكل مباشر ومستمر</li>
                <li>الاطلاع على درجاتهم ونتائجهم في جميع المواد</li>
                <li>متابعة سجل حضورهم وغيابهم يومياً</li>
                <li>متابعة الجداول الدراسية والأنشطة المدرسية</li>
                <li>التواصل الفعّال مع المعلمين وإدارة المدرسة</li>
                <li>الاطلاع على الإعلانات والتحديثات المهمة</li>
            </ul>
        </div>

        <div class="highlight">
            <div class="highlight-title">🔐 خطوة مهمة للبدء</div>
            <p style="margin: 0; color: #374151;">
                لضمان أمان حسابك وبدء استخدام النظام، يرجى إعادة تعيين كلمة المرور الخاصة بك. هذه الخطوة ضرورية لتفعيل حسابك والتمكن من الدخول إلى النظام ومتابعة أداء أبنائك الأكاديمي بكل سهولة ويسر.
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('password.request') }}" class="cta-button">إعادة تعيين كلمة المرور</a>
        </div>

        <div class="content">
            <p>نؤمن بأن التواصل الفعّال بين البيت والمدرسة هو أساس النجاح. نحن هنا لدعمك ودعم أبنائك في رحلتهم التعليمية! 🌟</p>
        </div>

        <div class="signature">
            <p>مع أطيب التحيات،<br>
            <strong>فريق إدارة المدرسة</strong></p>
        </div>

        <div class="footer">
            <p>هذه رسالة تلقائية، يرجى عدم الرد عليها.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>

