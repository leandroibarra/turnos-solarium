## turnos-solarium
Este sistema le permite administrar una web en donde los clientes de un local de camas solares pueden obtener turnos registrandose gratuitamente.

Además contiene un módulo de administración en el que se podrán:
+ Cancelar y reprogramar turnos reservados.
+ Administrar rango de fechas/horas en el cual los clientes no podrán reservar turnos.
+ Administrar permisos de usuarios registrados en el sistema.
+ Administrar parámetros del sitema.
+ Administrar parámetros, slides y precios para el sitio público.

## Tabla de contenidos
- [Reglas de Negocio](#reglas-de-negocio)
- [Instalación](#instalación)
- [Autor](#autor)
- [Licencia](#licencia)

## Reglas de Negocio
+ El horario de trabajo es de Lunes a Viernes de 9 hs a 20 hs.
+ Cualquier día se podrá reservar un máximo de 2 turnos por cliente.

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
