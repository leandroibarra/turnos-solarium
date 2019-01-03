## turnos-solarium
Este sistema le permite administrar una web en donde los clientes de un local de camas solares pueden obtener turnos registrandose gratuitamente.

Además contiene un módulo de administración en el que se podrán:
+ Cancelar y reprogramar turnos reservados.
+ Administrar rango de fechas/horas en el cual los clientes no podrán reservar turnos.
+ Administrar permisos de usuarios registrados en le sistema.
+ Administrar parámetros del sitema.

## Tabla de contenidos
- [Reglas de Negocio](#reglas-de-negocio)
- [Instalación](#instalación)
- [Autor](#autor)
- [Licencia](#licencia)

## Reglas de Negocio
+ Horarios de trabajo:
    + Lunes y Martes de 14 hs hasta 20 hs.
    + Miércoles, Jueves, y Viernes de 9 hs hasta 20 hs.
+ Cualquier día de 14 hs hasta 20 hs se podrá reservar un máximo de 2 turnos por cliente.
+ Miércoles, Jueves, y Viernes de 9 hs a 14 hs se podrá reservar soloment un turno por cliente.

## Instalación
- Clonar repositorio usando `https://github.com/leandroibarra/turnos-solarium.git`.
- Ejecutar `composer install`.
- Renombrar archivo `.env.example` a `.env` y establecer configuracioens de base de datos.
- Crear schema de base de datos usando el comando `php artisan migrate`.
- Configurar estado inicial de base datos mediante el comando `php artisan db:seed`.
- [Opcional] Reemplazar valor `http://turnos-solarium.loc` de constante`APP_URL` en archivos `.env` y `config/app.php` con tu propia url.
- Otorgar permisos de escritura usando los siguientes comandos:
    ```
    chmod 777 -R bootstrap/cache/
    chmod 777 -R storage

## Autor
Leandro Ibarra

## Licencia
Código publicado bajo [Licencia MIT](https://github.com/leandroibarra/turnos-solarium/blob/master/LICENSE)
