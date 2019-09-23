---
visible: true
title: Docker Deep Dive
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [docker]
---
[toc]
This section is derived from the book **Docker Deep Dive** by Nigel Poulton

#### Install docker
- **curl -fsSL get.docker.com -o get-docker.sh**
- **chmod +x get-docker.sh**
- **sudo sh get-docker.sh**
- **sudo usermod -aG docker ron**

### Docker Basics

#### BUILD AN IMAGE AND RUN IT IN A CONTAINER

1.  clone the repo  —  git clone https://github.com/nigelpoulton/psweb.git
2.  cd into the directory and build the image —  docker image build -t test:latest .  The test:latest  is the name:tag that you choose for the image
3.  verify the image is there — docker image ls
	   REPO  	  		TAG 	 		 IMAGE ID    		CREATED				SIZE
	   test				latest			f154cb3ddbd4		1 minute ago			55.6MB

4.  now run the image in a container   — docker container run -d --name web1 --publish 8080:8080 test:latest
								| command to run      |  run detached  |    name of container  |  port to expose  <host:container>  |  name of image

5.  open browser and go to http://<dockerhost>:8080

#### IMAGES

docker image pull  —  download images from repositories like docker hub
docker image ls  — list all images stored on the host in local cache
docker image inspect — show details for an image like layers and where it is stored
docker image rm — delete image. image must not be running in a container

#### CONTAINERS

docker container run — start a new container
docker container ls — list all running containers. add the -a flag to see all running or not
Ctrl - PQ — detach from the terminal of a running container
docker container exec — run a new command inside a container. to attach to a container  —  docker container exec -it <container> bash
docker container start/stop  — start or stop an existing container
docker container rm — delete a stopped container. you should stop it first
docker container inspect  — view detailed info including config and runtime

#### TROUBLESHOOTING CONTAINERS

view daemon logs  —   journalctl -u docker
view container logs — docker container logs <containername>
view service logs — docker service logs <servicename>    ( for swarm services )

#### CONTAINERIZE AN APP

docker image build — read a Dockerfile and containerize an application
INSIDE THE DOCKERFILE:

FROM:  the base image to start from , usually the first command in a dockerfile
RUN:  run commands in the image which create new layers
COPY:  copies files into the image such as application code
EXPOSE:  set the netowrk port the app will use
ENTRYPOINT:  set the default app to run when the container is started

### DOCKER COMPOSE

docker compose uses a single yaml file to buld a multi-container application

docker-compose up — deploy a compose app using the compose file ( usually docker-compose.yaml )
docker-compose stop — stop all containers in a compose app without deleting them
docker-compose rm — delete a stopped compose app. deletes containers and networks but not volumes and images
docker-compose restart — restart a compose app that has been stopped with the docker-compose stop command
docker-compose ps — list each container in a compose app
docker-compose down — stop and delete a running compose app. will not delete volumes and image, only containers and networks

### DOCKER SWARM

build out all docker hosts
if using firewall ensure following ports are open:
   2377/tcp
   7946/tcp
   4789/udp

#### INITIALIZE SWARM NODES

log on to mgr and initialize new swarm
	docker swarm init \
    --advertise-addr <mgr IP>:2377 \
    --listen-addr <mgr IP>:2377

join tokens will be presented in the output. run the suggested command on all nodes to be joined

on the mgr list the swarm nodes
docker node ls

#### AUTOLOCK THE SWARM
restarting an old mgr can break the swarm config
to prevent this autolock the swarm
	 docker swarm update --autolock=true
a key will be provided that must be used to unlock a swarm manager after it restarts
	  SWMKEY-1-OCYxdM71I4/XYqo0ilZk124s3BXs9cfO5h+P7OE7+7k


#### SWARM SERVICES

A swarm service is much like a regular container but it can be replicated and can scale up or down
new services are created with the docker service create command
		docker service create --name web-fe \
       -p 8080:8080 \
       --replicas 5 \
       nigelpoulton/pluralsight-docker-ci

