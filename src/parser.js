// Author: Jaime Fano Alijarde
// Descripcion: Convierte un valor numérico que AEMET devuelve como string con coma decimal.
// Devuelve null si el valor está ausente.
function parseNum(val) {
  if (val === undefined || val === null || val === '') return null;
  return parseFloat(String(val).replace(',', '.'));
}

// Transforma una lectura cruda de AEMET al objeto que espera el INSERT.
// Los nombres de campo (tmax, tmin, tmed...) se verificarán con la respuesta real.
function parseLectura(raw) {
  const esInapreciable = raw.prec === 'Ip';

  return {
    fecha:               raw.fecha,
    temp_max:            parseNum(raw.tmax),
    temp_min:            parseNum(raw.tmin),
    temp_media:          parseNum(raw.tmed),
    precipitacion:       esInapreciable ? 0 : parseNum(raw.prec),
    precip_inapreciable: esInapreciable,
    vel_media_viento:    parseNum(raw.velmedia),
    racha_max_viento:    parseNum(raw.racha),
    horas_sol:           parseNum(raw.sol),
    pres_max:            parseNum(raw.presMax),
    pres_min:            parseNum(raw.presMin),
  };
}

module.exports = { parseLectura };
