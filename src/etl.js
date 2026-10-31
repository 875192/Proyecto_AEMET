// Author: Jaime Fano Alijarde
// Descripcion: 
const pool           = require('./pool');
const { fetchLecturas } = require('./aemet');
const { parseLectura }  = require('./parser');

const INSERT_SQL = `
  INSERT INTO lecturas_diarias
    (id_ciudad, fecha, temp_max, temp_min, temp_media,
     precipitacion, precip_inapreciable,
     vel_media_viento, racha_max_viento, horas_sol, pres_max, pres_min)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ON DUPLICATE KEY UPDATE
    temp_max            = VALUES(temp_max),
    temp_min            = VALUES(temp_min),
    temp_media          = VALUES(temp_media),
    precipitacion       = VALUES(precipitacion),
    precip_inapreciable = VALUES(precip_inapreciable),
    vel_media_viento    = VALUES(vel_media_viento),
    racha_max_viento    = VALUES(racha_max_viento),
    horas_sol           = VALUES(horas_sol),
    pres_max            = VALUES(pres_max),
    pres_min            = VALUES(pres_min)
`;

async function procesarCiudad(idCiudad, indicativo, nombre, fechaIni, fechaFin) {
  console.log(`[${nombre}] Descargando ${fechaIni} → ${fechaFin}...`);

  const raw = await fetchLecturas(indicativo, fechaIni, fechaFin);
  console.log(`[${nombre}] ${raw.length} lecturas recibidas.`);

  const conn = await pool.getConnection();
  try {
    for (const item of raw) {
      const l = parseLectura(item);
      await conn.execute(INSERT_SQL, [
        idCiudad, l.fecha, l.temp_max, l.temp_min, l.temp_media,
        l.precipitacion, l.precip_inapreciable,
        l.vel_media_viento, l.racha_max_viento, l.horas_sol,
        l.pres_max, l.pres_min,
      ]);
    }
    console.log(`[${nombre}] Insertadas/actualizadas ${raw.length} filas.`);
  } finally {
    conn.release();
  }
}

async function ejecutarETL(fechaIni, fechaFin) {
  const [ciudades] = await pool.query('SELECT id_ciudad, nombre, indicativo FROM ciudades');

  for (const ciudad of ciudades) {
    await procesarCiudad(ciudad.id_ciudad, ciudad.indicativo, ciudad.nombre, fechaIni, fechaFin);
  }
}

module.exports = { ejecutarETL };
