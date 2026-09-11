<x-app-layout title="Dashboard">
    <div class="stats">@foreach($stats as $stat)<x-stat-card :label="$stat['label']" :value="$stat['value']" :caption="$stat['caption']" :icon="$stat['icon']"/>@endforeach</div>
    <div class="dashboard-grid"><section class="panel chart-panel"><div class="panel-head"><h2>Sebaran Alsintan per Kecamatan</h2></div><div class="bar-chart">@forelse($districtData as $item)<div class="bar"><b>{{ $item->total }}</b><i style="height:{{ min(100, $item->total * 12) }}%"></i><span>{{ $item->district }}</span></div>@empty<div class="empty">Belum ada data alsintan untuk ditampilkan.</div>@endforelse</div></section><section class="panel condition-panel"><div class="panel-head"><h2>Alsintan Berdasarkan Kondisi</h2></div><div class="condition-chart"><div class="donut" aria-hidden="true"></div><div class="condition-list">@foreach(['Baik' => '', 'Rusak Ringan' => 'warn', 'Rusak Berat' => 'danger'] as $condition => $class)<div class="condition {{ $class }}"><i></i><span>{{ $condition }}<strong>{{ $conditionData->get($condition)?->total ?? 0 }}</strong></span></div>@endforeach</div></div></section></div>
    <section class="dashboard-table"><div class="table-heading"><h2>Ringkasan Data SIMANTAP</h2><a class="btn btn-soft" href="{{ route('public.poktans') }}">Lihat data publik →</a></div>
        <nav class="type-tabs" aria-label="Filter jenis data">
            <a class="type-tab {{ $selectedType === '' ? 'is-active' : '' }}" href="{{ route('dashboard', array_filter(['district' => $selectedDistrict])) }}">Semua</a>
            @foreach($typeOptions as $option)
                <a class="type-tab {{ $selectedType === $option ? 'is-active' : '' }}" href="{{ route('dashboard', array_filter(['type' => $option, 'district' => $selectedDistrict])) }}">{{ $option }}</a>
            @endforeach
        </nav>
        <div class="table-wrap"><table class="data-table"><thead><tr>@foreach($tableColumns as $column)<th>{{ $column['label'] }}</th>@endforeach</tr></thead><tbody>@forelse($recentData as $item)<tr>@foreach($tableColumns as $column)<td>@switch($column['key'])
@case('no'){{ $loop->parent->iteration }}@break
@case('type')<b>{{ $item['type'] }}</b>@break
@case('name'){{ $item['name'] }}@break
@case('district'){{ $item['district'] }}@break
@case('village'){{ $item['village'] }}@break
@case('poktan'){{ $item['poktan'] }}@break
@case('commodity'){{ $item['commodity'] }}@break
@case('field'){{ $item['columns'][$column['field']] ?? '—' }}@break
@case('coordinates'){{ $item['coordinates'] }}@break
@case('status')<x-badge :value="$item['status']"/>@break
@case('photo')@if($item['photo'])<a href="{{ $item['photo'] }}" target="_blank" rel="noreferrer"><img class="table-photo" src="{{ $item['photo'] }}" alt="Foto {{ $item['name'] }}"></a>@else<span class="table-photo-empty">—</span>@endif @break
@case('actions')@if($item['show_url'] || $item['edit_url'])<div class="row-actions">@if($item['show_url'])<a class="action" href="{{ $item['show_url'] }}" title="Lihat detail">⌕</a>@endif
@if($item['edit_url'])<a class="action" href="{{ $item['edit_url'] }}" title="Ubah data">✎</a>@endif</div>@else—@endif @break
@endswitch</td>@endforeach</tr>@empty<tr><td colspan="{{ count($tableColumns) }}"><div class="empty"><div class="empty-icon">▣</div>Belum ada data untuk ditampilkan.</div></td></tr>@endforelse</tbody></table></div></section>
</x-app-layout>
