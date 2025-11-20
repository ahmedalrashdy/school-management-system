<x-layouts.dashboard page-title="سجل الأنشطة">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'سجل الأنشطة', 'icon' => 'fas fa-history'],
        ]" />
    </x-slot>

    <x-ui.main-content-header
        title="سجل الأنشطة"
        description="عرض وتتبع جميع الأنشطة في النظام"
    />

    <!-- Filters -->
    <x-ui.filter-section :showReset="request()->anyFilled(['event', 'log_name'])">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form.select
                name="log_name"
                label="الفئة"
                :options="\App\Enums\ActivityLogNameEnum::options()"
                selected="{{ request('log_name') }}"
                option-value="value"
                option-label="label"
            />
            <x-form.select
                name="event"
                label="نوع الحدث"
                :options="\App\Enums\ActivityEventEnum::options()"
                selected="{{ request('event') }}"
                option-value="value"
                option-label="label"
            />
        </div>
    </x-ui.filter-section>

    <!-- Activities List -->
    @forelse ($activities as $activity)
        <x-activity-log.item
            :$activity
            :isLast="$loop->last"
        />
    @empty
        <div class="text-center py-12">
            <i class="fas fa-history text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">لا توجد أنشطة</p>
        </div>
    @endforelse

    <!-- Pagination -->
    @if ($activities->hasPages())
        <div class="mt-6">
            {{ $activities->links() }}
        </div>
    @endif
</x-layouts.dashboard>
