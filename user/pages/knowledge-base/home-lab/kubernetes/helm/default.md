---
visible: true
title: Helm
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [kubernetes,helm,homelab]
---


Source:  [github helm page](https://github.com/helm)


### Install Helm
!!! This is done on the workstation

Get the package and deploy the executable to /usr/local/bin

```
wget https://storage.googleapis.com/kubernetes-helm/helm-v2.14.1-linux-amd64.tar.gz
tar zxf helm-v2.14.1-linux-amd64.tar.gz
sudo mv linux-amd64/helm /usr/local/bin
rm -rf ../helm-v2.14.1-linux-amd64.tar.gz
rm -rf ../linux-amd64/
	```


#### For new clusters

Initialize tiller and create the RBAC roles
```
helm init && \
kubectl create serviceaccount --namespace kube-system tiller && \
kubectl create clusterrolebinding tiller-cluster-rule --clusterrole=cluster-admin --serviceaccount=kube-system:tiller && \
kubectl patch deploy --namespace kube-system tiller-deploy -p '{"spec":{"template":{"spec":{"serviceAccount":"tiller"}}}}'
```

Update the helm repo
```
helm repo update
```

#### To customize a Helm installation before installing
```
helm inspect values stable/mediawiki
	```

This will show what parameters are configurable

You can then override any of these settings in one of 2 ways:

1. In a YAML formatted file:, and then pass that file during installation:
```
$ cat << EOF > config.yaml
mariadbUser: user0
mariadbDatabase: user0db
EOF
helm install -f config.yaml stable/mariadb
```

2. On the command line:
```
helm install --set name=value stable/mariadb
```

Multiple values are comma separated
```
helm install --set name=value,name=value stable/mariadb
```

!!! The YAML equivalent of `name=value` is:  `name: value`
