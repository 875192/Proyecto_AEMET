# Imagen del ETL: Node.js 20 con las dependencias del proyecto.
FROM node:20-alpine

# La zona horaria (TZ en docker-compose) la resuelve Node con sus datos ICU integrados,
# así que no hace falta instalar tzdata.
# Docker Desktop no siempre enruta IPv6: que Node (y npm) prueben primero IPv4.
ENV NODE_OPTIONS=--dns-result-order=ipv4first

WORKDIR /app

# Primero solo los ficheros de dependencias, para aprovechar la caché de Docker.
COPY package.json package-lock.json ./
RUN npm ci --omit=dev

COPY . .

# Por defecto arranca el programador: actualiza al arrancar y después cada día.
# Uso manual: "node index.js" (histórico completo) o "npm run actualizar".
CMD ["node", "scheduler.js"]
