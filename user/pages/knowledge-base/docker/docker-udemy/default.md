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

- create named volumes for easy recognition.
    docker run -d --name mysql -e MYSQL_ALLOW_EMPTY_PASSWORD=True **-v mysql-db:/var/lib/mysql** mysql