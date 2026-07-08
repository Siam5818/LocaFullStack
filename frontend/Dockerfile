#/* cspell:disable */

#build Vite → Nginx statique

# ── Stage 1 : Build Vite ─────────────────────────────────────────────────────
FROM node:20-alpine AS builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline

COPY . .

# La variable VITE_API_URL sera passée au build via --build-arg
ARG VITE_API_URL=http://localhost/api
ENV VITE_API_URL=$VITE_API_URL

RUN npm run build

# ── Stage 2 : Nginx statique ─────────────────────────────────────────────────
FROM nginx:1.27-alpine AS production

COPY --from=builder /app/dist /usr/share/nginx/html
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]