to see all services running on a swarm
	docker service ls

to see all service replicas and the state of each
	docker service ps

to see a service config
	docker service inspect --pretty web-fe
NOTE:  the --pretty shortens it. otherwise it is more verbose

#### MODES OF REPLICATION

there are 3 modes of replication for a docker service
	 replicated — the default which puts the declared number of replicas evenly across the hosts
	 global — this mode puts a single replica on every node in the cluster


#### SCALING A SERVICE

to rescale a service
   docker service scale web-fe=5

#### REMOVE A SERVICE

	docker service rm web-fe


#### ROLLING UPDATES

Update the uber-svc service from v1 to v2:

	docker service update \
	--image nigelpoulton/tu-demo:v2 \    <---  the image to update to
	--update-parallelism 2 \  <-----  update 2 replicas at a time
	--update-delay 20s uber-svc  — put a twenty second delay between updates


#### SWARM TROUBLESHOOTING

view swarm service logs
	docker service logs <service name>

the logs can be followed with --follow      tailed with --tail   or get extra details with  --details

#### DOCKER NETWORKING

there are 3 network drivers shipped with docker
	bridge — single host driver will only connect containers on the same host. the default is called docker0
	overlay — multi-host driver will connect containers on different hosts
	MACVLAN — gives the container its own MAC and IP and connects it directly to the physical network. host must be in promiscuous mode

NOTE: it is possible to work around the host only limitation of the bridge with the host:container port mapping, but this does not scale well and monopolizes the port number of the host.

#### DOCKER NETWORK COMMANDS

docker network ls — list all networks on the local docker host
docker network create — create a new network. default is the bridge unless the -d <driver> is specified
docker network inspect — get details about a network config
docker network prune — will delete all unused networks on a host
docker network rm — delete a specific network on a host

#### create an overlay network.

 an overlay network is a layer 2 network that spans across networks that are routed
	docker network create -d overlay uber-net

create a new service and attach it to the overlay network
	docker service create --name uber-svc \
	--network uber-net \
	-p 80:80 --replicas 12 \
	nigelpoulton/tu-demo:v1

NOTE: the -p 80:80  publishes a port on every node even if they are not running a replica. it is called ingress mode and is the default.
In ingress mode a node not running the app will forward the request to a node that is running it.
 NOTE:  with ingress mode you also get load balancing

The other method is called host mode and only publishes ports that the app is actually running on.

To publish in host mode the create command looks like this:
	docker service create --name uber-svc \
	--network uber-net \
	--publish published=80,target=80,mode=host \
	--replicas 12 \
	nigelpoulton/tu-demo:v1


#### TROUBLESHOOT NETWORKING

for systemd
   journalctl -u docker.service  — view daemon logs
   docker container logs <containername>  — view container logs
   docker service logs <servicename>  — view service logs

#### SERVICE DISCOVERY

service discovery allows containers and swarm services to find each other name as long as they are on the same network.
it uses the embedded DNS server to do this. also the DNS resolver built into all containers.
NOTE:  the container must be created with either the --name or the --net-alias  flag  for it to be registered in the DNS server
it is possible to pass config options to the container or service such as --dns for custom dns servers or --dns-search for a search domain

#### OVERLAY NETWORKING

allows containers on different hosts even different networks to connect on a flat layer 2 network
when you create a new overlay network it is only initially available on the node it is created on.
if you then create a new service and attach it to the overlay network any other containers running on other nodes will then see the network

#### PROOF OF CONCEPT
1) create the overlay network - docker network create -d overlay uber-net   - on the mgr node. it will not be visible on othe worker nodes
2)  create a new service that will have 4 replicas. this will cause them to be running on 4 different nodes -
	docker service create --name test \
	--network uber-net \
	--replicas 4 \
	ubuntu sleep infinity
3)  on node3 do a docker network ls  -  and since a container from the service will be running on it it will show the new overlay network now

