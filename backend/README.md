# Back-end (Laravel)

Esta pasta conterá o projeto Laravel responsável pela API REST do sistema de pedidos de viagem corporativa.

## Sanctum / Autenticação

Recomenda-se usar `laravel/sanctum` para autenticação via tokens. Após instalar dependências, execute:

```bash
cd backend
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate
```

No `User` já foi adicionado o trait `HasApiTokens` para emitir tokens. As rotas usam `auth:sanctum`.
