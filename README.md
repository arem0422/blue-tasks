# Blue Tasks Project

Monorepo con dos piezas:
- `blue-tasks`: API REST en Laravel 10 con JWT para proyectos, tareas y comentarios.
- `blue-tasks-frontend`: SPA en Angular 21 + Tailwind que consume la API.

## Requisitos generales
- Base de datos MySQL o MariaDB disponible.
- PHP 8.1+ y Composer para el backend.
- Node.js 18+ (recomendado 20) y npm 10+ para el frontend.

## Estructura
- `blue-tasks/` — backend Laravel (API).
- `blue-tasks-frontend/` — frontend Angular.

## Levantar el backend (Laravel)
1. Entrar a la carpeta:
   ```bash
   cd blue-tasks
   ```
2. Instalar dependencias:
   ```bash
   composer install
   ```
3. Copiar y ajustar entorno:
   ```bash
   cp .env.example .env
   ```
   Define en `.env` al menos `APP_URL`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
4. Generar claves:
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```
5. Migrar y sembrar datos de prueba:
   ```bash
   php artisan migrate --seed
   ```
6. Servir en local:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   La API quedara en `http://localhost:8000/api`.

Credenciales semilla utiles: `owner@blue-tasks.test / password` (tambien user2 y user3 con la misma clave).

## Levantar el frontend (Angular)
1. Entrar a la carpeta:
   ```bash
   cd blue-tasks-frontend
   ```
2. Instalar dependencias:
   ```bash
   npm install
   ```
3. Apuntar al backend: edita `src/environments/environment.ts` y ajusta `apiUrl` si tu API no esta en `http://blue-tasks.test/api` (por ejemplo `http://localhost:8000/api`).
4. Servir en desarrollo:
   ```bash
   npm start   # alias de ng serve
   ```
   Abre `http://localhost:4200`.
5. Build de produccion:
   ```bash
   npm run build
   ```
   El artefacto queda en `dist/blue-tasks-frontend/` listo para servir (Nginx/Apache/Vercel, etc.).

## Flujo sugerido
1) Levanta el backend y realiza login con una credencial seed.  
2) Crea proyectos y tareas desde el frontend.  
3) Al completar un proyecto puedes llamar `PATCH /projects/{id}/complete` desde la UI o via API para cerrar todas sus tareas.

## Mas detalles
- Documentacion ampliada en `blue-tasks/README.md` (endpoints y seeds) y `blue-tasks-frontend/README.md` (guard, interceptor, problemas comunes).
- Si cambias dominios, ajusta `APP_URL` en el backend y `apiUrl` en el frontend, y revisa CORS/HTTPS.
