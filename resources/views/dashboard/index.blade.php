<x-layouts.dashboard
    title="$pageTitle"
    page-title="لوحة التحكم"
>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    مرحباً، {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    نظرة عامة على نظام إدارة المدرسة
                </p>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-ui.stat-card
            title="إجمالي الطلاب"
            :value="$stats['total_students']"
            icon="fas fa-user-graduate"
            color="primary"
            trend="up"
            trend-value="+12%"
        />

        <x-ui.stat-card
            title="المدرسون"
            :value="$stats['total_teachers']"
            icon="fas fa-chalkboard-teacher"
            color="success"
            trend="up"
            trend-value="+3%"
        />

        <x-ui.stat-card
            title="المستخدمون النشطون"
            :value="$stats['total_users']"
            icon="fas fa-users"
            color="info"
        />

        <x-ui.stat-card
            title="الحصص اليوم"
            value="24"
            icon="fas fa-calendar-check"
            color="warning"
        />
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activities -->
        <div class="lg:col-span-2">
            <x-ui.card
                title="الأنشطة الأخيرة"
                icon="fas fa-history"
            >
                <div class="space-y-4">
                    @forelse ($lastActivities as $activity)
                        <x-activity-log.item
                            :$activity
                            :isLast="$loop->last"
                        />
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-box-open fa-3x mb-4"></i>
                            <p class="text-lg font-semibold">لا توجد أنشطة لعرضها حاليًا.</p>
                            <p class="text-sm">عندما تحدث أنشطة في النظام، ستظهر هنا.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a
                        href="{{ route('dashboard.activities.index') }}"
                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium"
                    >
                        عرض جميع الأنشطة
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
            </x-ui.card>
        </div>

        <!-- Quick Actions & Notifications -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <x-index-dashboard.quick-actions />

            <!-- Upcoming Events -->
            <x-index-dashboard.upcoming-events />

        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 gap-6 mt-6">
        <!-- Attendance Chart -->
        @if ($attendanceStatsParms['termId'] !== null)
            <livewire:dashboard.attendance.stats
                :academicTermId="$attendanceStatsParms['termId']"
                :startDate="$attendanceStatsParms['startDate']"
                :endDate="$attendanceStatsParms['endDate']"
            />
        @endif


    </div>
</x-layouts.dashboard>
