@props(['label', 'value', 'caption', 'icon' => '◇'])
<article class="stat-card"><div class="stat-top"><span>{{ $label }}</span></div><div class="stat-detail"><div><div class="stat-value">{{ number_format($value, 0, ',', '.') }}</div><div class="stat-caption">{{ $caption }}</div></div><i class="stat-icon">{{ $icon }}</i></div></article>
