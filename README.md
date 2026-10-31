# Proyecto AEMET — Extracción de datos meteorológicos a MySQL

**Autor:** Jaime Fano Alijarde ([jaimefano10@gmail.com](mailto:875192@unizar.es)) — Universidad de Zaragoza  
**Fecha de creación:** Octubre 2026  
**Asignatura:** Sistemas de Información · Práctica de investigación 

---

## Descripción

Script en **Node.js** que consume la API REST de [AEMET OpenData](https://opendata.aemet.es) para obtener valores climatológicos diarios de 5 ciudades españolas y los almacena en una base de datos **MySQL**.

Este proyecto es la **fase 1** de una práctica en dos partes; la fase 2 construirá una aplicación web sobre estos mismos datos.

## Ciudades monitorizadas

| Ciudad | Indicativo AEMET |
|---|---|
| Madrid | 3195 |
| Barcelona | 0201D |
| Valencia | 8416 |
| Zaragoza | 9434 |
| Teruel | 8368U |

## Datos recogidos por ciudad y día

Temperatura máxima, mínima y media · Precipitación · Velocidad media y racha máxima del viento · Horas de sol · Presión máxima y mínima.

## Estructura del repositorio

```
├── src/
│   ├── pool.js              # Pool de conexiones MySQL reutilizable para toda la aplicación
│   ├── aemet.js             # Cliente HTTP para la API REST de AEMET OpenData
│   ├── parser.js            # Transforma la respuesta cruda de AEMET al formato del schema
│   └── etl.js               # Orquesta la extracción, transformación e inserción de datos
├── index.js                 # Punto de entrada: carga .env, define rango de fechas y arranca el ETL
├── schema.sql               # Esquema de la base de datos (tablas ciudades + lecturas_diarias)
├── docker-compose.yml       # MySQL 8.4 + phpMyAdmin en Docker
├── package.json             # Dependencias Node.js (mysql2, dotenv)
├── .env.example             # Variables de entorno necesarias (copiar a .env y ajustar)
└── notas-practica-aemet.md  # Notas internas de desarrollo y decisiones tomadas
```

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Node.js 20+
- API key de AEMET OpenData (solicitar en [opendata.aemet.es](https://opendata.aemet.es))

## Puesta en marcha

```bash
# 1. Clonar el repositorio
git clone https://github.com/875192/Proyecto_AEMET.git
cd Proyecto_AEMET

# 2. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus contraseñas y API key

# 3. Levantar la base de datos
docker compose up -d
# MySQL disponible en localhost:3306
# phpMyAdmin disponible en http://localhost:8080
```

> El archivo `schema.sql` se ejecuta automáticamente la primera vez que se crea el volumen.  
> Para reiniciar desde cero: `docker compose down -v && docker compose up -d`
