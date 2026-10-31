// Author: Jaime Fano Alijarde
// Descripcion: 
require('dotenv').config();

const pool             = require('./src/pool');
const { ejecutarETL }  = require('./src/etl');

// Rango de fechas: últimos 30 días por defecto, o pasar FECHA_INI / FECHA_FIN en .env
function formatFecha(d) {
  return d.toISOString().slice(0, 10);
}

const hoy     = new Date();
const hace30  = new Date(hoy);
hace30.setDate(hoy.getDate() - 30);

const fechaIni = process.env.FECHA_INI || formatFecha(hace30);
const fechaFin = process.env.FECHA_FIN || formatFecha(hoy);

(async () => {
  console.log(`Extracción AEMET: ${fechaIni} → ${fechaFin}`);
  try {
    await ejecutarETL(fechaIni, fechaFin);
    console.log('ETL completado.');
  } catch (err) {
    console.error('Error en ETL:', err.message);
    process.exit(1);
  } finally {
    await pool.end();
  }
})();
