---
title: 'Jenkins Pipelines'
date: '15:55 27-05-2019'
visible: true
---

# CICD.Jenkins.Pipelines
Created Monday 11 March 2019

<https://github.com/wardviaene/jenkins-course>

# Section.1.intro
Created Monday 11 March 2019


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.1.intro/pasted_image.png)



![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.1.intro/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.1.intro/pasted_image002.png)









# Section.2.Intro.to.jenkins
Created Monday 11 March 2019

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.2.Intro.to.jenkins/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.2.Intro.to.jenkins/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.2.Intro.to.jenkins/pasted_image002.png)



Ultimately, this is the process for continuous integration and delivery as it pertains to the SDLC:

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.2.Intro.to.jenkins/pasted_image003.png)

The above process needs to go as quickly as possible.

This course will focus on using Jenkins to manage the Build, Test, and Release cycles, and to use Docker for the package process.

it is possible to complete this process and deploy to production several times per day.
if this process takes days or weeks, then improving the CI/CD process is what is needed.



![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.2.Intro.to.jenkins/pasted_image005.png)

The AWS tools are often limited, but are better intefrated with other AWS services



Jenkins Installation
--------------------

**mkdir -p /var/jenkins_home**
**chown -R 1000:1000 /var/jenkins_home/**
**docker run -p 8080:8080 -p 50000:50000 -v /var/jenkins_home:/var/jenkins_home -d --name jenkins jenkins/jenkins**

Format of the run command:      docker run -p [*port mappings*] -v [*volume mappings*] -d [*run detached*] --name [*container name*] [*image to run*]

Note that the above run command creates the volume **/var/jenkins_home **which will be the jenkins home folder 


**docker ps**
*CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                                              NAMES*
*d27c93395119        jenkins/jenkins     "/sbin/tini -- /usr/…"   About an hour ago   Up About an hour    0.0.0.0:8080->8080/tcp, 0.0.0.0:50000->50000/tcp   jenkins*

Get the Jenkins admin password
**docker exec -it jenkins cat /var/jenkins_home/secrets/initialAdminPassword**
*4edfac50722549ffa3cf802955559818*


#### Intro to docker





# Section.3.Building.nodejs.app
Created Monday 11 March 2019

#### Why a node.js app

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.3.Building.nodejs.app/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.3.Building.nodejs.app/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.3.Building.nodejs.app/pasted_image002.png)


#### Build and deploy a node.js app

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.3.Building.nodejs.app/pasted_image003.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.3.Building.nodejs.app/pasted_image004.png)
 A docker repository can also be run on-prem**


Demo
----

#### build the nodejs app without docker


* Install nodejs plugin in jenkins
* create new freestyle project
* add the git repo under source code mgmt using https to avoid ssh keys - <https://github.com/wardviaene/docker-demo.git>
* add build step - execute shell cmd - **npm install**
* add nodejs under global tools - install from nodejs.org latest version
* go to project config page under build env select the nodejs added earlier
* run the build and it completes
* go to the workspace in the jenkins home folder ( /var/jenkins_home ) - **/var/jenkins_home/workspace/nodejs example app  **and the new build is there under *node_modules*

Also, the git repo that was configured for the project has been cloned into the workspace


#### Build the nodejs app with docker

This will be steps 3 and 4 from the above slide.
There are several docker plugins available. For this course use **CloudBees Docker Build and Publish**
Next, ensure that jenkins can access the docker socket. For this, there is a docker build on github we will clone it and use that image instead

**git clone https://github.com/wardviaene/jenkins-docker.git**

**docker build -t jenkins-docker .**
*docker build -t jenkins-docker .*
*Sending build context to Docker daemon  74.75kB*
*Step 1/4 : FROM jenkins/jenkins*
* ---> a5e18ff4fa3b*
*Step 2/4 : USER root*
* ---> Running in 14bc52c10682*
*Removing intermediate container 14bc52c10682*
* ---> 56358e1462c9*
*Step 3/4 : RUN mkdir -p /tmp/download &&  curl -L https:*download.docker.com/linux/static/stable/x86_64/docker-18.03.1-ce.tgz | tar -xz -C /tmp/download &&  rm -rf /tmp/download/docker/dockerd &&  mv /tmp/download/docker/docker* /usr/local/bin/ &&  rm -rf /tmp/download &&  //**groupadd -g 999 docker &&  usermod -aG staff,docker jenkins**
* ---> Running in 690e222e8706*
*  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current*
*                                 Dload  Upload   Total   Spent    Left  Speed*
*100 36.9M  100 36.9M    0     0  26.7M      0  0:00:01  0:00:01 --:--:-- 26.7M*
*Removing intermediate container 690e222e8706*
* ---> 6545dfbcc1a2*
*Step 4/4 : user jenkins*
* ---> Running in c4bc39cb05a8*
*Removing intermediate container c4bc39cb05a8*
* ---> 9168f860dff9*
*Successfully built 9168f860dff9*
*Successfully tagged jenkins-docker:latest*
**NOTE:  **The RUN portion of building the container included adding the docker group and adding the jenkins user to that docker group. This will allow the container to use the linux docker socket.

