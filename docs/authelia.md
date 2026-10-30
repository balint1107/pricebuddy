# Optional trusted-proxy sign-in

With `MONITOR_SSO_ENABLED=1`, the browser middleware verifies `X-Monitor-Secret`
against the private `MONITOR_SSO_SECRET` and looks up `X-Monitor-User` in the JSON
`MONITOR_SSO_USERS` mapping. Values are existing numeric user IDs. Unknown users
and missing/forged secrets fail closed; no user, role, password or product data is
created or migrated. The middleware runs on the full web group, including Livewire.
The native Filament password action is disabled in this mode.

The home deployment binds `balintberes` (email sign-in `balintberes96@gmail.com`) to the existing administrator (ID 1). nginx
must overwrite identity headers after Authelia authorization, and direct backend
access must be restricted to that proxy. The secret must never be put in source
control or sent by frontend code. API keys remain separate application credentials;
external API clients also need to satisfy the deployment's gateway policy.

Unset the explicit SSO enable flag to retain the original login behavior. Existing
hashes remain available for deliberate recovery. Central password/passkey management
belongs to Authelia. No schema changes or dependency additions are required.
