-- =====================================================================
-- Práctica de investigación: datos meteorológicos AEMET -> MySQL
-- Author: Jaime Fano Alijarde
-- Esquema (fase 1): campos mínimos del enunciado + "otros datos".
-- NOTA: las columnas extra corresponden a campos del endpoint de valores
-- climatológicos diarios de AEMET (tmed, velmedia, racha, dir, sol, presMax,
-- presMin, hrMedia, hrMax, hrMin), verificados contra respuestas reales.
-- Todas las medidas admiten NULL: no todas las estaciones miden todo
-- (p. ej. Madrid y Barcelona no informan de sol; Barcelona tampoco de presión).
-- =====================================================================

CREATE DATABASE IF NOT EXISTS meteo_aemet
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE meteo_aemet;

-- ---------------------------------------------------------------------
-- Tabla ciudades: una fila por ciudad, vinculada a su estación AEMET.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ciudades (
  id_ciudad   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(100) NOT NULL,   -- p. ej. "Zaragoza"
  indicativo  VARCHAR(10)  NOT NULL,   -- idema de AEMET (puede llevar letras: "0201D")
  altitud     SMALLINT     NULL,       -- m sobre el nivel del mar (altitud de la estación, la rellena el ETL)
  PRIMARY KEY (id_ciudad),
  UNIQUE KEY uq_ciudades_indicativo (indicativo)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabla lecturas_diarias: un registro por estación y día.
-- Las medidas admiten NULL porque AEMET no siempre informa todos los datos.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lecturas_diarias (
  id_lectura          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_ciudad           INT UNSIGNED    NOT NULL,
  fecha               DATE            NOT NULL,
  -- Campos del enunciado
  temp_max            DECIMAL(4,1)    NULL,   -- °C (tmax)
  temp_min            DECIMAL(4,1)    NULL,   -- °C (tmin)
  precipitacion       DECIMAL(5,1)    NULL,   -- mm (prec)
  -- Otros datos
  temp_media          DECIMAL(4,1)    NULL,   -- °C (tmed)
  precip_inapreciable BOOLEAN         NOT NULL DEFAULT FALSE, -- AEMET devuelve "Ip" (<0,1 mm)
  vel_media_viento    DECIMAL(4,1)    NULL,   -- m/s (velmedia)
  racha_max_viento    DECIMAL(4,1)    NULL,   -- m/s (racha)
  horas_sol           DECIMAL(4,1)    NULL,   -- horas (sol)
  pres_max            DECIMAL(5,1)    NULL,   -- hPa (presMax)
  pres_min            DECIMAL(5,1)    NULL,   -- hPa (presMin)
  dir_viento          TINYINT UNSIGNED NULL,  -- decenas de grado, 0-36 (dir); 99 = dirección variable
  hum_media           TINYINT UNSIGNED NULL,  -- % humedad relativa media (hrMedia)
  hum_max             TINYINT UNSIGNED NULL,  -- % humedad relativa máxima (hrMax)
  hum_min             TINYINT UNSIGNED NULL,  -- % humedad relativa mínima (hrMin)
  PRIMARY KEY (id_lectura),
  -- Una sola lectura por ciudad y día: permite relanzar el script sin duplicar
  UNIQUE KEY uq_lectura_ciudad_fecha (id_ciudad, fecha),
  KEY idx_lecturas_fecha (fecha),
  CONSTRAINT fk_lecturas_ciudad
    FOREIGN KEY (id_ciudad) REFERENCES ciudades (id_ciudad)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Datos iniciales. COMPROBAR los indicativos en datosclima.es o con el
-- inventario de estaciones de AEMET antes de usarlos.
-- ---------------------------------------------------------------------
INSERT INTO ciudades (nombre, indicativo) VALUES
  ('Madrid',    '3195'),
  ('Barcelona', '0201D'),
  ('Valencia',  '8416'),
  ('Zaragoza',  '9434'),
  ('Teruel',    '8368U')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
