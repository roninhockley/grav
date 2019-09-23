---
visible: true
title: KVM Template for k8s Nodes
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [homelab,kubernetes]
---

#### Debian Stretch


Install docker -  [https://docs.docker.com/install/linux/docker-ce/debian/](https://docs.docker.com/install/linux/docker-ce/debian/)

! Install the preferred version of docker as below
```
sudo apt-get install docker-ce=18.06.2~ce~3-0~debian  containerd.io
```

Install the kubelet stack - [https://kubernetes.io/docs/setup/independent/install-kubeadm/](https://kubernetes.io/docs/setup/independent/install-kubeadm/)

```
apt-get update && apt-get install -y apt-transport-https curl
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | apt-key add -
cat <<EOF >/etc/apt/sources.list.d/kubernetes.list
deb https://apt.kubernetes.io/ kubernetes-xenial main
EOF
apt-get update
apt-get install -y kubelet kubeadm kubectl
apt-mark hold kubelet kubeadm kubectl docker-ce docker-ce-cli
```

! Extra packages:  htop xfsprogs
