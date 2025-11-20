<script>
    window.flash = {
        success: @json(session('success')),
        error: @json(session('error')),
        warning: @json(session('warning')),
        info: @json(session('info')),
    };
</script>
<div x-data x-init="if (flash.success) Toast.fire({ icon: 'success', title: flash.success });
if (flash.error) Toast.fire({ icon: 'error', title: flash.error });
if (flash.warning) Toast.fire({ icon: 'warning', title: flash.warning });
if (flash.info) Toast.fire({ icon: 'info', title: flash.info });"></div>

<!-- معالج لإظهار إشعارات Toast من Livewire -->
<div x-data
    x-on:show-toast.window="
 const { type, message } = $event.detail;
 const iconMap = {
     'success': 'success',
     'error': 'error',
     'warning': 'warning',
     'info': 'info'
 };
 if (window.Toast) {
     window.Toast.fire({
         icon: iconMap[type] || 'info',
         title: message
     });
 }
">
</div>
