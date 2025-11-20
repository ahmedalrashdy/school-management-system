<x-dynamic-component :component="$sidebar">
    <x-slot name="breadcrumbs">
        <x-ui.breadcrumbs :items="[
            ['label' => 'لوحة التحكم', 'url' => route('dashboard.index'), 'icon' => 'fas fa-home'],
            ['label' => 'الإشعارات'],
        ]" />
    </x-slot>

    <x-ui.main-content-header title="الإشعارات" />

    <x-ui.card>
        <x-table :headers="[['label' => 'الإشعار'], ['label' => 'الحالة'], ['label' => 'الإجراءات']]">

            @forelse($notifications as $notification)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <x-table.td nowrap>
                        <x-ui.notification-item :$notification />
                    </x-table.td>
                    <x-table.td nowrap>
                        <x-ui.badge :variant="$notification->unread() ? 'success' : 'default'">
                            {{ $notification->unread() ? 'غير مقرؤ' : 'مقرؤ' }}
                        </x-ui.badge>
                    </x-table.td>
                    <x-table.td nowrap>
                        <div class="flex items-center gap-2">
                            @if ($notification->unread())
                                <form
                                    action="{{ route('notifications.read', $notification) }}"
                                    method="post"
                                >
                                    @csrf @method('patch')
                                    <x-table.action
                                        icon="fas fa-check"
                                        variant="success"
                                        title="تعليم كمقروء"
                                    />
                                </form>
                            @endif


                            <x-table.action-delete
                                @click="$dispatch('open-modal', {
                                name: 'delete-notification',
                                notification:{
                                    id:'{{ $notification->id }}',
                                    title:'{{ $notification->data['title'] ?? 'إشعار' }}',
                                    route:'{{ route('notifications.destroy', $notification) }}'
                                }
                                })"
                            />
                        </div>
                    </x-table.td>
                </tr>

            @empty
                <tr>
                    <td colspan="100%">
                        <div class="text-center py-12">
                            <i class="fas fa-bell-slash text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">لا توجد إشعارات</p>
                        </div>
                    </td>
                </tr>
            @endforelse

        </x-table>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </x-ui.card>
    {{-- Delete Confirmation Modal --}}
    <x-ui.confirm-action
        name="delete-notification"
        title="تأكيد حذف الإشعار"
        dataKey="notification"
        spoofMethod="DELETE"
        confirmButtonText="حذف"
        confirmButtonVariant="danger"
    >
        هل أنت متأكد من حذف الإشعار <strong x-text="notification?.title"></strong>؟
    </x-ui.confirm-action>
</x-dynamic-component>
