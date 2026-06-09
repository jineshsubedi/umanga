@props(['url'])
@php
    $appLogo = \App\Models\Setting::where('key', 'app_logo')->value('value');
    $appName = \App\Models\Setting::where('key', 'app_name')->value('value') ?: config('app.name');
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($appLogo)
<img src="{{ asset('storage/' . $appLogo) }}" class="logo" alt="{{ $appName }} Logo" style="max-height: 50px;">
@else
{{ $appName }}
@endif
</a>
</td>
</tr>
