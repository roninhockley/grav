---
visible: true
title: Nginx Ingress Controller setup
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [homelab,kubernetes,nginx]
---

Sources:
- https://github.com/helm/charts/tree/master/stable/nginx-ingress  -  for config parameters to use in values.yaml
- https://kubernetes.github.io/ingress-nginx/deploy/baremetal/  -  for guidance on what configuration to use for a baremetal cluster

Deploy the controller from helm with values file
```
helm install -f /home/ron/on-prem/nginx-ingress/values.yaml stable/nginx-ingress
```
Deploy the ingress resource file
```
kubectl apply -f /home/ron/on-prem/ingress-rules/ingress.yaml
```

#### The values file

Use a `values.yaml` file to create a DaemonSet using the host network:
```yaml
controller:
  kind: DaemonSet
  hostNetwork: true
```

This will bind port 80 and/or 443 of the node itself to the nginx pod running on it.

#### The ingress resource

The ingress resource used above:
```yaml
apiVersion: extensions/v1beta1
kind: Ingress
metadata:
  name: joomla-rules
spec:
  rules:
  - host: joomla.example.com
	http:
	  paths:
	  - path: /
		backend:
		  serviceName: cloying-bronco-joomla
		  servicePort: 80
  - host: drupal.example.com
	http:
	  paths:
	  - path: /
		backend:
		  serviceName: illocutionary-crocodile-drupal
		  servicePort: 80
```
- the ingress can point to port 80 of the service since the controller pods are binded directly to the host network.
- since the controller is deployed as a daemonset, the hostfile or DNS entry can be URL without specifying any port
