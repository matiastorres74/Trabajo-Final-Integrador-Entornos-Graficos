# Alto Rosario Shopping · Portal de promociones

Trabajo Práctico Integrador de **Entornos Gráficos** (UTN FRRo, Ingeniería en Sistemas de Información, comisión 203, año 2025).

Web app responsive para gestionar los descuentos y promociones de los locales de un shopping de Rosario. Los clientes buscan promociones y piden su código de descuento. Los dueños de locales publican promociones y responden los pedidos. El administrador valida cuentas y promociones, publica novedades y genera reportes.

- **URL de producción:** `https://COMPLETAR-URL-DE-PRODUCCION` *(pendiente de despliegue)*
- **Repositorio:** https://github.com/matiastorres74/Trabajo-Final-Integrador-Entornos-Graficos

## Integrantes

- María Victoria Gasparetti
- Matías Torres

Profesores: Diaz, D. y Butti, J.

## Usuarios de prueba

Todos tienen el email confirmado y datos cargados, así que el sistema no se abre vacío.

| Rol | Email | Contraseña | Qué tiene cargado |
|---|---|---|---|
| Administrador | `admin@example.com` | `Admin123` | 2 promociones pendientes de aprobar, 1 dueño pendiente, 1 consulta sin responder |
| Dueño de local | `dueno@example.com` | `Dueno123` | 3 locales (Moda Urbana, Paso Firme y Café del Paseo) y pedidos de clientes para responder |
| Dueño de local | `dueno2@example.com` | `Dueno123` | 2 locales (Deportes Rosario y Aroma Perfumería) |
| Dueño pendiente | `dueno.pendiente@example.com` | `Dueno123` | No puede entrar hasta que el administrador lo apruebe |
| Cliente Inicial | `cliente@example.com` | `Cliente123` | 1 descuento aceptado y 2 esperando respuesta |
| Cliente Medium | `cliente.medium@example.com` | `Cliente123` | 3 descuentos aceptados; ve novedades Medium |
| Cliente Premium | `cliente.premium@example.com` | `Cliente123` | 6 descuentos aceptados; accede a todas las promociones |

Los emails `@example.com` no reciben correo. Para probar el registro y la recuperación de contraseña de punta a punta, registrate con un email propio.

### Recorrido sugerido para probar en 5 minutos

1. Sin iniciar sesión, entrá a **Promociones** y buscá `zapatillas` o el código de local `L003`.
2. Iniciá sesión como `cliente@example.com` y pedí *"Merienda para dos"* o cualquier promoción con el botón **Pedir este descuento**. Después mirá **Mis descuentos**.
3. Cerrá sesión e iniciá como `dueno@example.com`. En **Solicitudes**, aceptá el pedido; el sistema genera el código `AR-XXXXXX`. Creá una promoción en **Mis promociones**.
4. Cerrá sesión e iniciá como `admin@example.com`. En **Promociones**, aprobá la promoción nueva. Después aprobá al dueño pendiente en **Usuarios**, creá un local y una novedad, y mirá los **Reportes**.

## Qué hace el sistema

| Perfil | Funcionalidades |
|---|---|
| **No registrado** | Ver promociones vigentes y locales; buscar por texto, local, código de local, rubro y categoría; formulario de contacto; registrarse. |
| **Cliente** | Registro con confirmación por email; pedir descuentos; historial con estado y código; novedades según su categoría; ver su categoría y el progreso para subir; cambiar nombre y contraseña; recuperar contraseña. |
| **Dueño de local** | Registro sujeto a aprobación del administrador; crear promociones (vigencia, días de la semana y categoría mínima) que quedan pendientes hasta que el administrador las aprueba; eliminar promociones; aceptar o rechazar pedidos; estadísticas de uso por promoción. |
| **Administrador** | CRUD de locales y asignación de dueños; aprobar o rechazar dueños; aprobar o rechazar promociones; CRUD de novedades con vigencia y categoría; reportes por local y por promoción, filtrables por período e imprimibles; bandeja de consultas de contacto; listado de usuarios. |

### Reglas de negocio

- Una promoción se puede pedir solo si está **aprobada**, **vigente** y corresponde a **un día válido** de la semana, y si la **categoría del cliente** alcanza la mínima.
- Cada cliente puede pedir cada promoción **una sola vez**; una restricción `UNIQUE` en la base también lo garantiza.
- Categorías: todos empiezan en **Inicial**. Con **3** descuentos aceptados se pasa a **Medium** y con **6** a **Premium**. Los umbrales se configuran con `USOS_PARA_MEDIUM` y `USOS_PARA_PREMIUM`, y nunca se baja de categoría.
- Las novedades se muestran solo dentro de su vigencia y a clientes de la categoría indicada o superior.
- Las promociones eliminadas por el dueño se borran de forma lógica, para no perder el historial de los reportes.
- Se envían emails al registrarse (confirmación), al recuperar la contraseña, al aprobar o rechazar un dueño o una promoción, al aceptar o rechazar un pedido (con el código) y al recibir una consulta de contacto.

