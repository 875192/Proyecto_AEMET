# Práctica de investigación: AEMET → MySQL (notas de trabajo)

Última actualización: 2026-11-03

## Enunciado (resumen)
- Script que consume la API REST de AEMET OpenData (valores climatológicos diarios) para ≥5 ciudades y guarda los datos en MySQL.
- Entregables: código + instrucciones, script SQL (y volcado de datos si es posible), memoria de 1-2 páginas (API key, endpoints usados y por qué, diseño final de BD, dificultades).
- Es la base de la fase 2 (aplicación web), así que el código de acceso a la API y el diseño de BD deben quedar claros y reutilizables.
- XAMPP/phpMyAdmin NO son obligatorios; solo MySQL.
- Web del profesor (solo consulta/contraste, no es la API oficial): https://datosclima.es/Aemethistorico/Meteostation.php

## Decisiones tomadas
- **Lenguaje: Node.js** (por si la fase 2 es full-stack con React; mismo lenguaje en front y back). Librerías: mysql2 + dotenv.
- **Base de datos: Docker** (MySQL 8.4 + phpMyAdmin opcional en localhost:8080), con `docker-compose.yml` que carga `schema.sql` al primer arranque. Para recargar el esquema: `docker compose down -v` y `docker compose up -d`.
- **Esquema**: tablas `ciudades` (id_ciudad, nombre, indicativo, altitud) y `lecturas_diarias` (id_lectura, id_ciudad FK, fecha, temp_max, temp_min, precipitacion + extras: temp_media, precip_inapreciable, vel_media_viento, racha_max_viento, dir_viento, hum_media, hum_max, hum_min, horas_sol, pres_max, pres_min). UNIQUE(id_ciudad, fecha) para poder relanzar el script sin duplicados.
- **Extras añadidos tras ver la API real**: humedad y dirección del viento (el enunciado cita humedad y viento como extras opcionales). La altitud la rellena el ETL desde la respuesta.
- **Campos descartados** de la API: `hora*` (horas de cada extremo) y `pintMax` (intensidad máxima de precipitación); no aportan a los objetivos de la fase 1.
- Ciudades iniciales: Madrid 3195, Barcelona 0201D, Valencia 8416, Zaragoza 9434, Teruel 8368U (**indicativos sin verificar**, comprobar en datosclima.es).
- **Estructura del script ETL**: separada en 4 módulos (`pool.js`, `aemet.js`, `parser.js`, `etl.js`) para que cuando se verifiquen los campos reales de la API solo haya que tocar `parser.js`.
- **Rango de fechas**: últimos 30 días por defecto; configurable con `FECHA_INI` / `FECHA_FIN` en `.env`.
- **`ON DUPLICATE KEY UPDATE`** en el INSERT para poder relanzar el script sin duplicados.
- **Sin reintentos automáticos** en el cliente de la API: se decide mantener la lógica lo más simple posible. Si AEMET falla (`fetch failed` / timeout), se relanza `npm start` a mano.
- El rango de 30 días no viene del enunciado: es decisión propia (el enunciado no fija rango).

## Estado actual
- [x] Schema SQL definido (`schema.sql`)
- [x] Entorno Docker configurado (`docker-compose.yml`)
- [x] Script ETL escrito (`index.js` + `src/`)
- [x] Repositorio GitHub creado y sincronizado
- [x] Docker Desktop instalado y probado
- [x] API key de AEMET recibida (se pasa como parámetro `api_key` en la URL)
- [x] Nombres de campos reales de la API verificados (tmed, prec, tmin, tmax, velmedia, racha, dir, sol, presMax, presMin, hrMedia, hrMax, hrMin, altitud)
- [x] Prueba end-to-end con `npm start`: 133 lecturas cargadas (04/10–31/10/2026)
- [x] Volcado SQL generado (`datos_aemet.sql`)
- [ ] Redactar la memoria

## Observaciones sobre los datos (para la memoria)
- AEMET publica los valores diarios con unos días de retraso: pidiendo hasta el 03/11 el último día disponible es el 31/10.
- Barcelona (0201D) tiene 21 lecturas en vez de 28: faltan días enteros (p. ej. 30/10) y alguno llega incompleto (31/10 solo con viento y humedad).
- Barcelona y Madrid no miden horas de sol; Barcelona tampoco presión. Por eso esas columnas admiten NULL.
- Los valores llegan como texto con coma decimal (`"15,2"`); la precipitación inapreciable llega como `"Ip"` (se guarda 0 + `precip_inapreciable = true`).
- `dir` viene en decenas de grado; `99` significa dirección variable.
- La API es intermitente: hubo timeouts puntuales (`UND_ERR_CONNECT_TIMEOUT`).

## Pendiente
- Verificar que el indicativo 0201D es la estación deseada para Barcelona (datosclima.es).
- Probar y documentar qué responde la API sin API key o con límite de peticiones superado (el enunciado lo pide en la memoria).
- Límite de rango de fechas por petición: no se ha probado más allá de 30 días.
- Redactar la memoria (1-2 páginas): API key, endpoints y por qué, diseño final de BD, dificultades.
- Regenerar `datos_aemet.sql` justo antes de entregar si se recargan datos.
