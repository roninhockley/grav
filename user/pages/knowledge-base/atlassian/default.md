---
visible: true
title: Atlassian
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [homelab,atlassian]
---

We own Jira Software, Bitbucket and Confluence licenses for 10 users.

### Infrastructure

The Jira infrastructure consists of 4 vm's all based on the Centos 7 template on pve as follows:

- Hostname:  jira
  - 6gb RAM
  - 2 cpu
- Hostname:  confluence
  - 6gb RAM
  - 2 cpu
- Hostname:  bitbucket
  - 6gb RAM
  - 2 cpu
- Hostname:  mysql
  - 6gb RAM
  - 2 cpu



To access the apps, first add an entry to your hosts file:  `192.168.1.206 atlassian`

The URLs are:
- log on to Jira at http://atlassian:8080
- log on to Confluence at http://atlassian:8090
- log on to Bitbucket at http://atlassian:7990
- usernames are ron/nikky with pw nr

For admin purposes of any app you can log on to Jira and at the top left click and get to the others


The repositories sitting on Bitbucket will be documented on a separate page titled Git, under the HOME LAB section of Grav.