Stop the current jenkins container, then remove it
**docker stop jenkins**
**docker rm jenkins**

The volume containing jenkins_home is still available at **/var/jenkins_home **so the new container will have access to it.

Run the new image with an additional volume added - **/var/run/docker.sock** to allow the new jenkins container to access the docker runtime thru the linux socket
**docker run -p 8080:8080 -p 50000:50000 -v /var/jenkins_home:/var/jenkins_home -v /var/run/docker.sock:/var/run/docker.sock -d --name jenkins jenkins-docker**

**docker ps**
*CONTAINER ID        IMAGE                     COMMAND                         CREATED                   STATUS                                                       PORTS                                                                           NAMES*
*e7942efcfc2a        jenkins-docker      "/sbin/tini -- /usr/…"         17 seconds ago        Up 16 seconds       0.0.0.0:8080->8080/tcp, 0.0.0.0:50000->50000/tcp                                jenkins*

Test that the container can use docker
**docker exec -it jenkins bash**
jenkins@e7942efcfc2a:/$ **docker ps**
*CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                                              NAMES*
*e7942efcfc2a        jenkins-docker      "/sbin/tini -- /usr/…"   10 minutes ago      Up 10 minutes       0.0.0.0:8080->8080/tcp, 0.0.0.0:50000->50000/tcp   jenkins*

Now we can configure the nodejs example app with a new build step to use **Docker Build and Publish. **The *Repository Name *will be a docker hub repo. I am adding *docker-nodejs-demo * to my docker hub account.
Next I added my registry credentials for the docker hub repo I just configured.
Now run the  build again. The build finishes and now there is a **latest** tag on my new docker repo.

The newest build also ran the first build step **npm install **which is now not needed since the docker build step also runs npm install. Remove the first build step from the project.

#### Run the image created above by the docker build

**docker run -p 3000:3000 -d --name my-nodejs-app roninhockley/docker-nodejs-demo**
**NOTE:** by not specifying a tag above it will default to latest

Once the container is built I can **curl localhost:3000 **and get the hello-world response coming from the new container

**INTERESTING NOTE:  **After starting the container and trying to curl localhost, it failed. I then realized i was still shelled into the jenkins container and had created the new container from within it. This worked because the jenkins container was given access to the linux docker socket!

#### Summary


1. The first build process without docker just built the app and placed the modules etc in the workspace for the project
2. The second build process with docker:
	a. recreate the jenkins container with the docker client in it, and granted access to the socket.
	b. in the new jenkins container, install the **Docker Build and Publish **plugin from CloudBees
	c. Add a new build step using the Docker Build and Publish plugin, and configure the new build step to publish the builds to a docker hub repo.
	d. run a new container using the image published by jenkins and test the nodejs app with a **curl localhost:3000**



In the next lectures we will use code to run the builds instead of the jenkins GUI


# Section.4.Infrastructure.as.code
Created Tuesday 12 March 2019

Most people use jenkins via the GUI for setup and managing projects. 
Using devops principles there is a better way.

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.4.Infrastructure.as.code/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.4.Infrastructure.as.code/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.4.Infrastructure.as.code/pasted_image002.png)


# Section.5.Jenkins.job.DSL
Created Tuesday 12 March 2019

The Jenkins Job DSL API reference website - <https://jenkinsci.github.io/job-dsl-plugin/>


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.5.Jenkins.job.DSL/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.5.Jenkins.job.DSL/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.5.Jenkins.job.DSL/pasted_image002.png)

The first part is the job definition. The type is job and it is named *NodeJS example. *
The job will use the 4 parameters defined:

* **scm** - specify what repo to clone, followed by the git user and email as usual when using git.
* **triggers** - specify a trigger for scm and set the schedule to poll every 5 mins
* **wrappers** - use a nodejs build environment named *nodejs. *The nodejs environment provides the commands **node** and **npm. **
* **steps** - use build step of type *shell* and run the command **npm install** 


All of the parameters are reflected on the project config page of the GUI. Using DSL allows us to set them without using the GUI.

#### Demo - jenkins job DSL with nodejs app

Steps: 

