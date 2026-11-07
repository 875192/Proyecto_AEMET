// Author: Jaime Fano Alijarde
// Descripcion: Punto de entrada del ETL. Descarga el histórico de AEMET por tramos y lo guarda en MySQL.
//
// Uso:
//   npm start                 -> descarga el rango completo (por defecto, los últimos HISTORICO_ANIOS años)
//   npm run actualizar        -> continúa desde la última lectura guardada de cada ciudad (solape de 7 días)
//
// Variables opcionales en .env: FECHA_INI, FECHA_FIN (YYYY-MM-DD), HISTORICO_ANIOS (10 por defecto),
// TRAMO_DIAS (180), PAUSA_MS (1500), MAX_INTENTOS (5), ESPERA_429_MS (60000), ESPERA_ERROR_MS (5000).

// El .env está en la raíz del repositorio (lo comparten docker-compose y el ETL)
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const pool            = require('./src/pool');
const { ejecutarETL } = require('./src/etl');

const formatFecha = (d) => d.toISOString().slice(0, 10);

const hoy       = new Date();
const anios     = Number(process.env.HISTORICO_ANIOS) || 10;
const haceAnios = new Date(hoy);
haceAnios.setUTCFullYear(hoy.getUTCFullYear() - anios);

const fechaIni = process.env.FECHA_INI || formatFecha(haceAnios);
const fechaFin = process.env.FECHA_FIN || formatFecha(hoy);

const opciones = {
  reanudar:  process.argv.includes('--reanudar'),
  tramoDias: Number(process.env.TRAMO_DIAS) || 180,
  pausaMs:   Number(process.env.PAUSA_MS) || 1500,
};

(async () => {
  console.log(`Extracción AEMET: ${fechaIni} → ${fechaFin}${opciones.reanudar ? ' (reanudando)' : ''}`);
  let codigoSalida = 0;
  try {
    const resumen = await ejecutarETL(fechaIni, fechaFin, opciones);
    console.log(`\nETL completado: ${resumen.insertadas} lecturas insertadas/actualizadas.`);
    if (resumen.fallos.length) {
      codigoSalida = 1;
      console.error(`\n${resumen.fallos.length} tramo(s) fallaron. Para reintentarlos, relanza "npm start" (no duplica datos)`);
      console.error('o ajusta FECHA_INI y FECHA_FIN en el .env al rango del tramo fallido:');
      for (const f of resumen.fallos) {
        console.error(`  - ${f.ciudad} ${f.ini} → ${f.fin}: ${f.error}`);
      }
    }
  } catch (err) {
    console.error('Error en ETL:', err.message);
    codigoSalida = 1;
  } finally {
    await pool.end();
  }
  process.exit(codigoSalida);
})();