NOTE:  if you do a docker network inspect uber-net  it will show the subnet of the network and it will be 10.0.0.0/24  this is the overlay network running on layer 2 so it will cross routers

#### VOLUMES AND PERSISTENT DATA

2 types of data in docker - persistent and non-persistent
every docker container automatically gets its own non-persistent storage and it lasts the lifecycle of the container itself
to get data from a container to persist it has to go on a volume
volumes are decoupled from containers and remain intact after the container is deleted or stopped

#### CREATE A NEW LOCAL VOLUME

docker volume create myvol
without specifying a driver it will use the local driver by default which means it will live on the host itself  in the /var/lib/docker/volumes directory
	so it will be at /var/lib/docker/volumes/myvol.
	NOTE:   using the docker volume inspect myvol command will give you the mountpoint which will be
/var/lib/docker/volumes/myvol/_data ...this is where the volume is surfaced on the host.
	you can see and write to the volume from the host as well

you can also deploy a volume in a Dockerfile with the  VOLUME <container-mount-point>  format.  but you cannot specify the host directory it will go to. you can specify that at deploy time.

#### CREATE A CONTAINER AND A VOLUME FOR IT

docker container run -dit --name voltainer --mount source=bizvol,target=/vol alpine
creates a container named voltainer and mounts a volume called bizvol to /vol in the container. if bizvol does not exist it will be created
you can also create a service and have the volume created on every host that gets a replica created on it
docker service create --name hellcat2 --replicas 5 --mount source=bizvol2,target=/vol alpine sleep 1d
every replica will get the volume created for it

### DOCKER STACKS

Stacks let you define complex multi-service apps
in a single declarative file. They also provide a simple way deploy the app and manage
its entire lifecycle — initial deployment > health checks > scaling > updates > rollbacks
and more
	* first you build your app in a compose file then you deploy it with stacks
	* use the docker stack deploy to deploy the app from the compose file

In a nutshell, Docker is great for development and testing. Docker Stacks are great
for scale and production

a Stack is typically services grouped and managed as a stack

#### DEPLOY THE ATSEA SHOP DEMO APP

BUILD FROM docker-stack.yml STACK FILE

1) clone the git repo
git clone https://github.com/dockersamples/atsea-sample-shop-app.git

2) open the stack file ( docker-stack.yml ) to establish pre-requisites

	version: "3.2"

	services:
	  reverse_proxy:
	    image: dockersamples/atseasampleshopapp_reverse_proxy
	    ports:
	      - "80:80"
	      - "443:443"
	    secrets:
	      - source: revprox_cert
	        target: revprox_cert
	      - source: revprox_key
	        target: revprox_key
	    networks:
	      - front-tier

	  database:
	    image: dockersamples/atsea_db
	    environment:
	      POSTGRES_USER: gordonuser
	      POSTGRES_DB_PASSWORD_FILE: /run/secrets/postgres_password
	      POSTGRES_DB: atsea
	    networks:
	      - back-tier
	    secrets:
	      - postgres_password
	    deploy:
	      placement:
	        constraints:
	          - 'node.role == worker'

	  appserver:
	    image: dockersamples/atsea_app
	    networks:
	      - front-tier
	      - back-tier
	      - payment
	    deploy:
	      replicas: 2
	      update_config:
	        parallelism: 2
	        failure_action: rollback
	      placement:
	        constraints:
	          - 'node.role == worker'
	      restart_policy:
	        condition: on-failure
	        delay: 5s
	        max_attempts: 3
	        window: 120s
	    secrets:
	      - postgres_password

	  visualizer:
	    image: dockersamples/visualizer:stable
	    ports:
	      - "8001:8080"
	    stop_grace_period: 1m30s
	    volumes:
	      - "/var/run/docker.sock:/var/run/docker.sock"
	    deploy:
	      update_config:
	        failure_action: rollback
	      placement:
	        constraints:
	          - 'node.role == manager'

	  payment_gateway:
	    image: dockersamples/atseasampleshopapp_payment_gateway
	    secrets:
	      - source: staging_token
	        target: payment_token
	    networks:
	      - payment
	    deploy:
	      update_config:
	        failure_action: rollback
	      placement:
	        constraints:
	          - 'node.role == worker'
	          - 'node.labels.pcidss == yes'

	networks:
	  front-tier:
	  back-tier:
	  payment:
	    driver: overlay
	    driver_opts:
	      encrypted: 'yes'

	secrets:
	  postgres_password:
	    external: true
	  staging_token:
	    external: true
	  revprox_key:
	    external: true
	  revprox_cert:
	    external: true

