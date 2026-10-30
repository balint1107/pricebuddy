# Expandable game rows

The v1.0.55 dashboard renders highlights and tag groups as full-width rows.
Each summary shows the game, store count, current available price/store and a
compact lowest-price history sparkline at the right, followed by the disclosure control.
Click the summary or the labelled toggle to expand store offers, actions and
aggregates. The game title still opens its product page. Toggle buttons support
keyboard activation and expose their expanded state to assistive technology.

The existing range chart/data collector and detail Livewire component are reused;
there is no synthetic price history. A single observation is shown as a point;
detailed views retain the existing min/average/max chart. Chart accessible names identify the game.
Scoped row styles include narrow-screen and dark-theme layouts without rebuilding
frontend assets. Category collapse and product dragging remain available.

Deployment uses the v1.0.55 upstream image with this fork's matching views mounted
read-only at `/app/resources/views`; run `php artisan view:clear` after view edits.
Runtime configuration is maintained separately in home-automation/docs/pricebuddy.md.

Product action URLs are generated as absolute URLs, preserving the proxy base
path in Livewire JSON updates as well as full-page renders. This fixes game and
action links losing `/pricebuddy/` after category collapse/reorder updates. The
matching Product.php override must be mounted alongside views for a stock image.

## Products page

The Products table uses the same game-row component as the dashboard. Its title
view column remains searchable and sortable; existing status, lowest-price period,
tag and checking-state filters, pagination, selection and bulk actions are retained.
Per-product edit/fetch/add-URL actions remain inside the expandable details instead
of adding a second action column. Distinct row keys keep list/dashboard Livewire
children independent. Stock-image deployments must also mount ProductResource.php and BaseAction.php.
Create/edit action URLs stay absolute so search/filter Livewire refreshes retain
the service prefix. Table wrapper spacing is removed around the shared rows.

Validation covered live title search, alphabetical sorting, checking-state filter
and reset, bulk-menu availability without executing mutations, 25/50 page-size
selection, row expansion/keyboard collapse and generated service-prefixed links.
Desktop and 390px mobile rows render at 80px without horizontal overflow. PHP
syntax and Blade compilation also pass.

## Store counts

Rows count distinct configured stores from product URLs, including listings that
have not returned their first price yet. A pending initial check must not make a
configured listing appear to be missing.

Rows without any price/stock reading show `No price yet`; a confirmed unavailable
listing without a price shows its actual stock label. Missing initial readings
must not be presented as confirmed unavailability. Status text wraps within the
price column on narrow screens.
