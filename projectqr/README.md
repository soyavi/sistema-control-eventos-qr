# Sistema de Control de Eventos con QR

Sistema de gestión de eventos que permite el registro de usuarios, generación automática de códigos QR, y control de asistencia mediante verificación de QR.

## Características

- Registro de usuarios con generación automática de códigos QR
- Verificación de acceso mediante códigos QR
- Seguimiento de asistencia a eventos
- Historial de uso de códigos QR
- Interfaz moderna y responsiva

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Node.js 16.x o superior
- Composer
- npm o yarn
- Extensión PHP PDO
- Extensión PHP Imagick

## Instalación

1. Clonar el repositorio:
```bash
git clone <repository-url>
cd <repository-name>
```

2. Instalar dependencias de PHP:
```bash
cd backend
composer install
```

3. Instalar dependencias de Node.js:
```bash
npm install
# o
yarn install
```

4. Configurar la base de datos:
- Crear una base de datos MySQL
- Copiar `backend/config.php.example` a `backend/config.php`
- Actualizar las credenciales de la base de datos en `config.php`

5. Iniciar el servidor PHP:
```bash
cd backend
php -S localhost:8000
```

6. Iniciar el servidor de desarrollo de Next.js:
```bash
npm run dev
# o
yarn dev
```

## Uso

1. Acceder a la aplicación en `http://localhost:3000`

2. Registro de Usuarios:
   - Navegar a la sección "Registro de Usuario"
   - Completar el formulario con los datos requeridos
   - Se generará automáticamente un código QR

3. Verificación de Acceso:
   - Navegar a la sección "Verificación de QR"
   - Escanear o ingresar el código QR
   - El sistema mostrará la información del usuario y su historial de asistencia

## Estructura del Proyecto

```
.
├── backend/                # Backend PHP
│   ├── config.php         # Configuración de la base de datos
│   ├── db.php            # Conexión a la base de datos
│   ├── register_user.php # Endpoint de registro
│   └── verify_qr.php     # Endpoint de verificación
│
├── src/                   # Frontend Next.js
│   ├── app/              # Páginas y rutas
│   │   ├── api/         # API routes
│   │   ├── registro/    # Página de registro
│   │   └── verify/      # Página de verificación
│   └── components/       # Componentes reutilizables
│
└── README.md             # Documentación
```

## Seguridad

- Los códigos QR generados incluyen información encriptada
- Validación de datos tanto en el frontend como en el backend
- Protección contra inyección SQL mediante PDO
- Sanitización de entradas de usuario

## Contribuir

1. Fork el repositorio
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.
