# Práctica de investigación: AEMET → MySQL (notas de trabajo)

Última actualización: 2026-10-30

## Enunciado (resumen)
- Script que consume la API REST de AEMET OpenData (valores climatológicos diarios) para ≥5 ciudades y guarda los datos en MySQL.
- Entregables: código + instrucciones, script SQL (y volcado de datos si es posible), memoria de 1-2 páginas (API key, endpoints usados y por qué, diseño final de BD, dificultades).
- Es la base de la fase 2 (aplicación web), así que el código de acceso a la API y el diseño de BD deben quedar claros y reutilizables.
- XAMPP/phpMyAdmin NO son obligatorios; solo MySQL.
- Web del profesor (solo consulta/contraste, no es la API oficial): https://datosclima.es/Aemethistorico/Meteostation.php

## Decisiones tomadas
- **Lenguaje: Node.js** (por si la fase 2 es full-stack con React; mismo lenguaje en front y back). Librerías previstas: fetch/axios + mysql2.
- **Base de datos: Docker** (MySQL 8.4 + phpMyAdmin opcional en localhost:8080), con `docker-compose.yml` que carga `schema.sql` al primer arranque. Para recargar el esquema: `docker compose down -v` y `docker compose up -d`.
- **Esquema**: tablas `ciudades` (id_ciudad, nombre, indicativo) y `lecturas_diarias` (id_lectura, id_ciudad FK, fecha, temp_max, temp_min, precipitacion + extras: temp_media, precip_inapreciable, vel_media_viento, racha_max_viento, horas_sol, pres_max, pres_min). UNIQUE(id_ciudad, fecha) para poder relanzar el script sin duplicados.
- Ciudades iniciales: Madrid 3195, Barcelona 0201D, Valencia 8416, Zaragoza 9434, Teruel 8368U (**indicativos sin verificar**, comprobar en datosclima.es).

## Archivos en la carpeta de la práctica
schema.sql, docker-compose.yml, .env.example, .gitignore (más el enunciado en PDF).

## Pendiente / por verificar
- La API key aún no ha llegado por correo (revisar spam, probar con otro correo, avisar al profesor si sigue sin llegar).
- Instalar Docker Desktop y probar `docker compose up -d`.
- Verificar con la primera respuesta real de la API los nombres de campos (tmed, prec, tmin, tmax, velmedia, racha, sol, presMax, presMin), el límite de rango de fechas por petición y cómo se pasa la api_key. La documentación oficial no se pudo leer desde la sesión.
- Escribir el script de extracción en Node.js (dos peticiones: primero devuelve URL "datos", luego se piden los datos; "Ip" en precipitación = inapreciable; valores con coma decimal en texto).
- Redactar la memoria.
