@props([
    'align' => 'right', // left, center, right
    'nowrap' => false,
])

@php
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ];
    
    $baseClasses = 'px-6 py-4';
    $alignClass = $alignClasses[$align] ?? $alignClasses['right'];
    // دعم nowrap كخاصية boolean أو string
    // في Blade، عندما نكتب nowrap فقط (بدون قيمة)، يتم تمريره كـ string 'nowrap'
    $isNowrap = $nowrap === true 
        || $nowrap === 'true' 
        || $nowrap === '1' 
        || $nowrap === 'nowrap'
        || ($nowrap !== false && $nowrap !== null && $nowrap !== '');
    $nowrapClass = $isNowrap ? 'whitespace-nowrap' : '';
    
    // إزالة nowrap من $attributes حتى لا يظهر في HTML
    $filteredAttributes = $attributes->except('nowrap');
@endphp

<td {{ $filteredAttributes->merge([
    'class' => implode(' ', array_filter([
        $baseClasses,
        $alignClass,
        $nowrapClass,
    ]))
]) }}>
    {{ $slot }}
</td>
