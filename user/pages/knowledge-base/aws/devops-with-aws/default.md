---
visible: true
title: DevOps with AWS
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,devops,aws]
---
[toc]
**AWS KEY**:
- ID - AKIAUACKSF65GPB6OBWU
- Secret - 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
## Section 1: Cloud Services and the DevOps Mindset

### Layers of services in a cloud environment

Cloud computing breaks down to 3 major service models:
- IaaS - computing resources in a virtualized environment, offering processing power, memory, storage, and network:
  - virtual machines
  - network equipment such as load balancers and virtual IP's
  - storage such as block devices
The IaaS layer sits directly on top of the hardware that cloud providers have in data centers.
- Paas - Common components such as data store (S3) and a queue.
  - this layer provides a number of ready to use apps such as database
- SaaS - managed services dedicated to certain purposes such as management and monitoring

When building an application, two things make a significant difference:
- relying on these cloud services, and adopting a DevOps mindset.

### Adopting a DevOps culture

A devops culture allows developers and operations teams to work together. Devops advocates the implementation of several engineering best practices, relying on tools and technologies covered in this course.

#### The origin of DevOps

The DevOps movement has its official start in Belgium in 2009, when a group of people met at the first DevOpsdays conference, organized by Patrick Debois. They discussed how to apply some agile concepts to infrastructure.

Agile methodologies transformed how software is developed. The agile method addressed some issues of the traditional _waterfall_ method of software development:

![](./waterfall.jpeg)

However, agile did not extend beyond the QA cycles, leaving a better but still waterfall method, not improving the release or deployment stages:

![](./waterfall2.jpeg)

The agile process improved the collaboration of designers, developers, and QA teams. However, collaboration between the developers and ops teams needed to be reassessed.

#### The developers vs operations dilemma

The classic struggle is that developers are motivated to ship new code as quickly as possible, while operations is concerned with system stability and uptime. Changes on the production platform are a downtime risk, and require scheduled maintenance windows, and proceeding with caution.

#### Differences in the prod vs dev environment

Often, the dev environment is different than the production environment, and as a result software passes the tests in dev but fails in prod due to things like different software libraries or differences in security or scale.

#### Key aspects of a DevOps culture

**source control everything**

Not just the product code, configs, tests, docs, and all of the infrastructure automation. The last piece is the product of infrastructure as code methods brought about by cloud comptuing technologies.

**automate testing**

Too often developers focus on implementing new features but not adding a test to the new code.

Within DevOps there are 4 levels of testing automation:
- unit testing - test the functionality of each code block
- integration testing - make sure services and components work together
- user interface (UI) testing - often the most challenging
- system testing - end to end testing of all components of an application

**automate deployment**

It is easier to write small chunks of new code and deploy as soon as possible to ensure they are working. This requires continuous integration and continuous deployment pipelines.

Here is the process:
- a new chunk of code is committed
- the CI pipeline kicks off running automated testing
- if there is no regression, the code is merged into mainline code base
- master is recompiled with new code, and it is pushed to the CD pipeline.
- the CD pipeline will deploy the new build to several environments including staging, integration, maybe pre-production, and finally, passing all tests, production.

Sometimes new features are not made accessible to end users right away. This is done with _feature flagging_ or _dark launches_.

By setting a flag in the service config to describe who gets access to the new feature, you can drive production traffic to the new service to evaluate impact on databases or performance.

This also allows to evaluate the end user experience of the new feature and compare to users not yet accessing it, to gauge impact.

**measure everything**

The DevOps methodology feeds off of metrics to assess and improve the overall quality of the product and team working on it.

Some of the most important metrics include:
- number of builds pushed to prod each day
- number of rollbacks in prod
- percentage of code coverage
- frequency of alerts paging on-call engineers
- frequency of outages
- application performance
- **mean time to resolution** (MTTR) - the speed at which an outage or issue is resolved

Points to consider in measuring the impact of adopting a DevOps culture:
- amount of collaboration across teams
- team autonomy
- cross functional work
- fluidity in the product
- how often dev and ops communicate
- happiness among engineers
- attitudes towards automation
- obsession with metrics

DevOps changes the culture of dev and ops being separate silos, making them collaborate more during all phases of the SDLC.

It requires not just a new mindset, but a specific set of toolss geared toward automation, deployment and monitoring.

### Deploying in AWS

Throughout this course, we will deploy a web application as our core product, scaling the infrastructure supporting it and making it more secure, by following devops practices.We will also learn how AWS provides many services and systems to perform a number of common tasks such as computing, networking, load balancing, storing data, monitoring, programmatically managing infrastructure and deployment, caching, and queuing.

#### AWS synergy with the DevOps culture

To better facilitate teams working together, The API is accessible through the command line CLI tool, along with a number of SDKs including Java, JavaScript, Python, .NET, PHP, Ruby Go, and C++.

These SDKs allow you to administer and use the managed services without relying on the AWS web interface, which becomes more difficult as your app grows in complexity.

Some of the major services we will use are:
- EC2 - at the computing level to create virtual servers
- EC2 auto scaling - a service to scale pools on EC2 instances to handle traffic spikes and host failures
- ECS - the _elastic container service_ for docker containers.
- ELB - the _elastic beanstalk_ for creating and deploying the appication
- AWS Lambda - for creating serverless functions to run custom code without hosting it on our EC2 instances.

To implement the CI and CD pipelines we will rely on these 4 services:
- AWS Simple Storage Service (S3): This is the object store service that will allow us to store our artifacts
- AWS CodeBuild: This will let us test our code
- AWS CodeDeploy: This will let us deploy artifacts to our EC2 instances
- AWS CodePipeline: This will let us orchestrate how our code is built, tested, and deployed across environments

For monitoring and measuring  :
- CloudWatch
- ElasticSearch/Kibana

To stream data to these services:
- Kinesis

For email and SMS alerts:
- SNS

For infrastructure management:
- CloudFormation

For security:
- Amazon Inspector
- AWS Trusted Advisor

Explore the IAM and VPC services in more detail.

## Section 2: Deploying the Hello World web app

In this section we will use the CLI and the web interface to create and configure a we4b server to host the web app.

**prepare workstation for AWS cli:
- reacquire access key ID and secret
- install pip
- install awscli
- run `aws configure` to set up cli with access key info
- verify with `aws iam list-users`
**NOTE:** Per book suggestion, I ran `sudo pip install aws-shell` to get a complete shell environment with command completion and help docs.

### Create web server with CLI

Setting up the server involves the `aws ec2 run-instances` command. The following info is needed:
- AMI ID
- instance type
- security group
- SSH keypair

#### Amazon Machine Images

The AMI image is the OS and additional software required to boot the system. To find the proper AMI, use `aws ec2 describe-images`. By default this command will list all available public AMI's which is millions.

You must filter the `aws ec2 describe-images` command when using it. We will use the following filters:
- name - Amazon Linux, the officially supported RedHat based image.
- arch - x86_64
- virtualization type - HVM, the newest and fastest
- GP2 support - use the newest gen of instances. The newest generation of instances dont come with _instance store_, this means the servers powering the instances will be separate from the servers storing the data.

