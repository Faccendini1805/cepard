# Sistema de Gestión de Tests y Evaluaciones

Sistema web desarrollado en PHP para la gestión de tests, usuarios y evaluaciones.

## Requisitos

- PHP 8.0 o superior
- MySQL 5.7 o superior
- Composer
- Apache/Nginx

## Dependencias

- Bootstrap 5.3
- DataTables
- PhpSpreadsheet
- TCPDF
- PHPMailer

## Instalación

1. Clonar el repositorio
2. Ejecutar `composer install`
3. Importar el archivo `database/schema.sql` en MySQL
4. Copiar `config/config.example.php` a `config/config.php` y configurar las credenciales
5. Asegurar permisos de escritura en las carpetas:
   - /uploads
   - /temp
   - /logs

## Estructura de Carpetas

```
/
├── admin/           # Panel de administración
├── api/            # Endpoints API
├── assets/         # Recursos estáticos (CSS, JS, imágenes)
├── config/         # Archivos de configuración
├── database/       # Scripts SQL y migraciones
├── includes/       # Clases y funciones PHP
├── templates/      # Plantillas y vistas
├── uploads/        # Archivos subidos
└── vendor/         # Dependencias de Composer
```

## Roles del Sistema

1. Administrador: Acceso total al sistema
2. Evaluador: Gestión de tests y evaluaciones
3. Usuario: Responder tests asignados

## Seguridad

- Autenticación mediante sesiones PHP
- Contraseñas hasheadas con password_hash()
- Protección contra CSRF
- Validación de entrada de datos
- Prepared Statements para consultas SQL
