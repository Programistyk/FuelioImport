FROM node:18-alpine AS frontend-builder
ARG VITE_MAX_FILE_SIZE_MB=20
WORKDIR /app
COPY package*.json ./
COPY index.html ./
COPY vite.config.js ./
COPY tailwind.config.js ./
RUN npm install
COPY src/ ./src
COPY web/ ./web
RUN VITE_MAX_FILE_SIZE_MB=${VITE_MAX_FILE_SIZE_MB} npm run build

FROM programistyk/php-runtimes:7.2-nginx
ADD ./lib /app/lib
ADD ./view/error_template.php /app/view/error_template.php
ADD ./web /app/web
ADD ./LICENSE /app
ADD config.nginx /etc/nginx/sites-enabled/default
ADD fuelio.ini /usr/local/etc/php/conf.d/fuelio.ini
COPY --from=frontend-builder /app/dist /app/web