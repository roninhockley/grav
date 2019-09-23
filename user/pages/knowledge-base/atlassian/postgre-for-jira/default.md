---
visible: true
title: PostgreSQL for Jira
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [jira,homelab,postgresql]
---


!!! Tried MySQL first. Didn't work. Went with Postgre

An outline of how PostgreSQL is set up for Jira stack.

Set up the postgre repo in yum

    yum install https://download.postgresql.org/pub/repos/yum/9.6/redhat/rhel-7-x86_64/pgdg-centos96-9.6-3.noarch.rpm

Install postgre

    yum install postgresql96
    yum install postgresql96-server

Initialize the database

    /usr/pgsql-9.6/bin/postgresql96-setup initdb

Start and enable the service

    systemctl enable postgresql-9.6
    systemctl start postgresql-9.6

Edit the two config files in `/var/lib/pgsql/9.6/data`

**nano postgresql.conf**

    listen_addresses = '*'  # add this line

**nano pg_hba.conf**

    host  all   all   192.168.1.0/24    md5  # add this line

Restart for changes to pick up

    systemctl restart postgresql-9.6

Open firewall for postgre

    firewall-cmd --permanent --add-port=5432/tcp
    firewall-cmd --reload

Create the user and database per Jira website instructions. Either use psql remotely or pgadmin.