1. install the *Job DSL* plugin
2. create new item called *seed project. *This project will create new projects and jobs based on job DSLs in source control.
	a. on the config page set up source code mgmt to use git repo *https:*github.com/wardviaene/jenkins-course//
	b. add build step *Process Job DSLs * and point it to *job-dsl/nodejs.groovy *under *Look on Filesystem. *It is there because of step a above


On first run, the job has failed - *ERROR: script not yet approved for use. *
Jenkins requires the scripts be approved as a safety mechanism.

Manage Jenkins> in-process Script Approval - click *Approve, * then the DSL runs with Success.
	
**Summary:**

The seed project now does the following:


* polls the configured git repo every 5 mins
* if new changes it checks out the new Rev
* it uses the nodejs build environment to run **npm install **and do the build. 
* after build is complete, in the workspace folder for the *NodeJS example *project there will be a *node_modules *folder there 


In the workspace folder, try to run **npm start **which fails because the *npm *command is not in the path
**find ~ -name 'npm'**
/var/jenkins_home/tools/jenkins.plugins.nodejs.tools.NodeJSInstallation/nodejs/lib/node_modules/npm
/var/jenkins_home/tools/jenkins.plugins.nodejs.tools.NodeJSInstallation/nodejs/lib/node_modules/npm/bin/npm
**/var/jenkins_home/tools/jenkins.plugins.nodejs.tools.NodeJSInstallation/nodejs/bin/npm**
	
jenkins@e7942efcfc2a:~/workspace/NodeJS example$ **export PATH=$PATH:/var/jenkins_home/tools/jenkins.plugins.nodejs.tools.NodeJSInstallation/nodejs/bin**

**Next steps:**   Build the NodeJS example project and publish it as a docker image using the docker plugin as we did earlier.

#### Demo - jenkins job DSL with docker build and publish
**NOTE:  **I forked all the course repos to my repo on github and edited the DSL scripts accordingly

This is going to involve an adaptation of the DSL file used before. The job is given a new name and the last parameter *steps *is different as it will use the docker plugin rather than npm install

job('NodeJS Docker example') {
scm {
git('git:github.com/roninhockley/docker-demo.git') {  node ->  is hudson.plugins.git.GitSCM
node / gitConfigName('roninhockley')
node / gitConfigEmail('[nikkyrronl@gmail.com](mailto:nikkyrronl@gmail.com)')
}
}
triggers {
scm('H/5 * * * *')
}
wrappers {
nodejs('nodejs')  this is the name of the NodeJS installation in //
  Manage Jenkins -> Configure Tools -> NodeJS Installations -> Name//
}
steps {
dockerBuildAndPublish {
repositoryName('roninhockley/docker-nodejs-demo')
tag('${GIT_REVISION,length=9}')
registryCredentials('dockerhub')
forcePull(false)
forceTag(false)
createFingerprints(false)
skipDecorate()
}
}
}
	
The use of dockerBuildAndPublish in DSL is referenced on the DSL API website here - <https://jenkinsci.github.io/job-dsl-plugin/#method/javaposse.jobdsl.dsl.helpers.step.StepContext.dockerBuildAndPublish>

The steps parameter uses *dockerBuildandPublish *with the following values:


* **repositoryName('roninhockley/docker-nodejs-demo') ** -  where the docker image will be published
* **tag('${GIT_REVISION,length=9}')  **- how to tag the image. Rather than the default *latest*, this will tag the image with the git revision number that triggered the build
* **registryCredentials('dockerhub') **- use credentials called *dockerhub*
* **forcePull(false)*** - *do not require the latest tag of the image to be pulled from dockerhub before building
* **forceTag(false)***  -  *do not force the tag to be replaced if it exists
* **createFingerprints(false)***  - *do not force creation of fingerprints for the image
* **skipDecorate()***  - *if this were true it would not decorate the build name

Steps:

* configure the seed project by adding the new script under DSL Scripts
* tidy up the credentials for dockerhub by changing the ID to *dockerhub *as specified in the DSL script
* deleted all projects except *seed project*
* ran *seed *which created both the npm project and the docker project
* ran both and confirmed they both succeeded.


From the build console output for the docker build I got the **docker build **command that jenkins used:

*[NodeJS Docker example] $ ***docker build -t roninhockley/docker-nodejs-demo:e583f9bd4 "/var/jenkins_home/workspace/NodeJS Docker example"**

* build and tag with the rev number ( **e583f9bd4 ) **
* for the build directory use **/var/jenkins_home/workspace/NodeJS Docker example**




# Section.6.Jenkins.Pipelines
Created Tuesday 12 March 2019