### Decisiones respecto del informe

- El **"Carrito"** de los bocetos se reemplazó por el botón *Pedir este descuento* en cada promoción, más la pantalla *Mis descuentos*: se llega al mismo resultado con menos pasos.
- El **newsletter** (marcado como "no obligatorio, sugerido a futuro") y el envío de novedades por email a otro usuario quedan fuera del alcance de esta entrega.
- El flujo "Buscar artículo" del diagrama de creación de promociones no se implementó como catálogo: la promoción se describe en texto libre.

## Imágenes

- `assets/img/fotos/`: fotos aportadas por el grupo, una por rubro (indumentaria, calzado, deportes, gastronomía, hogar, perfumería, tecnología y librería), más las del frente de local y las compras. **Son fotos de marcas reales (Golden Goose, Massimo Dutti, Carolina Herrera, Kilian, Nike, SMEG, Harrods) y se usan solo como ilustración en este trabajo práctico.** Antes de publicar el sitio conviene reemplazarlas por fotos propias, porque se ven logos reales y los locales del sistema son ficticios.
- Si se crea un local de un rubro sin foto (Juguetería u Óptica), se muestra una imagen genérica de una tienda departamental, presentada como ilustrativa.

## Tecnologías

- **PHP 8.1+** puro, sin framework: sesiones, cookies y `password_hash`.
- **MySQL 5.7+ / MariaDB 10.3+** con **PDO** y sentencias preparadas.
- **HTML5 y CSS3** (mobile first, sin librerías) y un poco de **JavaScript** como mejora progresiva: menú móvil, confirmaciones y prevención de doble envío. El sitio funciona sin JS.
- Envío de emails por **SMTP** con un cliente propio en `app/mailer.php` (STARTTLS o SSL y AUTH LOGIN), sin librerías.

## Estructura

```
Trabajo-Final-Integrador-Entornos-Graficos/
├── index.php, promociones.php, locales.php, local.php, novedades.php   páginas públicas
├── login.php, registro.php, confirmar.php, reenviar.php,
│   recuperar.php, restablecer.php, logout.php, mi-cuenta.php             cuenta
├── contacto.php, acerca.php, mapa-sitio.php, 404.php                     soporte
├── cliente/   solicitudes.php, solicitar.php
├── dueno/     index.php, promociones.php, solicitudes.php, estadisticas.php
├── admin/     index.php, locales.php, local-form.php, usuarios.php, promociones.php,
│              novedades.php, novedad-form.php, reportes.php, consultas.php
├── app/       bootstrap, config (.env), PDO, auth, validador, mailer, reglas de negocio, vistas
├── assets/    css, js, img
├── database/  schema.sql, seed.sql
├── storage/   logs y emails de desarrollo (no se versiona)
├── .env.example, .htaccess, router.php (solo para php -S)
```

## Instalación local

Requisitos: PHP 8.1 o superior con `pdo_mysql` y `openssl`, y MySQL o MariaDB (sirve XAMPP).

```bash
# 1. Base de datos
mysql -u root -e "CREATE DATABASE tp_shopping CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root tp_shopping < database/schema.sql
mysql -u root tp_shopping < database/seed.sql

# 2. Configuración
cp .env.example .env      # completar DB_*, APP_URL y, si se quiere, MAIL_*

# 3. Servidor (desde la carpeta del repositorio)
php -S localhost:8000 router.php
```

Con XAMPP alcanza con copiar la carpeta a `htdocs/` y poner `APP_URL=http://localhost/Trabajo-Final-Integrador-Entornos-Graficos` en el `.env`.

**Emails en local:** si `MAIL_HOST` queda vacío y `APP_ENV=development`, los emails no se envían. Se guardan en `storage/mails/` y se pueden abrir desde el enlace *"ver emails generados"* del pie de página, para probar los links de confirmación y de recuperación.

## Despliegue en producción

1. Subí el contenido del repositorio a la raíz pública del hosting (`public_html` o `htdocs`).
2. Creá la base en el panel del hosting e importá `database/schema.sql` y `database/seed.sql` (por ejemplo, desde phpMyAdmin).
3. Creá el `.env` en el servidor a partir de `.env.example` con:
   - `APP_ENV=production`;
   - `APP_URL=https://tu-dominio` (los links de los emails se arman con este valor);
   - las credenciales de la base de producción;
   - el SMTP.
   
   Si el hosting permite definir variables de entorno, se pueden usar en lugar del `.env`.
