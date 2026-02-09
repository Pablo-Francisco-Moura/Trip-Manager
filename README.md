# Trip Manager

Repositório do desafio "Trip Manager" — sistema para gerenciar pedidos de viagem corporativa.

Estrutura principal

- `backend/` — API REST em Laravel (documentação específica em `backend/README.md`).
- `frontend/` — cliente em Vue.js (ainda a ser implementado).

Quick start (desenvolvimento)

1. Subir containers (backend + db):

```bash
docker compose up --build -d
```

2. Ver logs e verificar startup:

```bash
docker compose logs -f backend
docker compose logs -f db
```

3. Registrar usuário via API (exemplo):

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Pablo","email":"pablo@example.com","password":"secret"}'
```

Documentação adicional

- Backend: `backend/README.md` — contém instruções completas de execução, autenticação (Sanctum), endpoints e testes.
- Frontend: `frontend/README.md` — (a implementar) instruções para rodar o cliente Vue.js.
