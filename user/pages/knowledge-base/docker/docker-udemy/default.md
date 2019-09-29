---
visible: true
title: Docker Mastery
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [docker,containers,linux]
---

[toc]

Notes from the Udemy course **Docker mastery: The Complete Toolset from a Docker Captain.

## Persistent Data

### Volumes

If there is a `VOLUME /path` statement in the dockerfile, that will be a persistent volume under */var/lib/docker/volumes/<hash>/_data*.

For an example, the official mysql image has `VOLUME /var/lib/mysql` in its dockerfile.

If the container is run with no -v options, there will be a volume created under **/var/lib/docker/volumes** and the  volume will have an *ugly* name, a hash. Its path will be **/var/lib/docker/volumes/<hash>/_data**. 

This can be made into a friendly name on the **docker run** line with `-v <friendlyname>:</path>` such as `-v mysql-db:/var/lib/mysql`. Then it will be **/var/lib/docker/volumes/mydql-db/_data**

- create named volumes for easy recognition.

     docker run -d --name mysql -e MYSQL_ALLOW_EMPTY_PASSWORD=True **-v mysql-db:/var/lib/mysql** mysql

### Bind mounts

Mount a folder on host to a container. This also works if the dockerfile has a declared `VOLUME /path`. 

  docker run -d --name mysql -e MYSQL_ALLOW_EMPTY_PASSWORD=True **-v /host/path:/var/lib/mysql** mysql

The use case for bind mounts would be allowing the host to manipulate data in a directory and have the container see the chantges immediately. For instance, you run a build and the artifact goes to the bind-mounted directory and the container gets the new artifact immediately.

## Docker Compose

Compose allows you to build a system of containers that use each other, such as a new deployment of drupal. You need a container to run the webserver to serve out the PHP, and another container to run the database. So you use a compose file to declare all the containers (services)  and the volumes and ports that are needed.

Example for drupal:

    version: '2'

    services:
      drupal:
        image: drupal:8.2
        ports:
          - "8080:80"
        volumes:
          - drupal-modules:/var/www/html/modules
          - drupal-profiles:/var/www/html/profiles       
          - drupal-sites:/var/www/html/sites      
          - drupal-themes:/var/www/html/themes
      postgres:
        image: postgres:9.6
        environment:
          - POSTGRES_PASSWORD=mypasswd

    volumes:
      drupal-modules:
      drupal-profiles:
      drupal-sites:
      drupal-themes:

