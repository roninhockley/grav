---
visible: true
title: CICD Project
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,homelab]
---

[toc]

### Goals

The goal of the project is to have a complete pipeline from GIT source all the way to deployment onto a production cluster with AWS using EKS.

GIT -->  Jenkins --> Image on registry --> deploy to staging ( local k8s cluster) --> deploy to AWS EKS kubernetes service

### Progress so far

- **Gitlab server** is online with repos created. It also has a container registry for docker images. It serves out on both SSH and HTTPS with certificates intact.
- **jenkins master** is online in a docker container at http://docker.nikkyrron.com:8081 . I will front this with an nginx reverse proxy later on so we can use a **http://jenkins.nikkyrron.com** URL .