The app consists of 5 services, 3 networks, 4 secrets, and 3 port mappings
	* a worker node needs to be labeled pcidss=yes
	* 4 secrets need to be created

3) one of the services (payment_gateway) has some placement constraints requiring it to run only on nodes that have a label assigned -  pcidss=yes.
....
placement:
        constraints:
          - 'node.role == worker'
          - 'node.labels.pcidss == yes'
.......
4) Add the node label to node1
	docker node update --label-add pcidss=yes node1.nikkyron.com
5) verify the label
	docker node inspect node1.nikkyron.com
6) Create the 4 secrets defined in the stack file
	a. create a key pair ( 3 of the secrets will have cryupto keys )
		openssl req -newkey rsa:4096 -nodes -sha256 \
		-keyout domain.key -x509 -days 365 -out domain.crt
	* there will be a key pair in the current directory now
		   b. Create the revprox_cert , revprox_key , and postgress_password secrets
		docker secret create revprox_cert domain.crt
		docker secret create revprox_key domain.key
		docker secret create postgres_password domain.key
	c. Create the staging_token secret
		 echo staging | docker secret create staging_token -
7) list the secrets
	docker secret ls

* Pre-requisites are satisfied

#### DEPLOY THE APP

To deploy use the docker stack deploy command. In basic form it takes 2 arguments:
	* name of stack file   - docker-stack.yml
	* name of the stack   - will call it seastack

Ensure you are in the cloned repo directory ( atsea-sample-shop-app )

	docker stack deploy -c docker-stack.yml seastack


#### MANAGE THE APP

A stack is set of related services and infrastructure that gets deployed and managed as a unit
A stack built from normal docker resources - networks, volumes, services etc which can be managed with their usual commands
	* docker network
	* docker volume
	* docker secret
	* docker service
and so on....

Although it is possible to use docker service to scale the replicas, this is not the best way.
A better way is the declarative way, using the stack file.
	Example:   if you scale the app service from 2 replicas to 4 on the command line ( imperative method ) but then later on change the stack via the stack file, it will still declare 2 replicas. So when you apply the change declaratively using the stack file , it will roll the service back to 2.
Make all changes to the stack via the stack file, and keep the stack file in version control.

#### DECLARATIVELY MAKE CHANGES TO STACK

* Increase the number of appserver replicas from 2 to 10
* Increase the stop grace period for the visualizer service to 2 minutes

	1)  Edit the docker-stack.yml file and update the following two values:
		.services.appserver.deploy.replicas=10
		.services.visualizer.stop_grace_period=2m
	2) Save file and redeploy the app
		docker stack deploy -c docker-stack.yml seastack
  ( same command used to deploy originally )
Now if you run docker stack ps you will see the new replicas for the appserver service
NOTE:  you will also see the visualizer replica was shudown and a new one started with the new grace period value we added earlier

#### DELETE THE STACK

docker stack rm seastack
   * no confirmation will be asked before deleting the stack
the networks and services will be deleted but not the secrets as they were created before the stack
   * if volumes are defined at top level they will not be deleted either. ( in this exercise they were not )

#### COMMAND SUMMARY

	docker stack ls - list all stacks on the swarm, including the number of services they have
	docker stack ps <stackname> - detailed info about a deployed stack, including node each replica is on, desired and current states
	docker stack rm <stackname> - remove the stack (without confirmation)


