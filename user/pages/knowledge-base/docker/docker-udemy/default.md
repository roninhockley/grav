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

