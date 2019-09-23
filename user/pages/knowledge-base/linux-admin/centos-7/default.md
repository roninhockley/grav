---
visible: true
title: Tips and Tricks on CentOS 7
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [centos,linux,git]
---

### Install newer GIT for Bitbucket

This outlines how to get a newer version of GIT on CentOS 7. Atlassian Bitbucket requires a newer version than what ships with CentOS.

Remove existing git from server

    sudo yum remove git

Add the IUS repo

    sudo yum -y install  https://centos7.iuscommunity.org/ius-release.rpm
    sudo yum -y install  git2u-all

Check the git version to confirm

    git --version

Done.