**Pipelines vs DSL:  **In jenkins, the DSL is used to create new jobs, whereas pipelines are a job type.


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image002.png)

On the last point of the above slide:   this means that jenkins could detect the new source repo and automatically create a new project


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image003.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image005.png)

The variable *mvnHome* is a path used in the Build step to find the home directory for maven
The Build step will first clean then it builds a package, which in java is a **.jar file**
The Results stage will put the XML build results in a graph using junit


#### Demo: Jenkins pipelines with nodeJS and docker

Create a new project of type **Pipeline** and build the project using a Jenkinsfile.

**About the Jenkinsfile**

The Jenkinsfile for nodeJS docker project:

node {
   def commit_id
   stage('Preparation') {
 checkout scm
 sh "git rev-parse --short HEAD > .git/commit-id"                        
 commit_id = readFile('.git/commit-id').trim()
   }
   stage('test') {
 nodejs(nodeJSInstallationName: 'nodejs') {
   sh 'npm install --only=dev'
   sh 'npm test'
 }
   }
   stage('docker build/push') {
 docker.withRegistry('<https://index.docker.io/v1/>', 'dockerhub') {
   def app = docker.build("wardviaene/docker-nodejs-demo:${commit_id}", '.').push()
 }
   }
}
	
**Details:**


1. This project can be built on any node.
2. there is a variable *commit_id* define. This variable will house the commit id, exposed in the Preparation stage with  *readFile('.git/commit-id').trim()*


There are 3 stages:
	

* Preparation
	* *checkout scm - *check out the source code from git
	* *sh "git rev-parse --short HEAD > .git/commit-id" - * use git rev-parse command to get the commit id from HEAD and redirect output to a file called commit-id
	* commit_id = readFile('.git/commit-id').trim() - put the value of the file commit-id in the *commit_id* variable

		

* test
	* within the nodejs build environment run:
		* **nodejs(nodeJSInstallationName: 'nodejs') {**  -  this line defines means run nodejs from the tool in jenkins called *"nodejs"*
		* **sh 'npm install --only=dev'** - install only the dev packages defined in packages.json as *"devDependencies"*
		* **sh 'npm test'** - with npm, run the script specified in "*test": "mocha" *also defined in *packages.json . *(the purpose of mocha is to test your code)

The entire test block translates to  execute the nodejs function with parameters **nodeJSInstallationName: 'nodejs ** and in nodejs run 2 commands in a shell.
If the nodejs installation is not defined then nodejs will fail with "npm not found"
				

* docker build/push
	* **docker.withRegistry('https://index.docker.io/v1/', 'dockerhub') **set the registry to use (it points to dockerhub) and use credentials 'dockerhub'
	* **def app = docker.build("wardviaene/docker-nodejs-demo:${commit_id}", '.').push()  **- run docker build, tag it with *repo:commit_id, *build in the current directory, then push it to the registry set with **docker.withRegistry**


**Set it up in jenkins**

Ideally, use DSL to create this pipeline job, but for the course we are using the GUI


1. create new item called *nodejs docker pipeline * of type *pipeline* 
2. under **Pipeline** set definition to **Pipeline script from SCM, **set the **Repository URL**  (  *https:*github.com/roninhockley/docker-demo )  //and set the **Script Path **which is the location of the Jenkinsfile relative to the repo root folder
3. run the build.  on the project page you will see the **Stage View **and it will illustrate the 3 build stages set up in the Jenkinsfile with elapsed times and logs relative to each stage being executed. 


The stage view is good to see how to optimize the build, by identifying what takes the longest.

The great thing about pipelines is you can use stage view to see how to better optimize your build times , and you can bundle the Jenkinsfile with your source code in the repo, and version it.
Also it allows the developers to take control of the build process within their source repo and treat everything as code.


**Build, test, and run everything in docker containers**

A further discussion in more detail of using docker in the pipeline process.


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image006.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.6.Jenkins.Pipelines/pasted_image007.png)

#### Demo: jenkins pipeline tests with docker

At present the jenkins master is running in a container. This means that all the build steps are being executed in that container.
It would be better to have the build steps running in their own containers with all dependencies and binaries specified by us.
This will be an alternative to using the nodejs environment on the jenkins master. Instead use a dedicated container with the nodejs tools in it.

The Jenkinsfile for this is **misc/Jenkinsfile.v2:**

