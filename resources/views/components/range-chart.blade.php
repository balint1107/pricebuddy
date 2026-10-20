@php
    $height = $height ?? '50px';
    $displayDatasets = $cachedDatasets;
    if ($attributes->get('compact') && ! empty($displayDatasets['datasets'])) {
        // A row needs a sparkline, while detailed views keep their min/avg/max band.
        $displayDatasets['datasets'] = array_slice($displayDatasets['datasets'], 0, 1);
        $displayDatasets['datasets'][0]['fill'] = false;
        $displayDatasets['datasets'][0]['label'] = __('Lowest price');
        $displayDatasets['datasets'][0]['pointRadius'] = count($displayDatasets['labels'] ?? []) === 1 ? 2 : 0;
        $displayDatasets['datasets'][0]['pointBackgroundColor'] = $displayDatasets['datasets'][0]['borderColor'];
    }
@endphp
<div>
    <div
        style="height: {{ $height }}"
        id="{{ uniqid('chart-wrapper-'.$product->getKey().'-') }}"
        x-load
        x-data="pbChart({
            cachedData: @js($displayDatasets),
            options: @js($options),
            type: @js($type),
        })"
        {{ $attributes->except('compact')->merge(['class' => 'relative']) }}
    >
        <canvas
            role="img"
            aria-label="Price history chart for {{ $product->title ?? 'product' }}"
            class="absolute inset-0 top-1 bottom-1 rounded-br-lg"
            x-ref="canvas"
            id="{{ uniqid('chart-canvas-'.$product->getKey().'-') }}"
        ></canvas>
    </div>
</div>