Also, sort the output by age to only look at the most recently released AMI. The command is:
`aws ec2 describe-images --filters "Name=description,Values=Amazon Linux AMI * x86_64 HVM GP2" --query 'Images[*].[CreationDate, Description, ImageId]' --output text | sort -k 1 | tail` and this is the output:

    2018-01-10T18:55:00.000Z	Amazon Linux AMI 2017.09.1.20180108 x86_64 HVM GP2	ami-ca1c47b0
    2018-01-15T19:14:50.000Z	Amazon Linux AMI 2017.09.1.20180115 x86_64 HVM GP2	ami-97785bed
    2018-01-18T23:05:02.000Z	Amazon Linux AMI 2017.09.1.20171120 x86_64 HVM GP2	ami-1ac9e760
    2018-03-07T06:59:59.000Z	Amazon Linux AMI 2017.09.1.20180307 x86_64 HVM GP2	ami-1853ac65
    2018-03-07T07:00:50.000Z	Amazon Linux AMI 2017.09.1-testlongids.20180307 x86_64 HVM GP2	ami-07fc3cb791f32513e
    2018-04-13T00:32:59.000Z	Amazon Linux AMI 2018.03.0.20180412 x86_64 HVM GP2	ami-467ca739
    2018-05-08T18:06:53.000Z	Amazon Linux AMI 2018.03.0.20180508 x86_64 HVM GP2	ami-14c5486b
    2018-06-22T22:26:53.000Z	Amazon Linux AMI 2018.03.0.20180622 x86_64 HVM GP2	ami-cfe4b2b0
    2018-08-11T02:30:11.000Z	Amazon Linux AMI 2018.03.0.20180811 x86_64 HVM GP2	ami-0ff8a91507f77f867
    2018-11-08T00:26:36.000Z	Amazon Linux AMI 2018.03.0.20180811 x86_64 HVM GP2	ami-07edebf3b6affeea1

From this result, the optimal image is **ami-07edebf3b6affeea1**.

#### Instance types

The virtual hardware for the instance. We will use the **t2.micro** (unless I find a better deal later on) since free tier is not strictly required.

#### Security groups

These are like firewalls. All EC2 instances have a set of security groups assigned to them, and each group has rules for ingress and egress traffic.

We will create a small web app on port _tcp/3000_. Also for SSH _tcp/22_.

**NOTE:** Since I am using the aws shell, I will omit the `aws` prefix from all commands.

Steps to create security group:

1. Find default **VPC** (virtual private cloud) ID. A VPC is like a virtual datacenter with its own virtual network. The security groups are bound to subnets, which are bound to the virtual network:
![](./vpc.png)
To find the VPC ID use `aws ec2 describe-vpcs`.Here is the output:

    {
        "Vpcs": [
            {
                "VpcId": "vpc-eab00e90",      # here is what we need
                "InstanceTenancy": "default",
                "CidrBlockAssociationSet": [
                    {
                        "AssociationId": "vpc-cidr-assoc-aff17ac3",
                        "CidrBlock": "172.31.0.0/16",
                        "CidrBlockState": {
                            "State": "associated"
                        }
                    }
                ],
                "State": "available",
                "DhcpOptionsId": "dopt-2e5b0055",
                "OwnerId": "275034288058",
                "CidrBlock": "172.31.0.0/16",
                "IsDefault": true
            }
        ]
    }


2. Using the VPC ID, create security group as follows:

    aws ec2 create-security-group \--group-name HelloWorld \--description "Hello World Demo" \--vpc-id vpc-eab00e90

The output:

    {
        "GroupId": "sg-0ed04db5494ad16d2"
    }

3. Open ports _tcp/22_ and _tcp/3000_ for inbound ingress traffic:

    aws ec2 authorize-security-group-ingress \--group-name HelloWorld \--protocol tcp \--port 22 \--cidr 0.0.0.0/0
    aws ec2 authorize-security-group-ingress \--group-name HelloWorld \--protocol tcp \--port 3000 \--cidr 0.0.0.0/0

4. Verify the ingress rules with:
`aws ec2 describe-security-groups \--group-names HelloWorld \--output text`

The output:

    SECURITYGROUPS	Hello World Demo	sg-0ed04db5494ad16d2	HelloWorld	275034288058	vpc-eab00e90
    IPPERMISSIONS	22	tcp	22
    IPRANGES	0.0.0.0/0
    IPPERMISSIONS	3000	tcp	3000
    IPRANGES	0.0.0.0/0
    IPPERMISSIONSEGRESS	-1
    IPRANGES	0.0.0.0/0


#### Generating SSH keypairs

You can either generate a keypair on EC2 and download the private key, or roll your own and import the public key into EC2. Here we will create them on EC2:

    ec2 create-key-pair --key-name EffectiveDevOpsAWS --query 'KeyMaterial' --output text > ~/.ssh/EffectiveDevOpsAWS.pem

    ec2 describe-key-pairs --key-name EffectiveDevOpsAWS

        {
      "KeyPairs": [
          {
              "KeyName": "EffectiveDevOpsAWS",
              "KeyFingerprint": "66:59:70:31:86:b2:d3:7e:50:4c:29:7d:64:8d:ea:0d:c7:21:30:a5"
          }
      ]
    }

On my workstation, i need to set permissions for the keypair:

`chmod 400 ~/.ssh/EffectiveDevOpsAWS.pem`

#### Launching the EC2 instance

Now that all above steps are complete the command to launch the instance is:

    ec2 run-instances \
    --instance-type t2.micro \
    --key-name EffectiveDevOpsAWS \
    --security-group-ids sg-0ed04db5494ad16d2 \
    --image-id ami-07edebf3b6affeea1

This AMI has a paid subscription attached and redirected me to subscribe before launch, I am going to try the instance from the book - `ami-cfe4b2b0`
**WORKED**.

