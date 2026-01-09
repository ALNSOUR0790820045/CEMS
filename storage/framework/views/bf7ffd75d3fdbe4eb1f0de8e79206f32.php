<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'CEMS ERP')); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        :root {
            --apple-bg: #f5f5f7;
            --nav-bg: rgba(255, 255, 255, 0.9);
            --text: #1d1d1f;
            --accent:  #0071e3;
            --border:  rgba(0,0,0,0.08);
        }

        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }

        body { 
            margin: 0; 
            font-family: 'Cairo', sans-serif; 
            background:  var(--apple-bg); 
            color: var(--text); 
        }

        header {
            position: fixed; 
            top: 0; 
            left: 0; 
            right: 0; 
            z-index: 2000;
            background: var(--nav-bg); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter:  blur(20px);
            border-bottom: 1px solid var(--border);
            height:  44px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            padding: 0 20px;
        }

        .nav-container { 
            display: flex; 
            gap: 30px; 
            align-items: center; 
        }

        .logo {
            font-weight: 700;
            font-size:  0.95rem;
            color: var(--text);
            margin-left: 20px;
        }

        . nav-group { 
            position: relative; 
            height: 44px; 
            display: flex; 
            align-items: center; 
        }
        
        .nav-link {
            text-decoration: none; 
            color: var(--text); 
            font-size: 0.85rem;
            font-weight: 400; 
            cursor: pointer; 
            transition:  0.2s;
            padding: 10px 0;
            white-space: nowrap;
        }

        . nav-group:hover .nav-link { 
            color: var(--accent); 
        }

        .mega-menu {
            position: fixed; 
            top: 44px; 
            left: 0; 
            right: 0;
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            
            display: grid;
            grid-template-columns: repeat(3, 1fr); 
            gap: 40px;
            
            opacity: 0;
            pointer-events:  none;
            visibility: hidden;
            
            padding: 30px 10%;
            border-bottom: 1px solid var(--border);
            
            transition: opacity 0.25s ease, visibility 0.25s ease;
            
            box-shadow:  0 20px 40px rgba(0,0,0,0.05);
            max-height: 70vh;
            overflow-y:  auto;
        }

        . nav-group:hover .mega-menu { 
            opacity: 1;
            pointer-events: auto;
            visibility: visible;
        }

        .mega-menu:: before {
            content: '';
            position: absolute;
            top: -44px;
            left: 0;
            right:  0;
            height: 44px;
        }

        .menu-col h4 { 
            font-size: 0.7rem; 
            color: #86868b; 
            letter-spacing: 1px; 
            margin-bottom: 15px; 
            border-bottom: 1px solid #eee; 
            padding-bottom: 5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .sub-link {
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 10px 8px;
            text-decoration: none; 
            color: var(--text); 
            font-size: 0.9rem; 
            font-weight: 500;
            transition: all 0.2s;
            border-radius: 6px;
        }

        .sub-link:hover { 
            color: var(--accent); 
            background: rgba(0, 113, 227, 0.08);
            transform: translateX(-5px); 
        }

        .sub-link i { 
            width: 18px; 
            height: 18px; 
            color:  var(--accent);
            opacity: 0.8;
        }

        .meta { 
            display: flex; 
            align-items: center; 
            gap: 15px; 
        }

        .search-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .search-icon:hover {
            background: rgba(0, 113, 227, 0.1);
        }

        .search-icon i {
            width: 18px;
            height: 18px;
            color: var(--text);
        }

        #clock { 
            font-family: 'SF Mono', 'Courier New', monospace; 
            font-weight: 600; 
            font-size: 0.75rem; 
            color: #86868b; 
            padding: 4px 10px; 
            border-radius: 4px;
            background: rgba(0,0,0,0.03);
            letter-spacing: 0.5px;
        }

        .user-name {
            font-size: 0.8rem;
            color: var(--text);
            font-weight: 500;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius:  50%;
            background: linear-gradient(135deg, #0071e3, #00c4cc);
            display:  flex;
            align-items:  center;
            justify-content:  center;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        .logout-btn {
            background: none;
            border: none;
            color: #ff3b30;
            cursor: pointer;
            font-size: 0.85rem;
            padding: 6px 10px;
            border-radius:  6px;
            transition:  all 0.2s;
            font-family: 'Cairo', sans-serif;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        . logout-btn:hover {
            background: rgba(255, 59, 48, 0.1);
        }

        .main-content {
            margin-top: 64px;
            padding: 20px;
            min-height:  calc(100vh - 64px);
        }

        @media (max-width: 992px) {
            .nav-container {
                display: none;
            }
        }
    </style>
</head>
<body>

    <header>
        <div style="display: flex; align-items: center;">
            <span class="logo">CEMS ERP</span>
            
            <div class="nav-container">
                <div class="nav-group">
                    <a class="nav-link">الإدارة العليا</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>الهيكل التنظيمي</h4>
                            <a href="/" class="sub-link"><i data-lucide="layout-dashboard"></i> لوحة التحكم</a>
                            <a href="#" class="sub-link"><i data-lucide="building-2"></i> إدارة الشركات</a>
                            <a href="#" class="sub-link"><i data-lucide="map"></i> الفروع</a>
                        </div>
                        <div class="menu-col">
                            <h4>العمليات</h4>
                            <a href="#" class="sub-link"><i data-lucide="workflow"></i> سير العمل</a>
                            <a href="#" class="sub-link"><i data-lucide="settings"></i> الإعدادات</a>
                            <a href="#" class="sub-link"><i data-lucide="shield-check"></i> الصلاحيات</a>
                        </div>
                        <div class="menu-col">
                            <h4>البيانات</h4>
                            <a href="#" class="sub-link"><i data-lucide="database"></i> النسخ الاحتياطي</a>
                            <a href="#" class="sub-link"><i data-lucide="bar-chart-3"></i> التقارير</a>
                            <a href="#" class="sub-link"><i data-lucide="activity"></i> المؤشرات</a>
                        </div>
                    </div>
                </div>

                <div class="nav-group">
                    <a class="nav-link">المشاريع</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>إدارة المشاريع</h4>
                            <a href="#" class="sub-link"><i data-lucide="folder-kanban"></i> المشاريع النشطة</a>
                            <a href="#" class="sub-link"><i data-lucide="map-pin"></i> المواقع</a>
                            <a href="#" class="sub-link"><i data-lucide="gantt-chart"></i> الجدول الزمني</a>
                        </div>
                        <div class="menu-col">
                            <h4>الجودة</h4>
                            <a href="#" class="sub-link"><i data-lucide="clipboard-check"></i> الاستلامات</a>
                            <a href="#" class="sub-link"><i data-lucide="shield-alert"></i> السلامة</a>
                        </div>
                        <div class="menu-col">
                            <h4>المعدات</h4>
                            <a href="#" class="sub-link"><i data-lucide="truck"></i> الآليات</a>
                            <a href="#" class="sub-link"><i data-lucide="wrench"></i> الصيانة</a>
                        </div>
                    </div>
                </div>

                <div class="nav-group">
                    <a class="nav-link">المالية</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>المحاسبة</h4>
                            <a href="#" class="sub-link"><i data-lucide="calculator"></i> المحاسبة</a>
                            <a href="<?php echo e(route('main-ipcs.index')); ?>" class="sub-link"><i data-lucide="receipt"></i> المستخلصات الرئيسية</a>
                            <a href="<?php echo e(route('main-ipcs.report')); ?>" class="sub-link"><i data-lucide="bar-chart"></i> تقرير المستخلصات</a>
                        </div>
                        <div class="menu-col">
                            <h4>العقود</h4>
                            <a href="#" class="sub-link"><i data-lucide="file-text"></i> العقود</a>
                            <a href="#" class="sub-link"><i data-lucide="gavel"></i> المطالبات</a>
                        </div>
                        <div class="menu-col">
                            <h4>الضمانات</h4>
                            <a href="#" class="sub-link"><i data-lucide="landmark"></i> الكفالات</a>
                            <a href="#" class="sub-link"><i data-lucide="piggy-bank"></i> المحبوسات</a>
                        </div>
                    </div>
                </div>

                <div class="nav-group">
                    <a class="nav-link">اللوجستيات</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>المشتريات</h4>
                            <a href="#" class="sub-link"><i data-lucide="shopping-cart"></i> المشتريات</a>
                            <a href="#" class="sub-link"><i data-lucide="package"></i> المستودعات</a>
                        </div>
                        <div class="menu-col">
                            <h4>الموارد البشرية</h4>
                            <a href="#" class="sub-link"><i data-lucide="users"></i> الموظفون</a>
                            <a href="#" class="sub-link"><i data-lucide="calendar"></i> الرواتب</a>
                        </div>
                        <div class="menu-col">
                            <h4>المقاولون</h4>
                            <a href="#" class="sub-link"><i data-lucide="handshake"></i> مقاولو الباطن</a>
                            <a href="#" class="sub-link"><i data-lucide="users-round"></i> الاستشاريون</a>
                        </div>
                    </div>
                </div>

                <div class="nav-group">
                    <a class="nav-link">العطاءات</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>إدارة العطاءات</h4>
                            <a href="#" class="sub-link"><i data-lucide="megaphone"></i> العطاءات المتاحة</a>
                            <a href="#" class="sub-link"><i data-lucide="file-check"></i> عروض الأسعار</a>
                        </div>
                        <div class="menu-col">
                            <h4>التقييم</h4>
                            <a href="#" class="sub-link"><i data-lucide="list-checks"></i> التقييم</a>
                            <a href="#" class="sub-link"><i data-lucide="trophy"></i> الترسية</a>
                        </div>
                        <div class="menu-col">
                            <h4>المتابعة</h4>
                            <a href="#" class="sub-link"><i data-lucide="eye"></i> المتابعة</a>
                        </div>
                    </div>
                </div>

                <div class="nav-group">
                    <a class="nav-link">الأرشيف</a>
                    <div class="mega-menu">
                        <div class="menu-col">
                            <h4>التوثيق</h4>
                            <a href="#" class="sub-link"><i data-lucide="folder-open"></i> الأرشيف الرقمي</a>
                            <a href="#" class="sub-link"><i data-lucide="mail"></i> الصادر والوارد</a>
                        </div>
                        <div class="menu-col">
                            <h4>المراسلات</h4>
                            <a href="#" class="sub-link"><i data-lucide="send"></i> قوالب الخطابات</a>
                        </div>
                        <div class="menu-col">
                            <h4>البحث</h4>
                            <a href="#" class="sub-link"><i data-lucide="search"></i> البحث</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="meta">
            <div class="search-icon">
                <i data-lucide="search"></i>
            </div>
            <div id="clock">00:00:00</div>
            <span class="user-name"><?php echo e(Auth::user()->name); ?></span>
            <div class="user-avatar" title="<?php echo e(Auth::user()->email); ?>">
                <?php echo e(Auth::user()->initials); ?>

            </div>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline; margin:  0;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="logout-btn" title="تسجيل الخروج">
                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                </button>
            </form>
        </div>
    </header>

    <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script>
        lucide.createIcons();

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now. getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = hours + ':' + minutes + ':' + seconds;
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /home/runner/work/CEMS/CEMS/resources/views/layouts/app.blade.php ENDPATH**/ ?>