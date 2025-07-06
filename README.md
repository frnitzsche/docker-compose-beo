### Command Line

```shell
$ docker run -d -p 80:80 --name beo-web frnitzsche/beo-web
```

### Docker Compose

```yaml
services:
  web:
    image: frnitzsche/beo-web
    restart: always
    ports:
      - 8081:80
    environment:
      BEO_DB_HOST: db
      BEO_DB_USER: myuser
      BEO_DB_PASSWORD: mypassword
      BEO_DB_NAME: moussala
    depends_on: 
      - db
  db:
    image: mysql:8.0
    restart: always
    ports:
      - 3306:3306
    environment:
      MYSQL_DATABASE: moussala
      MYSQL_USER: myuser
      MYSQL_PASSWORD: mypassword
      MYSQL_ROOT_PASSWORD: mypassword
    volumes:
      - db:/var/lib/mysql
volumes:
  db:
```