# Blue Tasks API

API REST en Laravel 10 para gestionar proyectos, tareas y comentarios. Usa JWT para autenticacion stateless.

## Requisitos
- PHP 8.1 o superior con extensiones habituales de Laravel.
- Composer.
- MySQL o MariaDB.
- (Opcional) Node y npm si necesitas compilar assets en `resources/`, aunque la API funciona sin frontend.

## Instalacion rapida
1. Clona el repositorio y entra en la carpeta:
   ```bash
   git clone <url-del-repo> blue-tasks
   cd blue-tasks
   ```
2. Instala dependencias PHP:
   ```bash
   composer install
   ```
3. Crea el archivo de entorno y ajusta la base de datos:
   ```bash
   cp .env.example .env
   # edita .env y define DB_DATABASE, DB_USERNAME, DB_PASSWORD
   ```
4. Genera claves:
   ```bash
   php artisan key:generate
   php artisan jwt:secret
   ```
5. Migra y carga datos de prueba:
   ```bash
   php artisan migrate --seed
   ```
6. Levanta el servidor de desarrollo:
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   La API quedara en `http://localhost:8000`.

## Datos sembrados (para probar de inmediato)
- owner@blue-tasks.test / password
- user2@blue-tasks.test / password
- user3@blue-tasks.test / password

Los proyectos iniciales pertenecen al usuario owner; los demas sirven para probar permisos.

## Autenticacion
Los endpoints bajo `/api` requieren token Bearer salvo `auth/register` y `auth/login`.
1. Autenticate:
   ```bash
   curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"owner@blue-tasks.test","password":"password"}'
   ```
2. Usa el `token` recibido:
   `Authorization: Bearer <token>` en cada peticion.

## Endpoints principales
- Autenticacion
  - `POST /api/auth/register`
  - `POST /api/auth/login`
  - `GET /api/auth/me`
  - `POST /api/auth/logout`
  - `POST /api/auth/refresh`

- Proyectos (solo dueno)
  - `GET /api/projects` (paginado)
  - `POST /api/projects` {nombre, estado?}
  - `GET /api/projects/{id}`
  - `PUT/PATCH /api/projects/{id}`
  - `PATCH /api/projects/{id}/complete` marca como completado y finaliza todas sus tareas
  - `DELETE /api/projects/{id}`

- Tareas (solo dueno del proyecto)
  - `GET /api/tasks?project_id=&estado=&prioridad=&per_page=`
  - `POST /api/tasks` {titulo, prioridad, project_id, estado?}
  - `GET /api/tasks/{id}` incluye proyecto y comentarios
  - `PUT/PATCH /api/tasks/{id}`
  - `DELETE /api/tasks/{id}`
  - Reglas: proyectos completados no permiten crear, editar ni eliminar tareas.

- Comentarios
  - `POST /api/tasks/{task}/comments` {cuerpo}

## Flujo sugerido de uso
1) Autenticar (login) y guardar el token. 2) Crear proyecto. 3) Crear tareas dentro del proyecto. 4) Agregar comentarios a cada tarea. 5) Cuando el proyecto termine, llamar `PATCH /projects/{id}/complete` para cerrar el proyecto y marcar todas las tareas como finalizada.

## Ejecucion de pruebas
```bash
php artisan test
```

## Notas
- Guarda tu JWT en un storage seguro del cliente.
- Si cambias el dominio o base URL, actualiza `APP_URL` en `.env` y limpia cache con `php artisan config:clear`.
