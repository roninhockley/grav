---
visible: true
title: Install Rook storage
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [homelab,kubernetes,rook]
---

### Deploy Rook ceph on cluster with dynamic provisioning

Deploy the operator with helm.
! If this is a new cluster the tiller deployment needs to be run  - [helm](../helm)
```
helm repo add rook-stable https://charts.rook.io/stable && \
helm install --namespace rook-ceph-system rook-stable/rook-ceph && \
watch kubectl get all -n rook-ceph-system
```
Create the cluster with the cluster.yaml file from github repo
```
kubectl apply -f ~/git/k8s-github/rook/cluster/examples/kubernetes/ceph/cluster.yaml && \
watch kubectl get all -n rook-ceph
```
!!! This takes a few mins and is done when there are 7 running pods

Provision the CEPH block storage pool and storageclass with the storageclass.yaml file from github repo
```
kubectl apply -f ~/git/k8s-github/rook/cluster/examples/kubernetes/ceph/storageclass.yaml && \
```
Patch the storageclass to create an annotation that makes it the default for dynamic provisioning.
! Use kubectl get storageclass to get the name if you need to.
```
kubectl patch storageclass rook-ceph-block -p '{"metadata": {"annotations":{"storageclass.kubernetes.io/is-default-class":"true"}}}'
```
Let all pods stabilize. Monitor with `watch` command
```
watch kubectl get all --all-namespaces
```

Test with a helm install of mysql
```
helm install stable/mysql
```

If any pods get stuck check the pvc is bound properly
```
kubectl get pvc
```


Install xfsprogs on all nodes to avoid mounting issues with XFS based images.
**NOTE:** This step is part of the build for the k8s template, just mentioning here in case it is not for some reason.
```
sudo apt install xfsprogs
```