4. Activá HTTPS (Let's Encrypt desde el panel) y, si el panel no redirige solo, descomentá las líneas de redirección en `.htaccess`.
5. Si la app no queda en la raíz del dominio, ajustá las rutas `ErrorDocument` del `.htaccess`.
6. SMTP recomendado: **Brevo** (300 emails por día gratis; requiere verificar el remitente) o **Gmail** con una *contraseña de aplicación* (`smtp.gmail.com`, puerto 587, `tls`). Usá un remitente identificable, como `MAIL_FROM_NAME="Alto Rosario Shopping"`.

## Seguridad implementada

- Contraseñas con `password_hash()` (bcrypt) y rehash automático; nunca se guardan en texto plano.
- **Todas** las consultas usan PDO con sentencias preparadas (`ATTR_EMULATE_PREPARES = false`).
- Todo lo que se muestra pasa por `e()` (`htmlspecialchars`), así que no hay XSS. Además hay una cabecera *Content-Security-Policy* estricta, sin scripts inline.
- Token **CSRF** en todos los formularios POST. El logout también es POST, así que un link externo no puede cerrar la sesión de nadie.
- Control de acceso por rol en cada página (`requerir_rol`). El dueño solo puede operar sobre promociones y solicitudes de **sus** locales, y el cliente solo ve **sus** solicitudes: el id sale de la sesión, nunca de la URL.
- Sesión con cookie `HttpOnly`, `SameSite=Lax` y `Secure` bajo HTTPS. El id de sesión se regenera al iniciar sesión, y el logout destruye la sesión y la cookie.
- Tokens de confirmación y recuperación aleatorios (256 bits), guardados como hash SHA-256, de un solo uso y con vencimiento (48 h y 1 h).
- Bloqueo de 1 minuto tras 5 intentos fallidos de login por email (o 20 por IP), contados en la base de datos (tabla `intentos_login`) para que borrar la cookie no reinicie el contador, y mensajes que no revelan si un email está registrado al recuperar la contraseña.
- Las carpetas `app/`, `storage/` y `database/`, y los archivos `.env`, `.sql` y `.md`, quedan bloqueados para el navegador (`.htaccess` y `router.php`).
- En producción los errores se registran en `storage/logs/` y el usuario ve una página de error en español, nunca el error crudo de PHP o de la base.

## Estado del checklist de entrega

| # | Sección | Estado |
|---|---|---|
| 1 | Despliegue | **Pendiente:** falta elegir el hosting y subirlo. La app está preparada (config por entorno, HTTPS, rutas relativas al `APP_URL`). Hay que completar la URL arriba y probarla desde el celular con datos móviles. |
| 2 | Repositorio | README, `.gitignore` (excluye `.env` y `storage/`) y sin credenciales en el código. Los commits los tiene que hacer cada integrante. |
| 3 | Testeable sin instrucciones | Un usuario por rol, datos de prueba cargados, registro de punta a punta y ayuda contextual (botón **Ayuda**) en cada pantalla. |
| 4 | Emails | Implementados: confirmación de registro, recuperación de contraseña y avisos. Los links usan `APP_URL`, el contenido está en español y el remitente es configurable. **Pendiente en producción:** configurar el SMTP y verificar que lleguen y no caigan en spam (Gmail y Outlook). El cliente SMTP se probó en local contra un servidor de prueba. |
| 5 | Navegación | Menú por rol con la sección activa marcada, ruta de acceso (migas), usuario y rol visibles, confirmación antes de borrar, mensajes de éxito y error, estados vacíos con mensaje, mapa del sitio y pie con contacto. |
| 6 | Idioma | Todo en español, incluidas las validaciones. Fechas en `dd/mm/aaaa` y números con separador de miles local. Los errores de la base nunca se muestran. |
| 7 | Validaciones | Todas del lado del servidor (`app/validador.php`), con el error al lado del campo y conservando lo cargado. Se validan formatos de email y fechas, largos máximos y caracteres de control. El doble envío se evita con JS y con restricciones `UNIQUE`. |
| 8 | Seguridad | Ver la sección *Seguridad implementada*. |
| 9 | Responsive | Mobile first; las tablas se apilan como tarjetas en el celular, sin scroll horizontal (probado a 375 px y 1280 px). Contraste AA y foco visible. |
| 10 | Última pasada | Sin `var_dump` ni `console.log`; la consola del navegador queda sin errores. El modo debug se apaga con `APP_ENV=production`. **Pendiente:** recorrerlo en incógnito en producción y que lo pruebe alguien ajeno al grupo. |