node {
   def commit_id
   stage('Preparation') {
 checkout scm
 sh "git rev-parse --short HEAD > .git/commit-id"                       <------ the Preparation stage is no different. the master is in charge of the source code.
 commit_id = readFile('.git/commit-id').trim()
   }
   stage('test') {
 def myTestContainer = docker.image('node:4.6')
 myTestContainer.pull()
 myTestContainer.inside {                                                          <-------- this Test stage runs a container from image  node:4.6 which has the nodejs tools in it.  No need to have the nodejs
   sh 'npm install --only=dev'										tools installed on the jenkins master, possibly multiple versions of it for different builds. images with different tags can have different
   sh 'npm test'													versions of nodejs 
 }
   }
   stage('test with a DB') {
 def mysql = docker.image('mysql').run("-e MYSQL_ALLOW_EMPTY_PASSWORD=yes") 
 def myTestContainer = docker.image('node:4.6')
 myTestContainer.pull()
 myTestContainer.inside("--link ${mysql.id}:mysql") { // using linking, mysql will be available at host: mysql, port: 3306        <----- run a mysql container and link it to the nodejs container.
  sh 'npm install --only=dev' 
  sh 'npm test'                     
 }                                   
 mysql.stop()          <---- jenkins needs to know to explicitly stop the mysql container, because mysql runs as a daemon in the container.
   }                                     
   stage('docker build/push') {            
 docker.withRegistry('<https://index.docker.io/v1/>', 'dockerhub') {
   def app = docker.build("wardviaene/docker-nodejs-demo:${commit_id}", '.').push()           <---- no different. the master will build and push the container artifact
 }                                     
   }                                       
}              


**Set it up in jenkins**

Create pipeline named **tests in docker, **configure the Pipeline Definition as script from scm and point to the appropriate Jenkinsfile  ( misc/Jenkinsfile.v2. )  for this project.

Run the build and as seen in console output it produced a new image tagged as *c57981e.*

# Section.7.Jenkins.Integrations
Created Wednesday 13 March 2019


Email integration
-----------------

**GOAL:  **Notify developers via email of broken builds as soon as possible

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image.png)

**NOTE:  **The push version is better.

#### Demo: Email notifications

The demo uses the **Email Extension **plugin
	
*This plugin is a replacement for Jenkins's email publisher. It allows to configure every aspect of email notifications: when an email is sent, who should receive it and what the email says*
	
On **Manage Jenkins>Configure System  **specify the smtp server and credentials.
	
The demo will use a Jenkinsfile - email-notifications/Jenkinsfile

node {
	
  // config 
  def to = emailextrecipients([
  [$class: 'CulpritsRecipientProvider'],
  [$class: 'DevelopersRecipientProvider'],
  [$class: 'RequesterRecipientProvider']
  ])
	
  // job
  try {                                                              <------this **block** is named *try*
stage('build') {
  println('so far so good...')              <--- build stage just prints a message
}
stage('test') {
  println('A test has failed!')                  <-------- test stage prints failure message and exits code 1 which jenkins will use to stop the build and flag it as failed
  sh 'exit 1'
}
  } catch(e) {  							<----- a new **block** named *catch*
// mark build as failed
currentBuild.result = "FAILURE";
// set variables
def subject = "${env.JOB_NAME} - Build #${env.BUILD_NUMBER} ${currentBuild.result}"           <--- define the subject for the email consisting of the job name, build # and the result
def content = '${JELLY_SCRIPT,template="html"}'            										<---- define the content for the email
	
// send email
if(to != null && !to.isEmpty()) {													<---- if the *to *variable ( defined at the beginning of this Jenkinsfile is not empty send the email
  emailext(body: content, mimeType: 'text/html',
 replyTo: '$DEFAULT_REPLYTO', subject: subject, 			<---- the default reply to is set on the jenkins Config System page
 to: to, attachLog: true )
}
	
// mark current build as a failure and throw the error
throw e;
  }
}
	
**Set up in Jenkins**

Create new pipeline job and set up the scm and jenkinsfile. Also configure Build Triggers to poll scm every 5 mins.

Slack Integration
-----------------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image002.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image003.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image004.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image005.png)


#### Demo: Slack Integration

Install the plugin called **Slack Notification Plugin. **This will add a section to the Configure System section at the bottom

To setup slack notifications:

In slack, add new app, search *webhook *and install the **Incoming Webhooks **app. Configure on the Configure System section. 
Setup on jenkins mostly involves the Webhook URL
**Refer to the video to do this again.** 

Create a new pipeline job and use the **slack-notifications/Jenkinsfile:**

node {
	
  // job
  try {
stage('build') {
  println('so far so good...')
}
stage('test') {
  println('A test has failed!')
  sh 'exit 1'
}
  } catch(e) {
// mark build as failed
currentBuild.result = "FAILURE";
	
// send slack notification
slackSend (color: '#FF0000', message: "FAILED: Job '${env.JOB_NAME} [${env.BUILD_NUMBER}]' (${env.BUILD_URL})")   <---- the slackSend function with the same arguments for the message body
	
// throw the error
throw e;
	
	
  }
}
	
