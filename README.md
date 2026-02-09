# Trip Manager

Repositório do desafio "Trip Manager" — sistema para gerenciar pedidos de viagem corporativa.

Estrutura principal

- `backend/` — API REST em Laravel (documentação específica em `backend/README.md`).
- `frontend/` — cliente em Vue.js (documentação em `frontend/README.md`).

Quick start (desenvolvimento)

1. Subir containers (backend, db e frontend):

```bash
docker compose up --build -d
```

2. Conferir containers em execução:

```bash
docker compose ps
```

3. Logs úteis:

```bash
docker compose logs -f backend
docker compose logs -f frontend
docker compose logs -f db
```

4. Registrar usuário via API (exemplo):

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Pablo","email":"pablo@example.com","password":"secret"}'
```

Comandos de desenvolvimento (sem Docker):

Backend:

```bash
cd backend
composer install
cp .env.docker.example .env
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```

Frontend:

```bash
cd frontend
npm install
npm run dev
```

Testes

- Rodar testes do backend dentro do container (recomendado):

```bash
docker compose exec backend php artisan test
```

- Rodar testes localmente (se ambiente PHP configurado):

```bash
cd backend
composer install
php artisan test
```

Variáveis de ambiente principais

- `APP_ENV`, `APP_DEBUG`, `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (backend)
- `VITE_API_BASE_URL` (frontend) — URL base da API (no compose está `http://backend:8000`).

## Default demo credentials

Para facilitar testes rápidos, use as credenciais abaixo para efetuar login na aplicação de desenvolvimento:

- Email: pablo@example.com
- Senha: secret

Estas credenciais são criadas por script de seed ou podem ser registradas via endpoint `/api/register` conforme indicado acima.

Notas

- A API usa Sanctum para autenticação via tokens pessoais; veja `backend/README.md` para detalhes.
- Para desenvolvimento rápido, o `docker-compose.yml` traz serviços para backend, db e frontend.

Contribuição

1. Crie branches separados por feature/bugfix.
2. Execute os testes localmente antes de abrir PR.

Mais detalhes estão em `backend/README.md` e `frontend/README.md`.
