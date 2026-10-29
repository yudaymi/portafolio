# Entre Páginas — PHP + MySQL

## Instalación en XAMPP
1. Copia la carpeta `entre_paginas` dentro de `C:\xampp\htdocs\`.
2. Inicia Apache y MySQL en XAMPP.
3. Abre `http://localhost/phpmyadmin`, selecciona **Importar** y carga `database.sql`.
4. Revisa credenciales de `app/db.php` (por defecto root, sin contraseña).
5. Abre `http://localhost/entre_paginas/`.
6. Registra una cuenta. Para habilitar el panel administrativo, ejecuta en phpMyAdmin (solo en desarrollo): `UPDATE usuarios SET rol='admin' WHERE email='TU_CORREO';`.

## Implementado
Registro/login, feed y búsqueda de publicaciones, reseñas/frases/fotos, subida de imágenes, comentarios, reacciones, guardados, biblioteca y progreso, clubes y conversaciones, perfil y estadísticas administrativas básicas.

## Alcance y pendientes
La base de datos contempla mensajería privada y notificaciones, pero **no tienen interfaz operativa** en esta versión. Tampoco están implementados panel CRUD administrativo completo, edición/eliminación de publicaciones, moderación, seguimiento social, portadas personalizadas de libros, recuperación de contraseña ni notificaciones en tiempo real. Para producción: configurar HTTPS, límites de peticiones, cabeceras de seguridad, validación adicional, política de privacidad y respaldos.

## Seguridad
PDO con consultas preparadas, `password_hash`, protección CSRF, escape HTML y validación MIME/tamaño en imágenes. El directorio de cargas bloquea ejecución PHP en Apache 2.4.


Actualización de autenticación: registro con validación por campo, nombre de usuario sin espacios, detección de duplicados, inicio de sesión por correo o usuario, contraseñas visibles opcionalmente y diseño responsive. Reemplazar los archivos existentes sin volver a importar database.sql.

## Registro editorial actualizado
- Para una instalación nueva, importa `database.sql` completo.
- Si ya tienes las 18 tablas (como en la captura de phpMyAdmin), **NO** importes otra vez `database.sql`: selecciona `entre_paginas` e importa **solo** `database_update.sql`. Esto añade `preferencias_lectura` sin borrar datos.
- Copia los archivos del ZIP sobre tu carpeta `C:\xampp\htdocs\entre_paginas\` y conserva `app/db.php` si cambiaste sus credenciales.
- Abre `http://localhost/entre_paginas/registro.php` y `login.php`.
- Los géneros se guardan en `preferencias_lectura`. La foto se guarda en `uploads/` y su ruta en `usuarios.avatar`. La biografía se guarda en `usuarios.bio`.
- El registro requiere aceptar los términos; `terminos.php` y `privacidad.php` contienen **textos provisionales**, que debes sustituir por políticas reales antes de publicar.
- No se muestran botones de Google/Facebook/Apple porque no existe integración OAuth; deben configurarse con credenciales de proveedor antes de activarlos.
