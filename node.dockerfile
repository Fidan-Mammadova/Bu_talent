# Build Stage
FROM node:23-alpine AS builder

WORKDIR /var/www/html/fe

# Quraşdırılacaq build asılılıqları
RUN apk add --no-cache python3 make g++ bash libc6-compat

# Lazımi faylları kopyalayırıq
COPY src/fe/package*.json ./
COPY src/fe/vite.config.js ./
COPY src/fe/tailwind.config.js ./
COPY src/fe/index.html ./
COPY src/fe/src ./src

# Asılılıqları quraşdırırıq və layihəni qururuq
RUN npm install --include=dev && \
    npm run build

# Runtime Stage
FROM node:23-alpine

WORKDIR /var/www/html/fe

# Build mərhələsindən yalnız lazımlı faylları və assets-ləri kopyalayırıq
COPY --from=builder /var/www/html/fe/dist ./dist
COPY src/fe/package*.json ./

# Yalnız istehsal üçün asılılıqları və lazım olan vasitələri quraşdırırıq
RUN npm install --production && \
    npm cache clean --force && \
    npm install -g serve && \
    addgroup -S nodejs && adduser -S nodejs -G nodejs && \
    chown -R nodejs:nodejs . && \
    rm -rf /root/.npm /root/.cache /tmp/* /var/cache/apk/*

USER nodejs

# Sağlamlıq yoxlaması (Healthcheck)
HEALTHCHECK --interval=30s --timeout=3s \
    CMD wget --no-verbose --tries=1 --spider http://localhost:3000/ || exit 1

# Yığılmış assets-ləri serve etmək üçün çevrə dəyişkənini müəyyən edirik
ENV HOST=0.0.0.0

# Portu expose edirik
EXPOSE 3000

# Yığılmış assets-ləri serve etmək üçün serve istifadə edirik
CMD ["serve", "-s", "dist", "-l", "3000"]
