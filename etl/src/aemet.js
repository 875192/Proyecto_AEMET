// Author: Jaime Fano Alijarde
// Descripcion: Cliente HTTP para la API REST de AEMET OpenData (valores climatológicos diarios).
const BASE_URL = 'https://opendata.aemet.es/opendata';

// Obtiene las lecturas diarias de una estación entre dos fechas (YYYY-MM-DD).
// AEMET requiere dos peticiones: la primera devuelve una URL temporal con los datos.
async function fetchLecturas(indicativo, fechaIni, fechaFin) {
  const apiKey = process.env.AEMET_API_KEY;
  if (!apiKey) throw new Error('AEMET_API_KEY no definida en .env');

  const endpoint =
    `${BASE_URL}/api/valores/climatologicos/diarios/datos` +
    `/fechaini/${fechaIni}T00:00:00UTC` +
    `/fechafin/${fechaFin}T23:59:59UTC` +
    `/estacion/${indicativo}` +
    `?api_key=${apiKey}`;

  // Primera petición: obtiene la URL de datos
  const res1 = await fetch(endpoint);
  if (!res1.ok) throw new Error(`AEMET error ${res1.status} para estación ${indicativo}`);
  const meta = await res1.json();

  if (meta.estado !== 200) {
    throw new Error(`AEMET respuesta inesperada (estado ${meta.estado}): ${meta.descripcion}`);
  }

  // Segunda petición: obtiene el array de lecturas
  const res2 = await fetch(meta.datos);
  if (!res2.ok) throw new Error(`Error al obtener datos de ${meta.datos}`);
  const lecturas = await res2.json();

  return lecturas;
}

module.exports = { fetchLecturas };
