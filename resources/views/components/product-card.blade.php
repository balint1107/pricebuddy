@php
    use App\Enums\Trend;
    /** @var \App\Models\Product $product */
    $latestPrice = $product->getPriceCache()->first();
    $storeCount = $product->getPriceCache()->count();
    $rowKey = $attributes->get('row-key', 'product-'.$product->id);
    $detailsId = 'game-row-details-'.md5($rowKey);
@endphp
@once
<style>
    .pb-game-rows { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; }
    .pb-game-rows.hidden { display: none; }
    .pb-products-record > div > .py-4 { padding-top: 0; padding-bottom: 0; }
    .pb-products-record .ps-3.pe-4 { padding-inline: 8px; }
    .pb-products-record .pb-game-row { border: 0; border-radius: 0; }
    .pb-game-row { border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; overflow: hidden; }
    .dark .pb-game-row { background: #111827; border-color: #293548; }
    .pb-game-row__summary { display: grid; grid-template-columns: minmax(0, 1fr) 120px 210px 30px; align-items: center; gap: 20px; min-height: 80px; padding: 10px 16px; cursor: pointer; }
    .pb-game-row__summary:hover { background: #f8fafc; }
    .dark .pb-game-row__summary:hover { background: #172033; }
    .pb-game-row__identity { display: flex; align-items: center; gap: 14px; min-width: 0; color: inherit; }
    .pb-game-row__cover { width: 68px; height: 44px; flex: 0 0 68px; overflow: hidden; border-radius: 5px; }
    .pb-game-row__cover img { width: 100%; height: 100%; object-fit: cover; }
    .pb-game-row__name { min-width: 0; }
    .pb-game-row__name h3 { font-size: 14px; font-weight: 600; line-height: 1.4; overflow-wrap: anywhere; }
    .pb-game-row__meta, .pb-game-row__store { font-size: 11px; line-height: 1.5; color: #64748b; }
    .dark .pb-game-row__meta, .dark .pb-game-row__store { color: #94a3b8; }
    .pb-game-row__price { text-align: right; font-variant-numeric: tabular-nums; }
    .pb-game-row__amount { font-size: 21px; font-weight: 650; line-height: 1.2; white-space: nowrap; }
    .pb-game-row__history { min-width: 0; width: 100%; }
    .pb-game-row__history-label { display: block; margin-bottom: 3px; font-size: 10px; color: #64748b; }
    .dark .pb-game-row__history-label { color: #94a3b8; }
    .pb-game-row__toggle { display: flex; align-items: center; justify-content: center; width: 30px; height: 36px; border-radius: 5px; color: #64748b; }
    .pb-game-row__toggle:hover { background: #e2e8f0; }
    .dark .pb-game-row__toggle:hover { background: #334155; }
    .pb-game-row__toggle:focus-visible, .pb-game-row__identity:focus-visible { outline: 2px solid #3b82f6; outline-offset: 3px; }
    .pb-game-row__details { border-top: 1px solid #e2e8f0; padding: 12px 16px 4px; }
    .dark .pb-game-row__details { border-color: #293548; }
    .pb-game-row [x-cloak] { display: none !important; }
    @media (max-width: 900px) {
        .pb-game-row__summary { grid-template-columns: minmax(0, 1fr) 100px 145px 28px; gap: 12px; padding: 10px 12px; }
        .pb-game-row__cover { width: 52px; height: 36px; flex-basis: 52px; }
        .pb-game-row__identity { gap: 10px; }
    }
    @media (max-width: 600px) {
        .pb-game-row__summary { grid-template-columns: minmax(0, 1fr) 64px 78px 24px; gap: 6px; padding: 10px; min-height: 80px; }
        .pb-game-row__cover { display: none; }
        .pb-game-row__name h3 { font-size: 12px; }
        .pb-game-row__amount { font-size: 16px; }
        .pb-game-row__meta, .pb-game-row__store { font-size: 10px; }
        .pb-game-row__history-label { font-size: 9px; }
        .pb-game-row__toggle { width: 24px; }
        .pb-game-row__details { padding: 8px 4px 4px; }
        .pb-products-record .pb-game-row__summary { grid-template-columns: minmax(0, 1fr) 64px 60px 20px; gap: 4px; padding: 8px; }
        .pb-products-record .pb-game-row__name h3 { font-size: 11.5px; line-height: 1.35; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 3; overflow: hidden; }
        .pb-products-record .pb-game-row__toggle { width: 20px; }
    }
</style>
@endonce
<div
    style="{{ Filament\Support\get_color_css_variables(Trend::getColor($product->trend), shades: [300, 500, 400, 600, 800]) }}"
    {{ $attributes->except('row-key')->merge(['class' => 'pb-game-row w-full']) }}
    x-data="{ expanded: false }"
>
    <div class="pb-game-row__summary" @click="expanded = !expanded">
        <a class="pb-game-row__identity" href="{{ $product->view_url }}" @click.stop>
            <div class="pb-game-row__cover {{ $draggable ? 'cursor-grab' : '' }}" @if ($draggable) x-sortable-handle @endif>
                <x-product-image :product="$product" />
            </div>
            <div class="pb-game-row__name">
                <h3>{{ $product->title }}</h3>
                <span class="pb-game-row__meta">{{ $storeCount }} {{ $storeCount === 1 ? __('store') : __('stores') }}</span>
            </div>
        </a>
        <div class="pb-game-row__price">
            <div class="pb-game-row__amount">
                {{ $latestPrice?->hasVisiblePrice() ? $latestPrice->getUnitPriceFormatted() : __('Unavailable') }}
            </div>
            @if ($latestPrice)
                <div class="pb-game-row__store">{{ $latestPrice->getStoreName() }}</div>
            @endif
            @if ($latestPrice?->hasPriceFactor())
                <div class="pb-game-row__meta"><x-price-factor-price :cache="$latestPrice" /></div>
            @endif
        </div>
        <div class="pb-game-row__history">
            <span class="pb-game-row__history-label">{{ __('Price history') }}</span>
            <x-range-chart :product="$product" height="36px" compact />
        </div>
        <button
            type="button"
            class="pb-game-row__toggle"
            @click.stop="expanded = !expanded"
            :aria-expanded="expanded.toString()"
            aria-controls="{{ $detailsId }}"
            aria-label="{{ __('Toggle details for :name', ['name' => $product->title]) }}"
        >
            <x-filament::icon icon="heroicon-s-chevron-down" class="h-4 w-4" x-bind:class="{ 'rotate-180': expanded }" />
        </button>
    </div>
    <div class="pb-game-row__details" id="{{ $detailsId }}" x-cloak x-show="expanded">
        @include('components.product-badges', ['product' => $product])
        @livewire('product-card-detail', ['product' => $product, 'showNextCheck' => false], key('product-card-detail-'.$rowKey))
    </div>
</div>
