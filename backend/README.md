# Back-end (Laravel)

Esta pasta contém o projeto Laravel responsável pela API REST do sistema de pedidos de viagem corporativa.

## Rodando localmente (recomendado: Docker)

Pré-requisitos:

- Docker e Docker Compose instalados

1. Copie o arquivo de exemplo de ambiente:

```bash
cp backend/.env.docker.example backend/.env
```

2. Suba os containers (build e execução):

```bash
docker compose up --build -d
```

3. Aguarde o banco subir e execute os comandos artisan dentro do container backend:

```bash
docker compose exec backend bash
composer install
php artisan key:generate
php artisan migrate
exit
```

Após isso a API estará disponível em: http://localhost:8000

### Observações sobre Docker

- O `docker-compose.yml` já monta o código da pasta `backend` como volume para facilitar desenvolvimento.
- Se preferir rebuildar a imagem após mudanças nas dependências do `composer` rode `docker compose up --build`.

## Rodando sem Docker (local host)

Pré-requisitos: PHP 8.2+, Composer, MySQL/Postgres (ou use sqlite)

```bash
cd backend
composer install
cp .env.docker.example .env
# ajuste as variáveis de banco no .env conforme seu ambiente
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

## Autenticação (Sanctum)

Este projeto utiliza `laravel/sanctum` para emissão de tokens pessoais (Bearer). O trait `HasApiTokens` já foi adicionado ao `User`.
Se ainda não publicou/migrou o Sanctum, dentro do container/ambiente execute:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate
```

Para autenticar, use os endpoints:

- `POST /api/register` — cria usuário e retorna `token` (campo `token` no JSON)
- `POST /api/login` — retorna `token`

Inclua o token nas requisições protegidas usando o header:

```
Authorization: Bearer <token>
```

## Endpoints principais

- `POST /api/register` — registrar usuário (name, email, password)
- `POST /api/login` — login (email, password)
- `POST /api/logout` — logout (autenticado)
- `GET /api/travel-requests` — listar pedidos do usuário (autenticado)
- `POST /api/travel-requests` — criar pedido (autenticado)
- `GET /api/travel-requests/{id}` — detalhes do pedido (autenticado, dono do pedido)
- `PATCH /api/travel-requests/{id}/status` — atualizar status (autenticado, somente admin)

## Testes

Rodar testes dentro do container:

```bash
docker compose exec backend bash
php artisan test
```

Ou localmente:

```bash
cd backend
vendor/bin/phpunit
```

### Executando apenas testes específicos

- Rodar uma classe de teste (dentro do container):

```bash
docker compose exec backend php artisan test --filter=TravelRequestTest
```

### Executando testes sem Docker (local)

```bash
cd backend
vendor/bin/phpunit --filter TravelRequestTest
```

### Notas

- Recomenda-se rodar `composer install` antes de rodar os testes se você estiver rodando localmente.
- Para executar testes que verificam notificações, utilizamos `Notification::fake()` nos testes de feature.

## Observações finais

- O README principal do repositório na raiz pode conter instruções adicionais para o front-end.
- Se quiser, eu posso adicionar um script `Makefile` ou `make` com comandos úteis (`up`, `down`, `migrate`, `test`).

## Sanctum / Autenticação

Recomenda-se usar `laravel/sanctum` para autenticação via tokens. Após instalar dependências, execute:

```bash
cd backend
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate
```

No `User` já foi adicionado o trait `HasApiTokens` para emitir tokens. As rotas usam `auth:sanctum`.
