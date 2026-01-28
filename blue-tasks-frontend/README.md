# Blue Tasks Frontend

SPA en Angular 17/18 (CLI 21.1.x) con Tailwind CSS para gestionar tareas que vienen de un backend Laravel/REST. Incluye guardas de ruta y un interceptor que envía el JWT en cada request.

## Requisitos
- Node.js 18+ (recomendado 20).
- npm 10+.
- Backend disponible y accesible (por defecto `http://blue-tasks.test/api`).

## Instalación y arranque rápido
1) Clona el repositorio  
   ```bash
   git clone <url-del-repo> blue-tasks-frontend
   cd blue-tasks-frontend
   ```
2) Instala dependencias  
   ```bash
   npm install
   ```
3) Configura el endpoint del backend  
   Edita `src/environments/environment.ts` si tu API no está en `http://blue-tasks.test/api`:
   ```ts
   export const environment = {
     production: false,
     apiUrl: 'http://TU-BACKEND/api',
   };
   ```
4) Levanta en modo desarrollo  
   ```bash
   npm start        # alias de ng serve
   # abre http://localhost:4200
   ```
   La recarga en caliente está activa.

## Scripts disponibles
- `npm start` — Servidor de desarrollo.
- `npm run build` — Build de producción en `dist/blue-tasks-frontend`.
- `npm test` — Tests unitarios (Vitest/Karma según config por defecto de Angular CLI).

## Estilos y UI
- Tailwind CSS 3 ya configurado (`tailwind.config.js`, `postcss.config.js`).
- Clases utility aplicadas en vistas: login, listado de tareas, detalle de tarea.
- Estilos globales en `src/styles.scss` con `@tailwind base/components/utilities`.

## Autenticación
- `AuthService` guarda el token en `localStorage` (`blue_tasks_token`).
- `authInterceptor` agrega el header `Authorization: Bearer <token>`.
- `AuthGuard` bloquea rutas protegidas y redirige a `/login`.
- Credenciales seed usadas en demo: `owner@blue-tasks.test / password` (ajusta según tu backend).

## Notas de despliegue
1) Ejecuta `npm run build`.  
2) Sirve la carpeta `dist/blue-tasks-frontend` con tu servidor preferido (Nginx, Apache, Vercel, etc.).  
3) Asegura CORS y HTTPS si tu API está en otro dominio.  
4) Recuerda apuntar `apiUrl` al endpoint público de producción.

## Problemas comunes
- **No carga la tabla al iniciar**: verifica que la API responda y que `apiUrl` sea correcto.  
- **401/403**: token inválido o expirado; vuelve a hacer login.  
- **CORS**: habilita orígenes para el host donde sirves el frontend.  
- **Tailwind no aplica**: confirma que `src/styles.scss` se importe (Angular CLI lo hace por defecto) y que `content` en `tailwind.config.js` incluya `./src/**/*.{html,ts}` (ya está configurado).

## Estructura breve
- `src/app/pages/login` — pantalla de acceso.
- `src/app/pages/tasks` — listado con filtros y paginación.
- `src/app/pages/tasks-detail` — detalle y comentarios.
- `src/app/core` — guard e interceptor.
- `src/environments` — configuración de entornos.

¡Listo! Con esto cualquiera puede instalar y correr el proyecto localmente.***
