# Práctica de investigación: AEMET → MySQL (notas de trabajo)

Última actualización: 2026-10-31

## Enunciado (resumen)
- Script que consume la API REST de AEMET OpenData (valores climatológicos diarios) para ≥5 ciudades y guarda los datos en MySQL.
- Entregables: código + instrucciones, script SQL (y volcado de datos si es posible), memoria de 1-2 páginas (API key, endpoints usados y por qué, diseño final de BD, dificultades).
- Es la base de la fase 2 (aplicación web), así que el código de acceso a la API y el diseño de BD deben quedar claros y reutilizables.
- XAMPP/phpMyAdmin NO son obligatorios; solo MySQL.
- Web del profesor (solo consulta/contraste, no es la API oficial): https://datosclima.es/Aemethistorico/Meteostation.php

## Decisiones tomadas
- **Lenguaje: Node.js** (por si la fase 2 es full-stack con React; mismo lenguaje en front y back). Librerías: mysql2 + dotenv.
- **Base de datos: Docker** (MySQL 8.4 + phpMyAdmin opcional en localhost:8080), con `docker-compose.yml` que carga `schema.sql` al primer arranque. Para recargar el esquema: `docker compose down -v` y `docker compose up -d`.
- **Esquema**: tablas `ciudades` (id_ciudad, nombre, indicativo) y `lecturas_diarias` (id_lectura, id_ciudad FK, fecha, temp_max, temp_min, precipitacion + extras: temp_media, precip_inapreciable, vel_media_viento, racha_max_viento, horas_sol, pres_max, pres_min). UNIQUE(id_ciudad, fecha) para poder relanzar el script sin duplicados.
- Ciudades iniciales: Madrid 3195, Barcelona 0201D, Valencia 8416, Zaragoza 9434, Teruel 8368U (**indicativos sin verificar**, comprobar en datosclima.es).
- **Estructura del script ETL**: separada en 4 módulos (`pool.js`, `aemet.js`, `parser.js`, `etl.js`) para que cuando se verifiquen los campos reales de la API solo haya que tocar `parser.js`.
- **Rango de fechas**: últimos 30 días por defecto; configurable con `FECHA_INI` / `FECHA_FIN` en `.env`.
- **`ON DUPLICATE KEY UPDATE`** en el INSERT para poder relanzar el script sin duplicados.

## Estado actual
- [x] Schema SQL definido (`schema.sql`)
- [x] Entorno Docker configurado (`docker-compose.yml`)
- [x] Script ETL escrito (`index.js` + `src/`)
- [x] Repositorio GitHub creado y sincronizado
- [ ] Docker Desktop instalado y probado
- [ ] API key de AEMET recibida
- [ ] Verificar nombres de campos reales de la API (tmed, prec, tmin, tmax, velmedia, racha, sol, presMax, presMin) y límite de rango de fechas por petición
- [ ] Prueba end-to-end con `npm start`
- [ ] Redactar la memoria

## Pendiente / por verificar
- La API key aún no ha llegado por correo (revisar spam, probar con otro correo, avisar al profesor si sigue sin llegar).
- Instalar Docker Desktop y probar `docker compose up -d`.
- Verificar con la primera respuesta real de la API los nombres de campos, el límite de rango de fechas por petición y cómo se pasa la api_key.
- Redactar la memoria.
