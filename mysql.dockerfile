# MySQL 8.0 versiyasını debian bazasında istifadə edirik
FROM mysql:8.0-debian

LABEL maintainer="Rasim Aghayev" \
      version="1.0" \
      description="Production-ready MySQL server"

# Əlavə vasitələr və müvafiq asılılıqları quraşdırırıq
RUN apt-get update && \
    apt-get install -y nano procps && \
    rm -rf /var/lib/apt/lists/*  
    # apt cache təmizlənir ki, yer tutmasın

# Xüsusi konfiqurasiyalar üçün direktoriyalar yaradırıq
RUN mkdir -p /etc/mysql/conf.d /docker-entrypoint-initdb.d

# Xüsusi MySQL konfiqurasiya faylını kopyalayırıq
COPY ./server/mysql/my.cnf /etc/mysql/conf.d/

# Əgər varsa başlanğıc SQL skriptlərini kopyalayırıq
COPY ./server/mysql/init.sql /docker-entrypoint-initdb.d/

# MySQL portunu expose edirik
EXPOSE 3306
