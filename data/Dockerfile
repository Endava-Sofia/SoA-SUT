FROM mysql:9.0.1
COPY ./users.sql /docker-entrypoint-initdb.d
ENV MYSQL_ROOT_PASSWORD=pass
EXPOSE 3306/tcp