The output from `run-instances` will produce a JSON formatted blob including the instance id which we will need to connect:

      ......
    "StateTransitionReason": "",
                "InstanceId": "i-0b1c70ba093dd2371",
                "ImageId": "ami-cfe4b2b0",
                "PrivateDnsName": "ip-172-31-92-192.ec2.internal",
                "KeyName": "EffectiveDevOpsAWS",
                "SecurityGroups": [
                    ........

#### Connect to instance via SSH

We are starting with a vanilla OS, we now need to connect to the instance and prepare it to host a web app.

1. Get the DNS name of the running instance:

    ec2 describe-instances \
    --instance-ids i-0b1c70ba093dd2371 \
    --query "Reservations[*].Instances[*].PublicDnsName"

The output:

    [
        [
            "ec2-3-83-204-63.compute-1.amazonaws.com"
        ]
    ]

Connect with private key that is stored on local machine: (not in aws-shell)

`ssh -i ~/.ssh/EffectiveDevOpsAWS.pem ec2-user@ec2-3-83-204-63.compute-1.amazonaws.com`

#### Create simple Hello World app

For tech companies, the most common use of AWS is hosting apps. For this course, we will use JavaScript, one of the most popular languages on GitHub. Knowledge of Javascript is not required to learn the DevOps principles taught in this course. It is just a good example to illustrate the concepts because:
- fairly easy to write and read even for beginners
- not compiled
- can be run server side with node.js
- officially supported by AWS and the AWS SDK for Javascript is a first class citizen.

All remaining tasks in this section are on the instance.

**install node.js**

**NOTE:** Amazon Linux runs on this instance and it already has EPEL in it.

`sudo yum install --enablerepo=epel -y nodejs`
The version installed is _0.10.48_. Old, but good enough for this course.

Here is the JS code that will execute from the instance:

    var http = require("http") http.createServer(function (request, response) {
    // Send the HTTP header
    // HTTP Status: 200 : OK
    // Content Type: text/plain
    response.writeHead(200, {'Content-Type': 'text/plain'})
    // Send the response body as "Hello World" response.end('Hello World\n')
    }).listen(3000)

    // Console will print the message console.log('Server running')

Use wget to pull the js file in to instance:

    wget https://raw.githubusercontent.com/yogeshraheja/Effective-DevOps-with-AWS/master/Chapter02/helloworld.js -O /home/ec2-user/helloworld.js

Run the server on the instance:

    node helloworld.js
    Server running

The app comes up at `http://ec2-3-83-204-63.compute-1.amazonaws.com:3000` with a simple _Hello World_.

**run the nodejs app as a service**

The Amazon Linux image we are using has `upstart` on it. Upstart has a significant advantage for instances that use elastic network interfaces; it will respawn the service until the interface is completely online.

We can configure the app as a service by adding the following info into `/etc/init/helloworld.conf`:

    description "Hello world Daemon"

    # Start when the system is ready to do networking. Start on started elastic-network-interfaces

    # Stop when the system is on its way down. Stop on shutdown

    respawn script
    exec su --session-command="/usr/bin/node /home/ec2-user/helloworld.js" ec2-user
    end script

**Once this is done simply start the service with: `sudo start helloworld`.

**terminate the instance**

Finished with this exercise, to terminate the instance:
`ec2 terminate-instances --instance-ids i-02dc3b22c984066d7`

## Section 3: Treating your Infrastructure as Code

In a cloud environment where everything is abstracted away and served up as virtual resources, code can describe the topology of a network and configuration of a system. This works to accomodate 2 core missions of devops, everything in source control, and automation.

There are 2 key concepts of effective DevOps:
- Infrastructure as Code (IAC) - describing all virtual resources as code, stored in source control
- configuration management - automating OS config, packages and app deployment

The end result of adopting these concepts is being able to manage infrastructure resources using the same tools and processes as those used in app development. We can use source control, making small changes in branches, submitting pull requests, and testing changes before they are applied to production.

Also by corraling the changes, we can manage bigger fleets of resources without adding more engineers or spending a lot more time on the process.

Further, it opens the door to more automation such as CI/CD.

### Managing infrastructure with CloudFormation

CloudFormation allows you to create JSON or YAML files to describe your desired infrastructure. Then by uploading the files to CloudFormation, they are executed, and your infrastructure is in place.

First a review of how CloudFormation is structured, then we will create a list stack to reproduce the Hello World application.

Then a look at 2 more options for creating CloudFormation templates, the designer, which allows you to visually create the template in a web GUI, and CloudFormer, which generates templates from existing infrastructure.

#### Getting started with CloudFormation

CloudFormation is accessed either on the AWS console, or via command line with `aws cloudformation` commands.

While designer and CloudFormer are useful tools for architecting infrastructure, they do not take advantage of the JSON file format available with CloudFormation. One of the most powerful aspects of CloudFormation is the ability to write code to dynamically generate CloudFormation templates.

On that note, we wil use Python and a library called **troposphere** to generate a Hello World CloudFormation template.

Install the troposphere library with pip:

Upgrade setuptools first:  `pip install -U setuptools` then do `pip install troposphere`

Create `helloworld-cf-template.py`.

    """Generating CloudFormation template."""

    from troposphere import (     # import necessary libraries
        Base64,
        ec2,
        GetAtt,
        Join,
        Output,
        Parameter,
        Ref,
        Template,
    )

    ApplicationPort = "3000"      # set variable to easily change port for future uses

    t = Template()

    t.add_description("Effective DevOps in AWS: HelloWorld web application")

    t.add_parameter(Parameter(      # parameter gives us a dropdown to pick keypair
        "KeyPair",
        Description="Name of an existing EC2 KeyPair to SSH",
        Type="AWS::EC2::KeyPair::KeyName",
        ConstraintDescription="must be the name of an existing EC2 KeyPair.",
    ))

    t.add_resource(ec2.SecurityGroup(     # this will create a security group
        "SecurityGroup",
        GroupDescription="Allow SSH and TCP/{} access".format(ApplicationPort),
        SecurityGroupIngress=[
            ec2.SecurityGroupRule(
                IpProtocol="tcp",
                FromPort="22",
                ToPort="22",
                CidrIp="0.0.0.0/0",
            ),
            ec2.SecurityGroupRule(
                IpProtocol="tcp",
                FromPort=ApplicationPort,
                ToPort=ApplicationPort,
                CidrIp="0.0.0.0/0",
            ),
        ],
    ))

    ud = Base64(Join('\n', [        # create userdata object to run on instance when created
        "#!/bin/bash",              #  Base64 will convert binary stream to text
        "sudo yum install --enablerepo=epel -y nodejs",
        "wget http://bit.ly/2vESNuc -O /home/ec2-user/helloworld.js",
        "wget http://bit.ly/2vVvT18 -O /etc/init/helloworld.conf",
        "start helloworld"
    ]))

    t.add_resource(ec2.Instance(
        "instance",
        ImageId="ami-cfe4b2b0",     # the ami and type could be parameterized
        InstanceType="t2.micro",
        SecurityGroups=[Ref("SecurityGroup")],    # the Ref keyword allows to point to SecurityGroup
        KeyName=Ref("KeyPair"),                   # defined in previous section of this template
        UserData=ud,
    ))

    t.add_output(Output(      # the output blocs will result in the info being available on
        "InstancePublicIp",                       # cloudformation after running this template
        Description="Public IP of our instance.",
        Value=GetAtt("instance", "PublicIp"),
    ))

    t.add_output(Output(
        "WebUrl",
        Description="Application endpoint",
        Value=Join("", [
            "http://", GetAtt("instance", "PublicDnsName"),
            ":", ApplicationPort
        ]),
    ))

    print (t.to_json())       # this will print the object with all attributes to json

By executing this python script, the last print line will output the t object with all attributes set in the method calls. We will execute the script and redirect the output to a file called `helloworld-cf.template`.

Next we go to web console and to the **CloudFormation** page, to create a new stack by uploading the `helloworld-cf.template` file.

After doing all steps, the `t.add_output` blocks at the end of the python script above will result in us being able to see the IP and the URL to access the app running in the stack just created by CloudFormation.

**NOTE:** Notice in the script that the security group and

#### Updating the CloudFormation stack

By using a CloudFormation template to manage resources, we can update the template to make changes to the running stack.

**updating the `helloworld-cf-template.py` script**

The purpose of troposphere is to use python to build the json file for CloudFormation more easily than writing the json by hand. In this section we will write more python to update the stack. In doing so we will take advantage of more services and external resources.

First off, we will change the CIDR value in the block allowing SSH to our instance. Instead of opening port 22 to the world, we will restrict it to just our workstation by using our public IP.

There is an online tool available which can use a simple `curl` command to retrieve the public IP:   `curl https://api.ipify.org`. It will simply return the public ip.

We can incorporate this tool into our template by using a python library called **ipify** in the template script. First install the library:  `pip install ipify`.

Since we need to state the public IP as CIDR in the script, we need another library:  `pip install ipaddress`.

Now edit the script file by adding import lines for the new libraries:

    ...
    from ipaddress import ip_network
    from ipify import get_ip
    from troposphere import (
        Base64,
        ec2,
        GetAtt,
        Join,
        Output,
        Parameter,
        Ref,
        Template,
    )

    ApplicationPort = "3000"
    PublicCidrIp = str(ip_network(get_ip()))
    ...

Next change the CidrIp block in the template as follows:

    SecurityGroupIngress=[
            ec2.SecurityGroupRule(
                IpProtocol="tcp",
                FromPort="22",
                ToPort="22",
                CidrIp=PublicCidrIp,
            ),
    ....
        ]

Now we can generate a new template and version it as follows:
`python helloworld-cf-template.py > helloworld-cf-v2.template`

A diff verified the newly generated template has the public IP in CIDR form for port 22 access.

**update the stack with new SSH config**

Simply go to the web console page for CloudFormation and click **Update** at the top, follow the prompts to upload the new template-v2 file, then the changes take effect.

The change can also be verified via awscli. First obtain the security group name then:
`aws ec2 describe-security-groups \--group-names HelloWorld-SecurityGroup-1KZ6KOHFI4X9G`

The output includes the following:

    ......
    "PrefixListIds": [],
                       "FromPort": 22,
                       "IpRanges": [
                           {
                               "CidrIp": "136.49.228.197/32"
    ....

#### Change sets

As templates become more complex, so does the risk of unexpected changes when performing an update as above. For example, rather than simply updating an existing security group, what if a new sg needs to be created?

AWS offers a safer way to update templates called **change sets**, accessible from the CloudFormation console.

The steps are a lot alike except at the last step, there is more emphasis on reviewing the changes to ensure nothing unexpected happens. Also, you can review recent changes using the **Change Sets** tab on the console.

#### Delete the stack

Very simple, just click **Delete Stack** on the console.

**summary**

While CloudFormation accomodates resource creation very well, it does not do much for something like updating services like EC2. CloudFormation does not track the state of a running instance. Therefore, to update an instance you would need to recreate it and once the recreated instance matches the existing one, simply swap them.

This is fine if the instance is immutable and designed to run stateless. If however, it is a long running service and has state, you will need a configuration management tool to properly update it.

### Adding a configuration management system

#### Getting started with Ansible

To get started we will relaunch the helloworld stack, this time using the awscli. This must be run from the repo folder where the tempate file lives:

    aws cloudformation create-stack \
        --capabilities CAPABILITY_IAM \
        --stack-name ansible \
        --template-body file://helloworld-cf-v2.template \
        --parameters ParameterKey=KeyPair,ParameterValue=EffectiveDevOpsAWS

The output:

    {
        "StackId": "arn:aws:cloudformation:us-east-1:275034288058:stack/ansible/f01fa220-95f2-11e9-9177-1251e602a9c6"
    }

Next, create a repo on GitHub for ansible.

**create host inventory dynamically**

Rather than using the standard hosts file, Ansible can query the AWS API to fetch the inventory. It does this with a python script from the official Ansible git repo:

    curl -Lo ec2.py http://bit.ly/2v4SwE5
    chmod +x ec2.py

The script needs a config file before use. Create `ec2.ini` and add:

    [ec2]
    regions = all
    regions_exclude = us-gov-west-1,cn-north-1
    destination_variable = public_dns_name
    vpc_destination_variable = ip_address
    route53 = False
    cache_path = ~/.ansible/tmp
    cache_max_age = 300
    rds = False

**NOTE:** the book doesn't mention it, but the script needs `boto` library.

By executing the script with `./ec2.py` a long file is returned with all resources found for my account:

    {
      "275034288058": [
        "54.145.32.35"
      ],
      "_meta": {
        "hostvars": {
          "54.145.32.35": {
            "ansible_host": "54.145.32.35",
            "ec2__in_monitoring_element": false,
            "ec2_account_id": "275034288058",
            "ec2_ami_launch_index": "0",
            "ec2_architecture": "x86_64",
            "ec2_block_devices": {
              "xvda": "vol-00dd41e1d782954fc"
            },
            "ec2_client_token": "ansib-insta-1L7RW9GKDFF42",
            "ec2_dns_name": "ec2-54-145-32-35.compute-1.amazonaws.com",
            "ec2_ebs_optimized": false,
            "ec2_eventsSet": "",
            "ec2_group_name": "",
            "ec2_hypervisor": "xen",
            "ec2_id": "i-08fc3a24cf913e071",
            "ec2_image_id": "ami-cfe4b2b0",
            "ec2_instance_profile": "",
            "ec2_instance_type": "t2.micro",
            "ec2_ip_address": "54.145.32.35",
            "ec2_item": "",
            "ec2_kernel": "",
            "ec2_key_name": "EffectiveDevOpsAWS",
            "ec2_launch_time": "2019-06-23T20:10:37.000Z",
            "ec2_monitored": false,
            "ec2_monitoring": "",
            "ec2_monitoring_state": "disabled",
            "ec2_persistent": false,
            "ec2_placement": "us-east-1b",
            "ec2_platform": "",
            "ec2_previous_state": "",
            "ec2_previous_state_code": 0,
            "ec2_private_dns_name": "ip-172-31-38-85.ec2.internal",
            "ec2_private_ip_address": "172.31.38.85",
            "ec2_public_dns_name": "ec2-54-145-32-35.compute-1.amazonaws.com",
            "ec2_ramdisk": "",
            "ec2_reason": "",
            "ec2_region": "us-east-1",
            "ec2_requester_id": "",
            "ec2_root_device_name": "/dev/xvda",
            "ec2_root_device_type": "ebs",
            "ec2_security_group_ids": "sg-014af80dda18c93b7",
            "ec2_security_group_names": "ansible-SecurityGroup-XNEQYHHD7IG4",
            "ec2_sourceDestCheck": "true",
            "ec2_spot_instance_request_id": "",
            "ec2_state": "running",
            "ec2_state_code": 16,
            "ec2_state_reason": "",
            "ec2_subnet_id": "subnet-8bef80d7",
            "ec2_tag_aws_cloudformation_logical_id": "instance",
            "ec2_tag_aws_cloudformation_stack_id": "arn:aws:cloudformation:us-east-1:275034288058:stack/ansible/f01fa220-95f2-11e9-9177-1251e602a9c6",
            "ec2_tag_aws_cloudformation_stack_name": "ansible",
            "ec2_virtualization_type": "hvm",
            "ec2_vpc_id": "vpc-eab00e90"
          }
        }
      },
      "ami_cfe4b2b0": [
        "54.145.32.35"
      ],
      "ec2": [
        "54.145.32.35"
      ],
      "i-08fc3a24cf913e071": [
        "54.145.32.35"
      ],
      "instance_state_running": [
        "54.145.32.35"
      ],
      "key_EffectiveDevOpsAWS": [
        "54.145.32.35"
      ],
      "security_group_ansible_SecurityGroup_XNEQYHHD7IG4": [
        "54.145.32.35"
      ],
      "tag_aws_cloudformation_logical_id_instance": [
        "54.145.32.35"
      ],
      "tag_aws_cloudformation_stack_id_arn_aws_cloudformation_us_east_1_275034288058_stack_ansible_f01fa220_95f2_11e9_9177_1251e602a9c6": [
        "54.145.32.35"
      ],
      "tag_aws_cloudformation_stack_name_ansible": [
        "54.145.32.35"
      ],
      "type_t2_micro": [
        "54.145.32.35"
      ],
      "us-east-1": [
        "54.145.32.35"
      ],
      "us-east-1b": [
        "54.145.32.35"
      ],
      "vpc_id_vpc_eab00e90": [
        "54.145.32.35"
      ]
    }

Included in the above output is the public IP of the instance running in our stack.

The last step is to configure Ansible to retrieve our inventory from AWS. Create a file called `ansible.cfg` in the ansible repo:

    [defaults]
    inventory      = ./ec2.py
    remote_user  = ec2-user
    become = True
    become_method  = sudo
    become_user    = root
    nocows     = 1

The above config file will point Ansible to use the `ec2.py` script to find the hosts that make up the AWS inventory.

#### Executing Ansible modules on instances

First example is using the standard Ansible `ping` module to find our instance IP and ping it:

`ansible --private-key ~/.ssh/EffectiveDevOpsAWS.pem ec2 -m ping`

The Ansible ping returns successfully:

    54.145.32.35 | SUCCESS => {
        "ansible_facts": {
            "discovered_interpreter_python": "/usr/bin/python"
        },
        "changed": false,
        "ping": "pong"
    }

**NOTE**  To eliminate having to specify `--private-key` when running Ansible like above, use a `~/,ssh/config` file to tie the key to the user:

    IdentityFile ~/.ssh/EffectiveDevOpsAWS.pem
    User ec2-user
    StrictHostKeyChecking no
    PasswordAuthentication no
    ForwardAgent yes

Created the config file, now i was able to run `ansible ec2 -m ping`.

Just like using `ssh -T` to execute commands on remote servers, Ansible can do the same using the **-a** option.

`ansible <host> -a <cmd>`

You can specify the target host using wildcards like `'54.145.32.*'`, for example `ansible '54.145.32.*' -a 'df -h'`. Note that this does NOT use the `ec2` script, it is pointed directly at the AWS subnet provided for our VPC.

**NOTE:** You can also use the **ec2** host specifier and have the python script find the hosts. I only have one instance running though, not sure if it will execute on multiple hosts, I suspect it will.

#### Using Ansible playbooks on instances

**deploy web app with roles**

The Ansible role will replace the **UserData** section of the template used to deploy the web app on the instance.

In order to follow the **DRY** (Don't Repeat Yourself) principle of reusable code, we can separate out the part that installs node.js, as this is likely not as subjective as the node.js code for the web app itself. This will be one of the roles we create. The other role will be to install the helloworld app.

Ansible by default looks for roles in the _roles_ directory at the root of the Ansible repo. Step 1 is to create this directory.

Community created roles are available from Ansible Galaxy, among others. Downloading pre-existing roles and creating new roles are both done using the `ansible-galaxy` command.
- `ansible-galaxy init` - to create a role from scratch.
- `ansible-galaxy install` - to download a role from galaxy

**create a nodejs role**

To create a new role for nodejs we issue the command:  `ansible-galaxy init nodejs` from within the _roles_ directory.
This results in the following directory structure in _roles_:

    nodejs/
    ├── defaults
    │   └── main.yml
    ├── files
    ├── handlers
    │   └── main.yml
    ├── meta
    │   └── main.yml
    ├── README.md
    ├── tasks
    │   └── main.yml
    ├── templates
    ├── tests
    │   ├── inventory
    │   └── test.yml
    └── vars
        └── main.yml

The most important directory is the **tasks** directory. When Ansible executes a playbook, it runs the code in the `tasks/main.yml` file. On creation the file contents are simply:

    ---
    # tasks file for nodejs

The goal of the role is to install **node.js**  and **npm**, so we will write code in this file to use the Ansible wrapper for `yum` to install them. We will use the `with_items` loop operator:

    ---
    # tasks file for nodejs

    - name: Installing node and npm
      yum:
        name: "{{ item }}"
        enablerepo: epel
        state: installed
      with_items:
        - nodejs
        - npm

The `with_items` loop works like:  _for item in with_items_.

This completes the role for installing node.js and npm. This role is a very basic example. You could also expand it to install specific versions, fetch the binaries from a source other than the epel repo, or to handle specific dependencies.

**create the helloworld role**

The next role is for installing the _helloworld_ application code.

From the roles directory, create the role with:   `ansible-galaxy init helloworld`.
This role will require copying files to the remote hosts. To do so we will first download the 2 necessary files to the **files** diretory of the new role:

    wget http://bit.ly/2vESNuc -O files/helloworld.js
    wget http://bit.ly/2vVvT18 -O files/helloworld.conf

Now add the tasks in the `tasks/main.yml` file:

    ---
    # tasks file for helloworld

    - name: Copying the application file
      copy:
        src: helloworld.js
        dest: /home/ec2-user/
        owner: ec2-user
        group: ec2-user
        mode: 0644
      notify: restart helloworld

    - name: Copying the upstart file
      copy:
        src: helloworld.conf
        dest: /etc/init/helloworld.conf
        owner: root
        group: root
        mode: 0644

    - name: Starting the HelloWorld node service
      service:
        name: helloworld
        state: started

The first two tasks will use the **copy** module to place the files on the instance. The last task uses the **service** module to start the upstart service defined by `helloworld.conf`.

The **notify** action is added for `helloworld.js` to trigger a restart of the helloworld service  if `helloworld.js` is modified.

Next we need to create a **handler** for the notify trigger. In roles everything is grouped together so there is a file `handlers/main.yml` for handlers of the role to live.

    ---
    # handlers file for helloworld

    - name: restart helloworld
      service:
        name: helloworld
        state: restarted

As you can see, this handler uses the same `service` module to restart the helloworld service.

Lastly, we need to establish the dependency that this role has on the nodejs role. This way, when the helloworld role is executed, it will first call the nodejs role to ensure nodejs is installed.

Dependencies go in the `meta/main.yml` file. The file currently has 4 sections:
- `galaxy_info` - where you put info about your role
- `min_ansible_version:` - for container enabled roles, to specify the minimum ansible container required to run this role
- `galaxy_tags` - for specifying tags used to find your role on galaxy
- `dependencies` - this is where we list the dependency on nodejs for the helloworld role

Here is the complete `mata/main.yml` file:

    galaxy_info:
      author: Yogesh Raheja
      description: DevOps Automation and Cloud Coach
      company: Thinknyx Technologies (optional)

      # If the issue tracker for your role is not on github, uncomment the
      # next line and provide a value
      # issue_tracker_url: http://example.com/issue/tracker

      # Some suggested licenses:
      # - BSD (default)
      # - MIT
      # - GPLv2
      # - GPLv3
      # - Apache
      # - CC-BY
      license: license (GPLv2, CC-BY, etc)

      min_ansible_version: 1.2

      # If this a Container Enabled role, provide the minimum Ansible Container version.
      # min_ansible_container_version:

      # Optionally specify the branch Galaxy will use when accessing the GitHub
      # repo for this role. During role install, if no tags are available,
      # Galaxy will use this branch. During import Galaxy will access files on
      # this branch. If Travis integration is configured, only notifications for this
      # branch will be accepted. Otherwise, in all cases, the repo's default branch
      # (usually master) will be used.
      #github_branch:

      #
      # platforms is a list of platforms, and each platform has a name and a list of versions.
      #
      # platforms:
      # - name: Fedora
      #   versions:
      #   - all
      #   - 25
      # - name: SomePlatform
      #   versions:
      #   - all
      #   - 1.0
      #   - 7
      #   - 99.99

      galaxy_tags: []
        # List tags for your role here, one per line. A tag is a keyword that describes
        # and categorizes the role. Users find roles by searching for tags. Be sure to
        # remove the '[]' above, if you add tags to this list.
        #
        # NOTE: A tag is limited to a single word comprised of alphanumeric characters.
        #       Maximum 20 tags per role.

    dependencies:
      - nodejs
      # List your role dependencies here, one per line. Be sure to remove the '[]' above,
      # if you add dependencies to this list.

This completes the helloworld role. Next is the playbook that will execute the roles.


**create the playbook**

At the repo root directory, create the `helloworld.yml` playbook file:

    ---
    - hosts: "{{ target | default('localhost') }}"
      become: yes
      roles:
        - helloworld

- The **hosts** line will use the variable `target` which we will supply at run-time on the command line.
- `localhost` will be used if no target is specified
- **become** means use elevated privileges (sudo) in this case

Since we have the dependency configured, we only need to call the helloworld role in the playbook.

**execute the playbook**

This is done with the `ansible-playbook` command. Of course, since it relies on the `ansible.cfg` file in the repo root, that is where we want to run it from.

The first run of the playbook includes the `--list-hosts` option, which will show what hosts are affected by the playbook before actually running on the hosts. This is a good strategy with ec2, since we are using the `ec2.py` script for dynamic host targets, rather than specifying them directly.

Also, the `-e` option allows to specify _extra-vars_ so we can use the `target` variable specified earlier in the helloworld role.

    ansible-playbook helloworld.yml \
      --private-key ~/.ssh/EffectiveDevOpsAWS.pem \   # not needed, we have the .ssh/config file
      -e target=ec2 \
      --list-hosts

The output, which shows the result of the python script when supplied with the _ec2_ pattern:

    playbook: helloworld.yml

      play #1 (ec2): ec2	TAGS: []
        pattern: ['ec2']
        hosts (1):
          54.145.32.35

Now we know our target. We can now use a `--check` option to execute a _dry-run_ mode in Ansible:

    ansible-playbook helloworld.yml \
        -e target=ec2 \
        --check

**NOTE:** the playbook failed to run because of a code change by Ansible that was interpreting Amazon Linux as RHEL **8** and thus trying to use the `dnf` package mgr instead of `yum`. I found on github that a fix was introduced so i reinstalled ansible in my venv with `pip install -U ansible`.

This produced the desired results for the dry run to ensure it will succeed:

54.145.32.35               : **ok=6**    **changed=3**    unreachable=0    **failed=0**    skipped=0    rescued=0    ignored=0

At this point I ran the playbook normally and all worked.

**Canary-testing changes**

This means quickly pushing out a change to a single host and testing out the changes. You can target the specific host easily using the `-e target=` option for `ansible-playbook`.

We will do a quick push and change the `helloworld.js` file. Since this file is copied in using `notify` in the playbook, Ansible will trigger the `handler` for the module and restart the service automatically.

- edited the helloworld.js to return **Hello World, Welcome again**
- ran `ansible-playbook helloworld.yml -e target=ec2` first with `--check` to test
- ran it again without dry run
- after running playbook, the curl returned the new string

**ROLLBACK** Since i did not commit the helloworld.js change i can just do a checkout of the file to revert the edits. Then i run the playbook again and the node is back where it was.

**Ansible pull mode**

To mitigate the risk of configuration drift that can occur when pushing changes to only subsets of hosts in an inventory, you can assure all hosts sharing a particular role all get the update by using _ansible pull_ on the hosts. This will require installing ansible and git on the hosts so they can run the pull command and access the repo containing the roles.

#### Install ansible and git on ec2 instance

For now we will use an ad-hoc command to install git and ansible on the instance:

    ansible ec2 \
    --become \
    -m yum -a 'name=git enablerepo=epel state=installed'

    ansible ec2 \
    --become \
    -m yum -a 'name=ansible enablerepo=epel state=installed'

This installed both packages, and I verified they work.

**configure localhost runs**

The pull method will require ansible to execute on the localhost, as it will be localhost that pulls and runs the plays on itself.

To facilitate this, first create a static inventory file in the ansible repo root:

`localhost` file:

    [localhost]
    localhost ansible_connection=local

Now, when the target is localhost, ansible will run a local connection, not SSH.

After pushing the changes in git, we need to add a cron job on the instance to kick off the pull. This is done again with an ad-hoc command to use the `cron` module:

    ansible ec2 \
    -m cron -a 'name=ansible-pull \
    minute="*/10" job="/usr/bin/ansible-pull \
    -U https://github.com/roninhockley/ansible \
    helloworld.yml -i localhost --sleep 60"'

Breakdown:
- `-m cron` - use module `cron`
- use cron with `-a` arguments for name, minute, and job `ansible-pull`
- `ansible-pull` -U is URL of file to pull
- **-i** is the inventory to run it against, here we specify localhost as configured previously.

 **NOTE:** I could not get the ad-hoc to work, it errored with _bad minute_. I put it instead in a playbook:

     ---
     - hosts: ec2
       tasks:
         - name: install cron job
           cron:
             name: ansible-pull
             minute: "*/10"
             job: "/usr/bin/ansible-pull -U https://github.com/roninhockley/ansible helloworld.yml -i localhost --sleep 60"

This worked no probs.

With the cron in place on the instance, I modified the helloworld.js again to add _Welcome again_ on the end of the message. I pushed the file to the remote. The cron job will:
-  run `ansible-pull` which will run the `helloworld.yml` playbook, kicking off the role
-  the role will copy the new version of the `helloworld.js` file
-  the copy module will trigger the notify and handler, to restart the service.

It all worked.

**Integrate Ansible with CloudFormation**

There are multiple strategies for doing this. For our setup there is an obvious path; using the `UserData` field, to initialize Ansible with the `ansible-pull` command.

Copy `helloworld-cf-template.py` template file to `ansiblebase-cf-template.py`.

Add following to file:

    ApplicationName = "helloworld"   #  define an app name

    GithubAccount = "roninhockley"    # set up github account
    GithubAnsibleURL = "https://github.com/{}/ansible".format(GithubAccount)

    AnsiblePullCmd = \    # sset up the ansible pull
    "/usr/bin/ansible-pull -U {} {}.yml -i localhost".format( GithubAnsibleURL,
    ApplicationName
    )

    ud = Base64(Join('\n', [ "#!/bin/bash",   # replace the UserData block
    "yum install --enablerepo=epel -y git", "pip install ansible",
    AnsiblePullCmd,
    "echo '*/10 * * * * {}' > /etc/cron.d/ansible- pull".format(AnsiblePullCmd)
    ]))

Now save the new file and create a new template from it.

`python ansiblebase-cf-template.py > ansiblebase.template`

**Create stack from command line**

Instead of using the web console, use awscli to create the stack:

    aws cloudformation create-stack \
    --stack-name helloworldv3 \
    --template-body file://ansiblebase.template \
    --parameters ParameterKey=KeyPair,ParameterValue=EffectiveDevOpsAWS

The output:

    {
        "StackId": "arn:aws:cloudformation:us-east-1:275034288058:stack/helloworldv3/ab27e190-9756-11e9-9a9b-127a71eef55e"
    }

Retrieve the public IP for the new instance using the stack name:

    aws cloudformation describe-stacks \--stack-name helloworldv3 \--query 'Stacks[0].Outputs[0]'

The output:

{
    "Description": "Public IP of our instance.",
    "OutputKey": "InstancePublicIp",
    "OutputValue": "**3.88.60.96**"
}

Test the instance with `curl`:

    curl 3.88.60.96:3000

    Hello World, Welcome again

Now commit new troposphere script. Then delete the stack, this time using awscli:

`aws cloudformation delete-stack --stack-name helloworldv3`

#### Monitoring

There are many well written blog posts online about how to monitor CloudFormation and Ansible.

You should also subscribe to an SNS topic for your stack creation to receive all events relating to the stack life cycle.

Ansible has a system of callbacks that can allow for automating Ansible execution. Receiving notifications when Ansible fails is even more important when using pull mode.

## Section 4: IaC with Terraform

Up to now we have created infrastructure using well accepted tools like CloudFormation, troposphere, and Ansible, all in concert. This is an efficient approach, but confined to AWS specifically. In large, heterogenous environments there could be more cloud platforms like Google cloud, Azure, OpenStack or VMWare.

Terraform is a tool that abstracts away the actual platform itself. It follows the recommended guidelines for IaC, and it allows for building, changing, and versioning infrastructure safely and efficiently.

Terraform has these benefits:
- easy to install
- a declarative approach to writing templates
- has both open source and enterprise offerings
- has idempotency
- a perfect match for all major cloud systems

However, Terraform is not:
- a configuration management tool. Something like Ansible is still needed.
- a low level tool like Boto for AWS

Installing requires only downloading a zip file, which contains one binary that goes to `/usr/bin`. Thats it.

### Create the Terraform Repository

Created repo `EffectiveDevOpsTerraform` on github. Cloned it to the venv where other repos are.

Created new directory called `firstproject` in repo.

**NOTE:** A good gitignore file is necessary for terraform. the .terraform files are too large for github. Here is the `.gitignore` i found at `https://github.com/github/gitignore/blob/master/Terraform.gitignore`.

    # Local .terraform directories
    **/.terraform/*

    # .tfstate files
    *.tfstate
    *.tfstate.*

    # Crash log files
    crash.log

    # Ignore any .tfvars files that are generated automatically for each Terraform run. Most
    # .tfvars files are managed as part of configuration and so should be included in
    # version control.
    #
    # example.tfvars

    # Ignore override files as they are usually used to override resources locally and so
    # are not checked in
    override.tf
    override.tf.json
    *_override.tf
    *_override.tf.json

    # Include override files you do wish to add to version control using negated pattern
    #
    # !example_override.tf

    # Include tfplan files to ignore the plan output of command: terraform plan -out=tfplan
    # example: *tfplan*

#### Create Terraform Template for AWS Instance Provisioning

Our first template `ec2.tf` starts with the following:

    provider "aws" {
    access_key = "AKIAUACKSF65GPB6OBWU"
    secret_key = "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"
    region = "us-east-1"
    }

**NOTE::** Obviously there are more ways to declare cloud providers rather than directly in the template. This method is called _static credentials_. Clearly, this file cannot go onto GitHub. Other methods include:
- env variables
- terraform var files
- AWS native credential files `~/.aws/credentials`
- and others

With the template in place we need to run `terraform init` to install the plugins for AWS.

Now we start adding resources to the `ec2.tf` file we created earlier. Resources are components of infrastructure, as simple as a DNS record, or a complete virtual server with multiple other services.

Every resource belongs to a provider and the type of the resource is suffixed with the provider name. Here is the form of a resource:

    resource "provider-name_resource-type" "resource-name" {
    parameter_name = “parameter_value”
    parameter_name = “parameter_value”
    .
    .
    }

Here we will create an `aws_instance` resource, making sure to use our particular security group:

    # Resource Configuration for AWS
    resource "aws_instance" "myserver" {    # name the resource myserver
      ami = "ami-cfe4b2b0"
      instance_type = "t2.micro"
      key_name = "EffectiveDevOpsAWS"
      vpc_security_group_ids = ["sg-0ed04db5494ad16d2"]

      tags {
        Name = "helloworld"
      }
    }

Now we can validate the template with `terraform validate`.

It fails, turns out a change in version 12 of Terraform requires the `tags` block have an **=** operator to assign the mapping:

    tags = {            # adding + sign here fixes it. previous versions allowed assigning without it
      Name = "helloworld"
    }
    }

Next we perform a dry run using `terraform plan`.

The dry run indicates that one instance will be created. This is what we want so we now execute the template with `terraform apply`.

The instance was created:

`aws_instance.myserver: Creation complete after 34s [id=i-0f5a83fda6e64320d]`

Moreover, Terraform creates a `.tfstate` file in the execution directory with specifics on the resources created by the template file. The file is in JSON format, but you can view it human readable with `terraform show`

Destroying the resources created is as simple as `terraform destroy`. By using the `.tfstate` file, Terraform will remove all the resources it created.

#### Deploy the hello world app with Terraform Provisioner

Create a new project directory called `secondproject`.

Recall the UserData field from helloworld-cf-temtplate.py:

    ud = Base64(Join('\n', [
        "#!/bin/bash",
        "sudo yum install --enablerepo=epel -y nodejs",
        "wget http://bit.ly/2vESNuc -O /home/ec2-user/helloworld.js",
        "wget http://bit.ly/2vVvT18 -O /etc/init/helloworld.conf",
        "start helloworld"

There are 3 steps in getting the app deployed:
- prepare the server to run the app by installing nodejs
- copy the resources needed for the app; the JS code and the upstart config
- start the `helloworld` service

Terraform Provisioner uses configuration blocks that are available for several resources, which allow you to perform actions after resources are created. Mostly used for EC2 instances, provisioners are primarily used as **post build steps** to install lightweight apps or config mgmt agents like Pupper or Chef agents. They can also be used to run the config mgmt tools, such as playbooks, puppet modules, chef cookbooks etc.

Create a new template in the `secondproject` directory called `helloworlddec2.tf`

    # Provider Configuration for AWS
    provider "aws" {}                   # using env variables since this file is on github

    # Resource Configuration for AWS
    resource "aws_instance" "myserver" {
      ami = "ami-cfe4b2b0"
      instance_type = "t2.micro"
      key_name = "EffectiveDevOpsAWS"
      vpc_security_group_ids = ["sg-0ed04db5494ad16d2"]

      tags = {
        Name = "helloworld"
      }

    # Helloworld Appication code
      provisioner "remote-exec" {   # to get this working I had to consult terraform website
        connection {
          host = self.public_ip
          user = "ec2-user"
          private_key = "${file("~/.ssh/EffectiveDevOpsAWS.pem")}"
      }
        inline = [
          "sudo yum install --enablerepo=epel -y nodejs",
          "sudo wget https://raw.githubusercontent.com/roninhockley/ansible/master/roles/helloworld/files/helloworld.js -O /home/ec2-user/helloworld.js",
          "sudo wget https://raw.githubusercontent.com/roninhockley/ansible/master/roles/helloworld/files/helloworld.conf -O /etc/init/helloworld.conf",
          "sudo start helloworld",
        ]
      }
    }


Since this is a new project directory, we need to rerun `terraform init` to reinitialize the AWS plugin.

Next we **validate, plan and apply** the template.

It all worked and I was able to curl the app's http payload "Hello World, Welcome again".

### Integrate AWS, Terraform, and Ansible

There are 2 scenarios for consideration in this section:
- terraform with ansible push mode
- terraform with ansible pull mode

#### Terraform with Ansible push mode

Created a new project directory `thirdproject` and in that directory is the new terraform template `helloworldansible.tf` along with an `ansible.cfg` file and the `roles` directory from the ansible repo. Slight modifications were made to all, as they will not be using the troposphere library this time.

New project requires a new `terraform init` as usual to pull in the AWS plugin. Once that passed, I applied the template and the curl once again retrieved the correct response.

#### Terraform with Ansible pull mode

As before, we need to ensure ansible is installed on the new instance and that it runs `ansible-pull`.

Created a new project directory `fourthproject` containing a new terraform template `helloworldansiblepull.tf` which uses the `remote-exec` provisioner to connect to the instance and run `ansible-pull`:

    # Provider Configuration for AWS
    provider "aws" {
      region     = "us-east-1"
    }

    # Resource Configuration for AWS
    resource "aws_instance" "myserver" {
      ami = "ami-cfe4b2b0"
      instance_type = "t2.micro"
      key_name = "EffectiveDevOpsAWS"
      vpc_security_group_ids = ["sg-0ed04db5494ad16d2"]

      tags {
        Name = "helloworld"
      }

    # Provisioner for applying Ansible playbook in Pull mode
      provisioner "remote-exec" {
        connection {
          user = "ec2-user"
          private_key = "${file("~/.ssh/EffectiveDevOpsAWS.pem")}"
        }
        inline = [
          "sudo yum install --enablerepo=epel -y ansible git",
          "sudo ansible-pull -U https://github.com/roninhockley/ansible helloworld.yml -i localhost",
        ]
      }

    }

    # IP address of newly created EC2 instance
    output "myserver" {
     value = "${aws_instance.myserver.public_ip}"
    }

Run init, plan, and apply. The instance returned the curl result as desired.

## Section 5: Adding Continuous Integration/Deployment

Two new Amazon services are introduced in this section:
- CodeDeploy - define how new code is deployed to instances
- CodePipeline - orchestrate the full life cycle of applications

### Building a CI pipeline

We will use an ansible role to create the jenkins server. From the `ansible/roles` directory, to initialize a new role for jenkins; `ansible-galaxy init jenkins`.

Next, edit the tasks file for the new jenkins role; `jenkins/tasks/main.yml`:

    - name: Removing old version of JAVA from Amazon Linux
      yum:
        name: java-1.7.0-openjdk
        state: absent

    - name: Install specific supported version of JAVA
      yum:
        name: java-1.8.0-openjdk
        state: present

    - name: Add Jenkins repository
      shell: wget -O /etc/yum.repos.d/jenkins.repo https://pkg.jenkins.io/redhat/jenkins.repo

    - name: Import Jenkins GPG key
      rpm_key:
        state: present
        key: https://pkg.jenkins.io/redhat/jenkins.io.key

    - name: Install Jenkins
      yum:
        name: jenkins-2.99
        enablerepo: jenkins
        state: present

    - name: Start Jenkins
      service:
        name: jenkins
        enabled: yes
        state: started

Amazon Linux comes with Java 7, but jenkins needs 8. Next we add the repo and keys for the repo. Then install jenkins and start and enable the jenkins service.

The jenkins instance needs to test a nodejs app, so we need to either add nodejs as a dependency of the jenkins role, or add it to the playbook that will deploy the jenkins role.

Add to playbook is the better option. Create `jenkins.yml` in ansible root directory:

    ---
    - hosts: "{{ target | default('localhost') }}"
      become: yes
      roles:
        - jenkins
        - nodejs

Now that we are working with CI/CD we follow best practices and checkout a branch to work with:

`git checkout -b jenkins`, then add the playbook and role for jenkins. Next, I pushed the branch to github, then created a pull request to get it merged into master. Instead of using the github button to merge I did it from command line with:

- `git fetch origin`
- `git merge master`
- `git checkout master`
- `git merge --no-ff jenkins`
- `git push origin master`

This more closely follows the DevOps practices for working with source code with a team, when CI is involved. Continuous integration inherently means frquent commits and merges so a structured approach is necessary.

#### Create CloudFormation Template

We're not at the point of using Terraform yet with the CI/CD setup, so we are using CloudFormation for now. This means we need to python Troposphere script to create our CF template.

Clone the `ansiblebase-cf-template.py` file to `jenkins-cf-template.py`.

The following additions were made to the template file:

**add instance profile**

The EC2 instance needs to interact with AWS directly. We will import some additional python libraries to assign an IAM role to the instance. This allows the instance to interact with AWS services (EC2 in this case) without needing the access keys.

    from troposphere.iam import (
        InstanceProfile,
        PolicyType as IAMPolicy,
        Role,
    )

    from awacs.aws import (
        Action,
        Allow,
        Policy,
        Principal,
        Statement,
    )

    from awacs.sts import AssumeRole

The next part creates the role resource using the python libraries imported above. This goes after the creation of the UserData **ud** variable:

    t.add_resource(Role(
        "Role",
        AssumeRolePolicyDocument=Policy(
            Statement=[
                Statement(
                    Effect=Allow,
                    Action=[AssumeRole],
                    Principal=Principal("Service", ["ec2.amazonaws.com"])
                )
            ]
        )
    ))

The next part creates the **instance profile** which points to the role created above. This will be assigned to the instance afterward:

    t.add_resource(InstanceProfile(
        "InstanceProfile",
        Path="/",
        Roles=[Ref("Role")]
    ))

Finally, in the declaration of the instance, we add the reference to the instance profile, which assigns the role to the instance:

    t.add_resource(ec2.Instance(
        "instance",
        ImageId="ami-cfe4b2b0",
        InstanceType="t2.micro",
        SecurityGroups=[Ref("SecurityGroup")],
        KeyName=Ref("KeyPair"),
        UserData=ud,
        IamInstanceProfile=Ref("InstanceProfile"),  # add the instance profile
    )

Finally, create the CF template: `python jenkins-cf-template.py > jenkins-cf.template`.

Now we can create the stack using the awscli:

    aws cloudformation create-stack \
    --capabilities CAPABILITY_IAM \
    --stack-name jenkins \
    --template-body file://jenkins-cf.template \
    --parameters ParameterKey=KeyPair,ParameterValue=EffectiveDevOpsAWS

The output:

    {
        "StackId": "arn:aws:cloudformation:us-east-1:275034288058:stack/jenkins/de3cbe10-984c-11e9-bd28-12de81a2e318"
    }

After stack is complete, get the public IP:

    aws cloudformation describe-stacks \
    --stack-name jenkins \
    --query 'Stacks[0].Outputs[0]'

The output:

    {
        "Description": "Public IP of our instance.",
        "OutputKey": "InstancePublicIp",
        "OutputValue": "54.91.17.137"
    }

Now we wait for the `ansible-pull` mechanism we configured for the instance to install jenkins.

When it is done we can go to `http:54.91.17.137:8080` to get to jenkins **Getting Started** page.

Problems here. Switching to another Terraform centric course on Udemy.
