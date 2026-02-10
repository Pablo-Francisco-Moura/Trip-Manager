Frontend (Vue 3 + Vite)

Run locally (requires Node.js >=18):

```bash
cd frontend
npm install
npm run dev
```

Docker (dev):

```bash
docker compose up --build frontend
```

The frontend expects the API base URL in the `VITE_API_BASE_URL` environment variable (defaults to `http://backend:8000` when running in Docker).

Basic structure:

- `src/views/Login.vue` — login page
- `src/views/Dashboard.vue` — main dashboard with table and form
- `src/components/TravelRequestForm.vue` — create request form
- `src/components/TravelRequestsTable.vue` — requests table with status actions

Integrates with the backend API endpoints under `/api`.
