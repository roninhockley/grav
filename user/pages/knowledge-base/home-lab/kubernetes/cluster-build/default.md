---
visible: true
title: Cluster Build-out
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [homelab,kubernetes]
---
[toc]
## All Nodes

clone Template_k8s_debian9.8

Use these MACS to regain IP reservations:

DEV                         |PROD
:---------------------------|:------------------------
master `52:54:00:29:b6:cc`  |master `52:54:00:56:de:59`
node1 `52:54:00:c8:76:03`   |node1 `52:54:00:80:43:b1`
node2 `52:54:00:c3:a1:81`   |node2 `52:54:00:80:c7:75`
node3 `52:54:00:a1:f0:8c`   |node3 `52:54:00:a0:57:f4`

! Add a second CPU for the master VM

For details on the template used - [template](../template)


## BUILD MASTER

! Source:  [https://kubernetes.io/docs/setup/independent/create-cluster-kubeadm/](https://kubernetes.io/docs/setup/independent/create-cluster-kubeadm/)

Set hostname and update hosts file
```
sudo hostnamectl set-hostname <hostname>
sudo nano /etc/hosts
```

Create kubeadm config file to set pod CIDR and cluster name.

! Source:  [https://godoc.org/k8s.io/kubernetes/cmd/kubeadm/app/apis/kubeadm/v1beta1]( https://godoc.org/k8s.io/kubernetes/cmd/kubeadm/app/apis/kubeadm/v1beta1)

```
nano kubeadm-config.yaml

apiVersion: kubeadm.k8s.io/v1beta1
kind: ClusterConfiguration
networking:
  podSubnet: "10.244.0.0/16"
  dnsDomain: "cluster.local"
clusterName: "<prod/dev>"
```

!!! There is a local copy of this file on Mint at ~/kubeadm-config.yaml

The podSubnet is dependent on the CNI used
- for Canal - 10.244.0.0/16
- for Calico - 172.16.0.0/16
```
sudo kubeadm init --config=kubeadm-config.yaml
mkdir -p $HOME/.kube && \
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config && \
sudo chown $(id -u):$(id -g) $HOME/.kube/config
```

### Setup CNI network add-on


**Canal**
```
kubectl apply -f https://docs.projectcalico.org/v3.3/getting-started/kubernetes/installation/hosted/canal/rbac.yaml && \
kubectl apply -f https://docs.projectcalico.org/v3.3/getting-started/kubernetes/installation/hosted/canal/canal.yaml
```

**Calico**
```
kubectl apply -f https://docs.projectcalico.org/v3.3/getting-started/kubernetes/installation/hosted/rbac-kdd.yaml && \
kubectl apply -f https://docs.projectcalico.org/v3.3/getting-started/kubernetes/installation/hosted/kubernetes-datastore/calico-networking/1.7/calico.yaml
```

Verify network pods started
```
watch kubectl get all -n kube-system
```

!!! For later: find out how to deploy new version **3.7** of Canal with RBAC, as above with **3.3**. For now, staying with **3.3**

## BUILD WORKER NODES

Copy and paste the join command from the init:

**EXAMPLE**
```
kubeadm join 192.168.1.119:6443 --token 6d5ugi.bn2udvnx142vfs7f --discovery-token-ca-cert-hash sha256:11738d669e02f9ea6858ba7b90aba5db6e5cb7596baedf7699df9e0da946b4f0
```

If lost make another one by running this on master:
```
kubeadm token create $(kubeadm token generate) --ttl 3h --print-join-command
```
## PREPARE WORKSTATION FOR NEW CLUSTER

! Source:	[https://kubernetes.io/docs/tasks/access-application-cluster/configure-access-multiple-clusters/](https://kubernetes.io/docs/tasks/access-application-cluster/configure-access-multiple-clusters/)

The local config file at `~/.kube/config` on Mint has the stanzas for both the dev and prod clusters.

! For any cluster reinstalls on either side, the local config file will have to be updated

1. pull the new config for the new cluster to workstation
`scp <master>:~/.kube/config ~/.kube/config`

2. get the cluster cert data and the kubernetes-admin user's cert and key data from the new config file and merge it into the local config file

```
	apiVersion: v1
	clusters:
	- cluster:
		certificate-authority-data:   THE DEV CLUSTER CERT DATA GOES HERE
		server: https://192.168.1.101:6443
	  name: dev
	- cluster:
		certificate-authority-data:  THE PROD CLUSTER CERT DATA GOES HERE
		server: https://192.168.1.148:6443
	  name: prod
	contexts:
	- context:
		cluster: kubernetes
		user: dev
	  name: dev
	- context:
		cluster: prod
		user: prod
	  name: prod
	current-context: prod
	kind: Config
	preferences: {}
	users:
	- name: dev
	  user:
		client-certificate-data: USER CERT FOR DEV CLUSTER GOES HERE
		client-key-data: USER KEY FOR DEV CLUSTER GOES HERE
	- name: prod
	  user:
		client-certificate-data: USER CERT FOR PROD CLUSTER GOES HERE
		client-key-data: USER KEY FOR PROD CLUSTER GOES HERE
```
! MAKE SURE THE CONTEXT IS SET TO THE NEW CLUSTER

If needed reinitialize tiller on the new cluster [Helm](../helm)

Set up the nginx load balancer [nginx](../nginx)


Last step - add rook storage [Rook](../rook)

#### Cluster Shut Down
If you need to shutdown any or all nodes on the cluster you must drain the node/nodes first.
```
kubectl drain {master,node1,node2,node3} --delete-local-data --force --ignore-daemonsets
```
! Sometimes the brace expansion does not work properly and you need to drain them one-by-one

#### Cluster Tear Down
Drain all nodes, remove nodes from cluster, then reset all nodes to remove traces of cluster.
!!! This leaves the nodes intact to reuse for a new cluster.
```
kubectl drain {master,node1,node2,node3} --delete-local-data --force --ignore-daemonsets
kubectl delete node <node name>
kubeadm reset
iptables -F && iptables -t nat -F && iptables -t mangle -F && iptables -X
```