I ran the job and this landed in my slack channel:

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.7.Jenkins.Integrations/pasted_image006.png)

The first line came from testing the connection on the Jenkins config for the plugin.
The second line is the failure notification. The link goes to the page for build 1


**NOTE:	**Will do the rest of the integration demos later, want to get on with the course.

# Section.8.Advanced.Jenkins.Usage
Created Wednesday 13 March 2019

Jenkins slaves
--------------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image001.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image003.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image004.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image005.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image006.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image007.png)


#### Demo: Jenkins slave using SSH

Created vm called builder from ubuntu16.04 template.

The builder node is a docker host that runs a container to do the builds from jenkins.
I built an image using this Dockerfile:

FROM openjdk:8-jdk
	
# install git, curl, openssh server, and remove host keys
RUN apt-get update && apt-get install -y git curl openssh-server && mkdir /var/run/sshd && rm -rf /var/lib/apt/lists/* && rm -rf /etc/ssh/ssh_host_*
	
# prepare home, user for jenkins
ENV JENKINS_HOME /var/jenkins_home
	
ARG user=jenkins
ARG group=jenkins
ARG uid=1000
ARG gid=1000
	
RUN groupadd -g ${gid} ${group} \
&& useradd -d "$JENKINS_HOME" -u ${uid} -g ${gid} -m -s /bin/bash ${user}
	
VOLUME /var/jenkins_home
	
# get docker client
RUN mkdir -p /tmp/download && \
 curl -L <https://get.docker.com/builds/Linux/x86_64/docker-1.13.1.tgz> | tar -xz -C /tmp/download && \
 rm -rf /tmp/download/docker/dockerd && \
 mv /tmp/download/docker/docker* /usr/local/bin/ && \
 rm -rf /tmp/download && \
 groupadd -g 999 docker && \
 usermod -aG docker jenkins
	
# expose ssh port
EXPOSE 22
	
# make sure host keys are regenerated before sshd starts
COPY entrypoint.sh /entrypoint.sh
	
ENTRYPOINT ["/entrypoint.sh"]
	
I then pushed it to a newly created repo   -  **roninhockley/jenkins-slave**

Installed docker on the host, created a directory to be mounted to the container for jenkins_home, and created a keypair for jenkins to connect. 
These commands came from a file called *digitalocean_userdata.sh. * It was created by instructor to use on a droplet hence the name

**apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D**
**apt-add-repository 'deb https://apt.dockerproject.org/repo ubuntu-xenial main'**
**apt-get update**
**apt-get install -y docker-engine**
**systemctl enable docker**
**mkdir -p /var/jenkins_home/.ssh**
**cp /root/.ssh/authorized_keys /var/jenkins_home/.ssh/authorized_keys**
**chmod 700 /var/jenkins_home/.ssh**
**chmod 600 /var/jenkins_home/.ssh/authorized_keys**
**chown -R 1000:1000 /var/jenkins_home**
**docker run -p 2222:22 -v /var/jenkins_home:/var/jenkins_home -v /var/run/docker.sock:/var/run/docker.sock --restart always -d roninhockley/jenkins-slave**

Once the builder is configured, add it to jenkins as a new node. 

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image008.png)
**NOTE:  **Under **Advanced **the port is set to 2222, since the container is running with  **-p  2222:22**
The credentials were created using the keypair named mykey that i created on the builder. I place the pub key into the authorized_keys file for user jenkins, at  /*var/jenkins_home/.ssh/authorized_keys. *
This folder is mounted as a volume to the container thus jenkins will use the keypair to connect to the container for building.

First attempt to launch the new builder failed. From the jenkins log:

03/14/19 16:41:54] [SSH] Opening SSH connection to 192.168.1.238:2222.
/var/jenkins_home/.ssh/known_hosts [SSH] No Known Hosts file was found at /var/jenkins_home/.ssh/known_hosts. Please ensure one is created at this path and that Jenkins can read it.

The new builder node is not known to jenkins. 

**SOLUTION:**

Need to add the builder key to known_hosts on the master. 
**NOTE:  **Remember, the master is a docker container, so we are effectively writing to known_hosts on the volume mount, using persistent storage, as the container will always launch attached to this volume.


1. Get a shell in the jenkins master container:


**docker exec -it jenkins /bin/bash**
jenkins@e7942efcfc2a:/$


2. Run a command to retrieve keys from the builder:


jenkins@e7942efcfc2a:/$ **ssh-keyscan -p 2222 192.168.1.238**
# 192.168.1.238:2222 SSH-2.0-OpenSSH_7.4p1 Debian-10+deb9u6
[192.168.1.238]:2222 ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBNbaXIK5ZisokClprt+WCiFKmBNkTo+2oxSX9BYzFHIaZ4vJfJDa+S6REwtD8Yflazn5hqoY6HR5U+mUO/LWHVg=
# 192.168.1.238:2222 SSH-2.0-OpenSSH_7.4p1 Debian-10+deb9u6
[192.168.1.238]:2222 ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC1h13uKSmt7uxf32qLAU498e827nD8BgmU6BjP36MCQ+sdyLqU9qntYh0oclSrKLJuKh0et/tQ6aaoBX59mEzK01F05PzY+o8gE6XGPFJn4eC/7xGJY94H75tNODf/9MpuLKxd0guEFscRlOvwfbh+EcFrxDhWRuukOPGpVFjySLihNhhMEZtJNKUcfrOQEyfUoqcY3A0GHVke2JeAvqnqBljDJrC4V0tZTnc7xsVBZlYHLlwAj22IotG3C4Lyd4ZNlmwuCmCSVkaK0SStLgylbaEzqwCl81ptX+m6xR/Td4Iz/VBi/rZOnW6S9ZDjTBeBWdnqZqEiaaow9dBCew3Z
# 192.168.1.238:2222 SSH-2.0-OpenSSH_7.4p1 Debian-10+deb9u6
[192.168.1.238]:2222 ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIE8TeD3MWibafC/SCgcDaqb8kgtJIPvdJ1Ws8MKIOzL5


3. Run this command again and pipe the output to the known_hosts file on the master


jenkins@e7942efcfc2a:/$ **ssh-keyscan -p 2222 192.168.1.238 >> /var/jenkins_home/.ssh/known_hosts**

This solved the known_hosts issue, however it still would not bring the slave online. 
The keypair auth was failing. Turns out when pasting the private key into the credentials page on jenkins you must include the "prologue" portion of the key:

**-----BEGIN RSA PRIVATE KEY-----**
MIIEpgIBAAKCAQEAl7JxtjAp0Fzcn1AxdVEY7OsaT8qKPBNoAXsEGZH+uHkzENth
V169SwWLcFkqkrNt03QA0ko3pnSJSOsHURKZcIblV+vyFctgRHeiB9AEm+Ie5ZZk
Mr43+67Dnpr7269SRLVASD/UdU56dNL3HTSgBRXCigL+eNaTqjm5fsPhSDTFYT9W
OlyBOx867RdR7xWDpsnGoHIDWFHknOXKb+Nt9jcM/4Z0g5CbyRb2cP7EjY+jh5QY
F2zTCCLzqbeuozn+JnegNgXoG4q+L8umJDHWc6yzllc1EZEfGHj+kenGGtAgNjQU
Iw+Q0/f0tlE3QcTePZMtPSiVWLujRQB8r++eIQIDAQABAoIBAQCBlSY7ulLNsbWo
xYkqLRd8sqxsQpjpDq7GrP3oleUgUjAEE1LInp5WqqrdUt3iKTIE8lANUubM4zmu
t04tvBSkoibhfyTx5yQZ+JPJ1rFJM9SuzFRVYFZUQYrLPpsso0xEcwwxa40ik+JZ
TylWonCbR2ZCHKKiOLI1NFA3cD5OPa0ws7UUuHJrpXpT/6iNohc5TamMZ8SAcZYb
JQ0q22F63NJu0TcrXNWgGZ+1gq92Fbvr8OOF8e0rYi4LNGhUa3JB9zqsG4a1WEm/
9U7jBoTzCLURF+BACyYHiCQev6We4PKfM7PSlZikoobsN8f47hU5TemB95GlmLUF
Boc/QhM9AoGBAMhZuZ6P3gyB3/Z3eSpGToIe6rOElTb3n9/EG5m3oncxTfpoidY6
qSEw50Qx3ONEdwZKCLywgC/PyT4MshgBKb3QDdtHNOzdJu7/XYPWGmCKy0OTsdAw
qkPLS8QxwwipGg4UpV69VylcgLW0Ic5h6hM2QHyX6/DH2yjOpCyutboXAoGBAMHV
IV9fQUe42+HGoBPaY7U6F8YPptERhptOtVPjwvjfHtV4ns1tiXrpCYJyQ4blTXwv
XEAVtYaFC0r1taTcIVBgiitZRUYIYDNxRu1o5Uh+mKDx6Oopq/je6ofP6qbJvWrD
6xyDV1OpJg/27aNSKBKu0MXIFNaWU3faPNBPUuSHAoGBAMfCrpCqYXuXAvd7qBO+
hgi3SswzJZPo0j8KCXr63cJ6JoXGNaikVH4DhJM6JEN1wDdFGfEJCsahJsX3YpsM
jdHz390C4oJI/sjNVTBeCW649HIskN5Dp4Bw8tprw0qfUJs5eqk7n+xdjvec4xgk
CH+fWCziTR2GJ75ISulCSW+BAoGBAI94bzxFqG8rEG38dC5dvuG8fy5WFXa4jzbT
unm+o2lM1WjS4FBT3KgCZ8yLQRpPDdx2vcSdjQBl5+bzGiFN4sa4vmy5pgHhXzuU
hBhkRRo0wkqW5Hy2nZkXfudJ6XjM6IxnOdagDPpawFaewmTaAdlaViOfJnVzTZLw
EYVqwYNvAoGBAIPM0ZlHdT1Z8BaYq7g661HpRSvJiEiPai8zDoGyR6MuWfRknLh2
FoGSqBaD6oCHyD0iE4DxUAwhk/h24gtQZp9Q5arZSEpF88RNQWmo8mDN688a9fy9
pWTJ2zrkXG33MUc5Z1uF57TsPuib+ovbD3mhpvUp8eY1WMN3I25x410m
**-----END RSA PRIVATE KEY-----**


#### Demo: Jenkins slave using jnlp

Configure new node with these configs

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image009.png)


Created a new ubuntu vm as builder2

* installed java with  **sudo apt install openjdk-8-jdk**
* get the slave.jar file from jenkins - **wget http://docker:8080/jnlpJars/agent.jar**
* get the entire command from the node page on jenkins - **java -jar agent.jar -jnlpUrl http://docker:8080/computer/builder2/slave-agent.jnlp -secret 426b17912e47cf3b496934f896ee37edbd57d776b23b18122af369f8543cf3ed -workDir "/var/jenkins"**


After running the jnlp command above the builder is connected.

**NOTE:  **If the builder node is rebooted, the connection will be lost. The jnlp script needs to be run at boot in this scenario. Or run the builder as a docker container, and if it loses connection it will automatically start again

Instructor claims that the java web start for windows just hooks up. Will have to try that with a win10 vm soon.

Blue Ocean
----------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image010.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image011.png)

#### Demo: Blue Ocean

Blue Ocean is geared toward pipeline builds.
The Blue Ocean interface is available as a plugin.
It is not complete and some functions such as configuring the build will return to the classic interface.


ssh-agent
---------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image012.png)

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image013.png)

Essentially this means that the master will act as a proxy between the builder and the resource that requires ssh key authentication. The builder will get access to the resource without having the required keys stored on it.
Another step toward immutable builders that are easily recreated or scaled.

#### Demo: ssh-agent

First step:  create a new credential for the git checkout user of type SSH with public/private key

Then in the Jenkinsfile the block to use it looks like this:

node {
  stage('do something with git') {  
**sshagent (credentials: ['github-key'])** {
  // get the last commit id from a repository you own
  sh 'git ls-remote -h --refs [git@github.com:wardviaene/jenkins-course.git](mailto:git@github.com:wardviaene/jenkins-course.git) master |awk "{print $1}"'
}
  }
}
	
github-key is the ID of the credential with the keypair associated with it.
With the ssh-agent plugin, the master will authenticate with the repo and the pipeline will have access 

From the log for the build, the ssh-add command is invoked and it adds a key from the workspace that was generated by the plugin.

**NOTE:  **As with any ssh connection from the master to a remote host, the remote host must be in master's *known_hosts* file, so the ssh-keyscan utility is used to get the keys from the remote host.
**ssh-keyscan <remote host> >> <JENKINS_HOME>/.ssh/known_hosts**


Security best practices
-----------------------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image014.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image015.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image016.png)


Authentication and Authorization
--------------------------------

![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image017.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image018.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image019.png)

Option 2 is the less risky of the two options. You can explicitly grant your user the admin rights needed to fix the authorization issue.
So you :

* stop the jenkins master
* edit the config manually on the host
* start the jenkins master again using the changes you made in Option 1/2



Authentication Providers for Jenkins
------------------------------------


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image020.png)


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image021.png)

**NOTE:  **SAML is not exclusive to Onelogin. It is an open format and works with others like google as well


![](./CICD.Jenkins.Pipelines_files/UDEMY.COURSES/CICD.Jenkins.Pipelines/Section.8.Advanced.Jenkins.Usage/pasted_image022.png)