### DOCKER SECURITY

A combination of linux security mechanisms and docker security mechanisms

LINUX SECURITY MECHANISMS

1) Kernel namespaces  - a docker container is an organized collection of namespaces. every container has its own pid, net, mnt, ipc, uts and user namespace.
	* process id (pid) - provides isolated process tree for every container
	* network (net) - provide each container its own network stack. interfaces, IP addresses, port ranges, and routing tables
	* filesystem/mount (mnt)  - Every container gets its own unique isolated root / filesystem. This means that every container can have its own /etc , /var , /dev etc
	* inter-process communications (ipc)  - Docker uses the ipc namespace for shared memory access within a container
	* user (user)   -  Docker lets you use user namespaces to map users inside of a container to different users on the Linux host
	* UTS (uts)  -  Docker uses the uts namespace to provide each container with its own hostname
2) Control Groups  - are about setting limits, like CPU, RAM and disk I/O
3) Capabilities  -  Docker works with capabilities so that you can run containers as root, but strip out the root capabilities that you don’t need
			 if the only root privilege your container needs is the ability to bind to low numbered network ports, you should
			start a container and drop all root capabilities, then add back the CAP_NET_BIND_SERVICE capability
4) Mandatory Access Control Systems  - Docker works with major Linux MAC technologies such as AppArmor and SELinux
5) seccomp  - Docker uses seccomp, in filter mode, to limit the syscalls a container can make to the host’s kernel

DOCKER SECURITY MECHANISMS

1)  Swarm mode - lets you cluster multiple Docker hosts and deploy your applications in a declarative way

	* Cryptographic node IDs
	* Mutual authentication via TLS
		* Every manager and worker that joins a Swarm is issued a client certificate. This certificate is used for mutual authentication
		* to view a cert on a node use  sudo openssl x509 -in /var/lib/docker/swarm/certificates/swarm-node.crt -text
			* the Subject line will show the O, OU and CN fields which are the swarm id, role of the node, and the node ID
				 Subject: O=l1ijoxzq7n1miuizzlgtvwzi1, OU=swarm-manager, CN=wx90izhjajg1ezcp49ts7ulrm
	* Secure join tokens
		* format is always PREFIX - VERSION - SWARM ID - TOKEN.
		* PREFIX is always SWMTKN. VERSION field is swarm version. SWARM ID is a hash of the Swarm cert. TOKEN is always unique

	* CA configuration with automatic certificate rotation
	* Encrypted cluster store (config DB)
		* The cluster store is the brains of a Swarm and is the place where cluster config and state are stored
		* based on an implementation of etcd , and is automatically configured to replicate itself to all managers in the Swarm
	* Encrypted networks
2)  Security Scanning - Docker Security Scanning performs binary-level scans of Docker images and checks the software in them against databases of known vulnerabilities (CVE databases).
After the scan is performed, a detailed report is made available.

3)  Docker Content Trust - DCT allows developers to sign their images when they are pushed to Docker Hub or Docker Trusted Registry

4)  Docker Secrets  - Things like passwords, TLS certs, ssh keys for apps running in containers are stored as Docker Secrets. This is better than in env variables

THE PROCESS FOR SECRETS

	1. The blue secret is created and posted to the Swarm

2. It gets stored in the encrypted cluster store (all managers have access to the
	cluster store)

	3. The blue service is created and the secret is attached to it

	4. The secret is encrypted in-flight while it is delivered to the tasks (containers)
	in the blue service

	5. The secret is mounted into the containers of the blue service as an unencrypted
	file at /run/secrets/ . This is an in-memory tmpfs filesystem (this step is
	different on Windows Docker hosts as they do not have the notion of an in-
	memory filesystem like tmpfs)

	6. Once the container (service task) completes, the in-memory filesystem is torn
	down and the secret flushed from the node.

	7. The red containers in the red service cannot access the secret.
