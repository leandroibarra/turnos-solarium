# Changelog
Todos los cambios notables a este proyecto serán documentados en este archivo.

## [v2.0.3](https://github.com/leandroibarra/turnos-solarium/tree/v2.0.3) (2019-01-29)
**Agregado**
- Visualización de turnos del día actual y futuros en listado de administración.
- Restricción de reprogramación y cancelación para turnos vencidos del día actual.

**Cambiado**
- Título de sección bronceados y referencias de la misma en administración.
- Mensajes de error en validaciones de requests AJAX.

**Corregido**
- Condición WHERE en consulta SQL de modelo de excepciones.

## [v2.0.2](https://github.com/leandroibarra/turnos-solarium/tree/v2.0.2) (2019-01-21)
**Agregado**
- Editor de texto enriquecido para texto acerca de los bronceados.

**Cambiado**
- Estilos responsive de calendario.
- Horarios de trabajo por día.

**Corregido**
- Expresión regular para validar precios.
- Constructor y método de edición de slides.

## [v2.0.1](https://github.com/leandroibarra/turnos-solarium/tree/v2.0.1) (2019-01-18)
**Agregado**
- Middleware para forzar redirección a HTTPS en página de inicio.
- Sección para administrar precios.
- Sección para administrar slides.

**Cambiado**
- Validaciones de permisos en vistas.
- Páginas de error para códigos de estado 403 y 503.

## [v2.0.0](https://github.com/leandroibarra/turnos-solarium/tree/v2.0.0) (2019-01-06)
**Agregado**
- Sitio público con configuraciones de urls de redes sociales y texto sobre bronceado.
- Redirección forzada a HTTPS en entorno de producción (configurable desde variable de entorno).
- Bloqueo de múltiples clicks en botones de envío de formularios.

**Corregido**
- Consulta SQL en método de modelo de excepciones.

## [v1.1.2](https://github.com/leandroibarra/turnos-solarium/tree/v1.1.2) (2019-01-02)
**Agregado**
- Margen a párrafos en cuerpo de email.
- Boton para acceder a reserva online desde la administración.
- Estilos a contenedores de mensajes de error de formularios.

**Cambiado**
- Reestructuración de plugins css/js.
- Template de email de confirmación de turno.

**Corregido**
- Centrado de formularios de login y registro.
- Layouts y estilos responsive.
- Parámetros de meses en funciones de calendario.
- Consulta SQL en método de modelo de excepciones.
- Anchos de columnas segun tamaños de grilla.

## [v1.1.1](https://github.com/leandroibarra/turnos-solarium/tree/v1.1.1) (2018-12-20)
**Cambiado**
- Centrado de formularios de login y registro.

**Corregido**
- Obtención de código de excepción.

## [v1.1.0](https://github.com/leandroibarra/turnos-solarium/tree/v1.1.0) (2018-12-19)
**Agregado**
- Comentarios en código.
- Leyenda en el campo teléfono del formulario de confirmación de turno.

**Cambiado**
- Reestructuración de modelos.
- Links y estilos en páginas de error dependiendo del módulo en que se produzcan.

**Corregido**
- Paths de vistas de login y registro en sus respectivos controladores.
- Cálculo de cantidad de turnos por hora según configuración del sistema.
- Layouts y estilos responsive.

## [v1.0.0](https://github.com/leandroibarra/turnos-solarium/tree/v1.0.0) (2018-12-13)
**Agregado**
- Proceso de reserva de turnos desde módulo web.
- Implementación parámetros de sistema.
- Manejo de roles y permisos.
- Módulo de administración completo.
  * Listado y edición parámetros del sistema.
  * Listado, cancelación, y reprogramación de turnos.
  * Listado, creación, y edición de excepciones.
  * Listado y edición de permisos de usuarios.

**Cambiado**
- Estilos y tema para módulos web y administración, y páginas de error.
- Estructuración de archivos y rutas de módulos.
- Manejo de seeders para configuraciones iniciales necesarias para el correcto funcionamiento.
- Template de email de confirmación de turno.

## [v0.0.1](https://github.com/leandroibarra/turnos-solarium/tree/v0.0.1) (2018-11-14)
**Agregado**
- Version inicial en fase de desarrollo.