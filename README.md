# Sistema de Gestión de Tests y Evaluaciones

Sistema web desarrollado en PHP para la gestión de tests, usuarios y evaluaciones.

---

## Requisitos

- Docker y Docker Compose (recomendado para desarrollo y despliegue local)
- O bien: PHP 8.0+ y MySQL 5.7+ si deseas instalar manualmente

---

## Instalación y Puesta en Marcha (con Docker)

1. **Clona el repositorio:**
   ```bash
   git clone <url-del-repo>
   cd cepard
   ```

2. **Copia el archivo de ejemplo de variables de entorno:**
   ```bash
   cp .env.example .env
   ```
   Edita `.env` si necesitas cambiar los valores de conexión a la base de datos.

3. **Levanta los servicios con Docker Compose:**
   ```bash
   docker-compose up --build -d
   ```

4. **Accede a la aplicación:**
   - Web: [http://localhost:8080](http://localhost:8080)
   - MySQL: puerto 3307, usuario y contraseña según `.env`

5. **Credenciales por defecto:**
   - Usuario: `admin`
   - Contraseña: `admin123`

---

## Estructura de Carpetas

```
/
├── admin/           # Panel de administración
├── api/             # Endpoints API
├── assets/          # Recursos estáticos (CSS, JS, imágenes)
├── config/          # Archivos de configuración (incluye config.php)
├── database/        # Scripts SQL y migraciones (schema.sql)
├── includes/        # Clases y funciones PHP
├── logs/            # Logs de la aplicación
├── temp/            # Archivos temporales
├── templates/       # Plantillas y vistas
├── uploads/         # Archivos subidos por usuarios
├── vendor/          # Dependencias de Composer
├── Dockerfile       # Imagen personalizada de PHP+Apache
├── docker-compose.yml # Orquestación de servicios
├── .env             # Variables de entorno (no subir a git)
└── .env.example     # Ejemplo de variables de entorno
```

---

## Archivos Importantes

- **Dockerfile:** Configura el entorno PHP, Apache y dependencias.
- **docker-compose.yml:** Orquesta los servicios web y base de datos.
- **.env / .env.example:** Variables de entorno para conexión a la base de datos.
- **database/schema.sql:** Script para crear la estructura y datos iniciales de la base de datos.
- **config/config.php:** Configuración de la aplicación y conexión a la base de datos (ahora usa variables de entorno).

---

## Comandos Útiles

- **Levantar servicios:**  
  `docker-compose up -d`
- **Detener servicios:**  
  `docker-compose down`
- **Ver logs:**  
  `docker-compose logs -f`
- **Reconstruir contenedores:**  
  `docker-compose up --build -d`

---

## Conexión a la Base de Datos

- Host: `mysql` (dentro de Docker) o `localhost:3307` (desde tu máquina)
- Usuario, contraseña y nombre de base de datos: definidos en `.env`
- El contenedor de MySQL inicializa la base de datos usando `database/schema.sql` automáticamente.

---

## Roles del Sistema

1. **Administrador:** Acceso total al sistema
2. **Evaluador:** Gestión de tests y evaluaciones
3. **Usuario:** Responder tests asignados

---

## Seguridad

- Autenticación mediante sesiones PHP
- Contraseñas hasheadas con password_hash()
- Protección contra CSRF
- Validación de entrada de datos
- Prepared Statements para consultas SQL

---

## Notas

- Asegúrate de que las carpetas `/uploads`, `/logs` y `/temp` tengan permisos de escritura.
- Si cambias el archivo `.env`, reinicia los contenedores para aplicar los cambios.
- Para entornos de producción, revisa la configuración de seguridad y correo.
