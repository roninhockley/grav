---
visible: true
title: Purposeful Scripts
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [bash,scripting]
---

The scripts on this page are all that I have written for a specific purpose, such as for KVM, Kubernetes etc.

### Stop jira stack
```bash
#!/bin/bash

bitbucket="/opt/atlassian/bitbucket/6.1.2/bin/"
jira="/opt/atlassian/jira/bin/"
confluence="/opt/atlassian/confluence/bin/"
usage="Usage: stopjira -j for jira, -c for confluence -b for bitbucket -a for all"

if [[ $# = 0 ]]; then
	echo "${usage}"
	exit 0
fi

optstring=":jcba"
while getopts ${optstring} options; do
  case ${options} in
    j)
        echo "Stopping jira"
	${jira}stop-jira.sh
	exit 0
      ;;
    c)
        echo "Stopping confluence"
	${confluence}stop-confluence.sh
	exit 0
      ;;
    b)
	echo "Stopping bitbucket"
	${bitbucket}stop-bitbucket.sh
	exit 0
      ;;
    a)
	echo "Stopping entire jira stack"
	${bitbucket}stop-bitbucket.sh
	${confluence}stop-confluence.sh
	${jira}stop-jira.sh
	exit 0
	;;
    ?)
	echo "Invalid option: -${OPTARG}."
	echo "${usage}"
	exit 1
      ;;
esac
done
```

### Sync local repo for Centos 7

```bash
#!/bin/bash

reposync="reposync --gpgcheck --plugins --newest-only --delete --downloadcomps --download-metadata --download_path=/var/www/html/repos/"
synclog="/tmp/synclog"
repodir="/var/www/html/repos"
pkgs="createrepo yum-utils httpd"
repos="base centosplus extras update"

# Check for needed packages
for pkg in ${pkgs}; do
 echo "Checking for yum package ${pkg}"
 rpm -qa | grep ${pkg}
  if [[ ! $? = 0 ]]; then
    echo "Package ${pkg} not found, installing"
    yum -y install ${pkg}
    echo "Package ${pkg} installed successfully"
  else
    echo "Required package ${pkg} is already present"
  fi
done

#Check for existing repo directory or create if nonexistent
for dir in ${repos}; do
  mkdir ${repodir}/${dir} || echo "Directory ${dir} already exists"
done

#Create log file for sync if needed
if [[ ! -f ${synclog} ]]; then
  touch ${synclog}
fi

#Create or update all branches of repo
for repoid in ${repos}; do

  case ${repoid} in
    base)
    ${reposync} --repoid=${repoid}
    createrepo ${repodir}/${repoid}/ -g comps.xml
    echo "$(date): processed ${repoid}" >> ${synclog}
    ;;
    centosplus)
    ${reposync} --repoid=${repoid}
    createrepo ${repodir}/${repoid}/
    echo "$(date): processed ${repoid}" >> ${synclog}
    ;;
    extras)
    ${reposync} --repoid=${repoid}
    createrepo ${repodir}/${repoid}/
    echo "$(date): processed ${repoid}" >> ${synclog}
    ;;
    update)
    ${reposync} --repoid=${repoid}
    createrepo ${repodir}/${repoid}/
    echo "$(date): processed ${repoid}" >> ${synclog}
    ;;
    *)
    echo "$(date): Error: Pls remove file/folder: ${repoid} from repos folder and rerun this script." | tee /tmp/synclog
    exit 1
    ;;
  esac
done

#Set SElinux for webroot
echo "Setting SELinux context for webroot"
chcon -vR -t httpd_sys_content_t /var/www/html/

#Insert newline in log file after results
echo "===============" >> ${synclog}

```

### Provision KVM via libvirt and virt-install

This script, when complete, will connect via SSH to the KVM host and provision a set number of vm's with supplied parameters for name, and all resource counts such as RAM and CPU, as well as disk of stated size and type.
