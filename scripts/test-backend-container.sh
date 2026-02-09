#!/usr/bin/env bash
set -euo pipefail

# Script para subir containers e executar os testes do backend dentro do container
# Uso: ./scripts/test-backend-container.sh

echo "Subindo containers (docker compose up --build -d)..."
if docker compose up --build -d; then
  echo "Containers iniciados."
else
  echo "Falha ao subir containers. Tente executar com sudo: sudo docker compose up --build -d" >&2
  exit 1
fi

echo "Executando testes do backend dentro do container..."
if docker compose exec backend php artisan test; then
  echo "Testes executados com sucesso."
  exit 0
else
  echo "Falha ao executar os testes dentro do container. Se o Docker exigir sudo, rode:" >&2
  echo "  sudo docker compose exec backend php artisan test" >&2
  exit 1
fi
