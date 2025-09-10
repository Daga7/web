# NeuroByteAI — Web Project (PHP + MongoDB + Vanilla JS + Bootstrap)

Resumen: Proyecto full-stack que implementa una web estática inspirada en el diseño provisto y una API REST en PHP conectada a MongoDB para un CRUD de `items`.

## Estructura
- `public/` — frontend (HTML/CSS/JS, assets).
- `api/` — backend (PHP) con router simple y controladores.
- `seed_data.json`, `seed.php` — para poblar la BD.
- `composer.json` — dependencias PHP (mongodb/mongodb).
- `.env.example` — variables de entorno.
- `docker-compose.yml`, `Dockerfile` — opcionales para desarrollo con Docker.
- `docs/postman_collection.json` — ejemplos Postman (placeholder).

## Requisitos
- PHP 7.4+ (recomendado 8.0+)
- Composer (para instalar `mongodb/mongodb`)
- MongoDB 4.4+
- (Opcional) Docker & docker-compose

## Instalación (local)
1. Clona el repo y entra en la carpeta.
2. Copia `.env.example` a `.env` y edítalo con tus valores.
3. En `api/` ejecuta `composer install`.
4. Levanta MongoDB localmente o con Docker.
5. Sembrar datos: `php seed.php`
6. Iniciar servidor (modo desarrollo): desde la raíz puedes usar `php -S localhost:8000 -t public` o configurar Apache/Nginx apuntando a `public/` y la API a `api/`.

## Endpoints (resumen)
- `GET /api/items` — listar (paginación y búsqueda q)
- `GET /api/items/{id}` — obtener uno
- `POST /api/items` — crear
- `PUT /api/items/{id}` — actualizar
- `DELETE /api/items/{id}` — eliminar

Ejemplos curl en `docs/`.

## Paleta
- `#9b2cff` — primario (CTAs)
- `#ff3da7` — acento
- `#1b1b2f` — fondo oscuro
- `#ffffff` — texto claro

---
Lee los archivos en `api/` y `public/` para más detalles.
