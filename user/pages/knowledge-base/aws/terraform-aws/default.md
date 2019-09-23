---
visible: true
title: AWS Automation with Terraform
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,aws,terraform]
---

[toc]

Sections 1 and 2 nothing new.

## Section 3: Terraform Basics

Terraform file structure:
- assign values with 'key = value'
- "strings" are double quoted
- strings can be updated when wrapped in ${}
- multiline strings support heredoc <<EOF EOF
- lists are ["foo","bar"]
- maps are {"foo": "bar"}

The syntax for config files is called **HCL** or Hashicorp Configuration Language.

### CLI and common commands

- `terraform init` install or reinstall modules
- `terraform apply` apply without plan
- `terraform plan` build and show plan before applying
- `terraform plan -out=<path>` put plan in file
- `terraform plan -out destroy-plan` create a plan for a destroy before destroying
- `terraform apply destroy-plan` execute destroy plan
- `terraform destroy` destroy without using a plan
- `terraform refresh` refresh and show
- `terraform show` display known state
- `terraform validate` validate config
- `terraform apply plan` apply a plan

## Section 4: More Basics

### Variables

Use variables for elements that change. Such as different AMIs for regions, and to hide credentials that should not go in GIT. It also makes it easier to reuse files.

**EXAMPLE WITHOUT VARIABLES**

Here is a terraform file `instance.tf` to deploy an instance, and the file is not using variables:

    provider "aws" {
      access_key = "ACCESS_KEY_HERE"
      secret_key = "SECRET_KEY_HERE"
      region     = "us-east-1"
    }

    resource "aws_instance" "example" {
      ami           = "ami-0111e8c43a763eb71"
      instance_type = "t2.micro"
    }

**WITH VARIABLES**

The _access_key_, _secret_key_, and _region_ will all be supplied by variables, along with the _ami_, which will be supplied by a **mapping** which will assign the AMi based on the region.

This will require 3 new files:
- `provider.tf` - holds the block with assignments to variables
- `vars.tf` - holds the variables themselves, with values assigned EXCEPT for secrets
- `terraform.tfvars` - secret values are not in **vars.tf**, they are in here. this file will be ignored in the git repo with `.gitignore`, so secrets are not compromised.

The `provider.tf` file:

    provider "aws" {
        access_key = "${var.AWS_ACCESS_KEY}"
        secret_key = "${var.AWS_SECRET_KEY}"
        region = "${var.AWS_REGION}"
    }

The `vars.tf` file. Secrets have blank **{}** indicating values are elsewhere:

    variable "AWS_ACCESS_KEY" {}
    variable "AWS_SECRET_KEY" {}
    variable "AWS_REGION" {
      default = "eu-west-1"     # a default can be set here
    }
    variable "AMIS" {
      type = "map"    # declare a map variable which maps AMI choices to region being used
      default = {
        us-east-1 = "ami-13be557e"
        us-west-2 = "ami-06b94666"
        eu-west-1 = "ami-0d729a60"
      }
    }

Finally, the `terraform.tfvars` with the values for secrets:

    AWS_ACCESS_KEY = "<KEY>"
    AWS_SECRET_KEY = "<KEY>"
    AWS_REGION = ""

The region can be left blank since there is a default set. Or it can be overridden here.

Here is the new `instance.tf` file with variables being used. The _provider_ block is gone completely, and the _resource_ block now uses a **lookup** to access the **region = ami** mappings to set the ami based on the region being used:

    resource "aws_instance" "example" {
      ami           = "${lookup(var.AMIS, var.AWS_REGION)}"
      instance_type = "t2.micro"
    }

**NOTE:** Although indicated in the course, and maybe even on the terraform website, the lookup for the ami fails unless a value is expressly given in the `terraform.tfvars` file. The default set in the `vars.tf` file does not get picked up. I will figure this out later.

### Software Provisioning

There are primarily 2 ways to provision software on instances:
- build your own custom AMIs with your software bundled in. **Packer** is a great tool for this
- use standard AMIs then install the software using
-  _file uploads_
-  _remote exec_
-  a config mgmt tool like Ansible.

#### File uploads

The following new `instance.tf` file adds uploading a script file and executing it. It also adds a keypair for authentication to the instance to do so:

    resource "aws_key_pair" "mykey" {
      key_name = "mykey"
      public_key = "${file("${var.PATH_TO_PUBLIC_KEY}")}"   #identify the location of the public key
    }

    resource "aws_instance" "example" {
      ami = "${lookup(var.AMIS, var.AWS_REGION)}"
      instance_type = "t2.micro"
      key_name = "${aws_key_pair.mykey.key_name}"   # install the public key on the instance

      provisioner "file" {
        source = "script.sh"
        destination = "/tmp/script.sh"    # upload the script file
      }
      provisioner "remote-exec" {   # use the remote-exec provisioner to run the script
        inline = [
          "chmod +x /tmp/script.sh",
          "sudo /tmp/script.sh"
        ]
      }
      connection {
        host = "${self.public_ip}"    # the connection block for terraform to upload and run script
        user = "${var.INSTANCE_USERNAME}"
        private_key = "${file("${var.PATH_TO_PRIVATE_KEY}")}"
      }
    }

**NOTE** The term _inline_ used above simply means you are writing the code directly in the `.tf` file above, and not referencing an external file to get the commands.

The `script.sh` file that is uploaded and executed will install nginx on the instance. After applying the the plan I tested with curl that nginx was indeed on the instance.

### Outputting attributes

This is about how to retrieve attributes like the _public_ip_ from the instance. Useful for both displaying info at build or run time, or feeding into external software.

You can refer to any attribute by specifying the **resource type, resource name,** and the **attribute name** as below:

    output "ip" {
        value = "${aws_instance.example.public_ip}"   # use output to display the public ip
    }

To use the attributes in a script, use the **provisioner** block. In the following example we are using _local-exec_, to execute the provisioner locally on our machine, not on the instance. The block below will simply echo out to our terminal the **private_ip** attribute of the instance called _example_:

    provisioner "local-exec" {
       command = "echo ${aws_instance.example.private_ip} >> private_ips.txt"

This is useful for running automation scripts after provisioning, or populating an ansible host file so you can run playbooks against them later. You can also execute a script with the attributes as arguments, to gather a report of resource names and their IP addresses, for instance.

Here is the `instance.tf` file with outputs added in. It now has a new **provisioner (local-exec)** block and an **output** block:

    resource "aws_key_pair" "instancekey" {
      key_name = "instancekey"
      public_key = "${file("${var.PATH_TO_PUBLIC_KEY}")}"
    }

    resource "aws_instance" "example" {
      ami = "${lookup(var.AMIS, var.AWS_REGION)}"
      instance_type = "t2.micro"
      key_name = "${aws_key_pair.instancekey.key_name}"

      provisioner "local-exec" {
         command = "echo ${aws_instance.example.private_ip} >> private_ips.txt"
      }
      provisioner "file" {
        source = "script.sh"
        destination = "/tmp/script.sh"
      }
      provisioner "remote-exec" {
        inline = [
          "chmod +x /tmp/script.sh",
          "sudo /tmp/script.sh"
        ]
      }
      connection {
        host = "${self.public_ip}"
        user = "${var.INSTANCE_USERNAME}"
        private_key = "${file("${var.PATH_TO_PRIVATE_KEY}")}"
      }
    output "ip" {
        value = "${aws_instance.example.public_ip}"
    }

This resulted in the public IP being echoed out to my console after the apply was done. Also a file was created in the project directory with the private IP in it.

### Remote state

Terraform tracks the remote state of infrastructure using a file named `terraform.tfstate`. When executing `terraform apply` a backup is created of the current state if there is one, and a new state file is created.

If the remote state of a resource is changed, running `terraform apply` will make changes to restore the remote state to what is dictated in the `terraform.tfstate` file, restoring the resource to its _correct_ state. If you terminate an instance, apply will restart it.

As your project gets bigger, you may want to store the `.tfstate` file remotely in git. It gives you history of the state file, and allows to collaborate with others. It can, however, cause conflicts if multiple people work on it at the same time.

As an alternative, the state file can be stored using the **backend** functionality in terraform. The default is a **local backend**. You can also use:
- **S3** with a locking mechanism using dynamoDB
- **consul** with locking
- **terraform enterprise** a commercial solution

Backend has advantages:
- allows colaboration
- not stored locally, including any sensitive info
- _enhanced backends_ enable remote operations. the apply will then run completely remote.

To configure a remote state, you first add the code to a .tf file, then you run **init**. An example:

`backend.tf` using consul:

    terraform {
      backend "consul" {
        address = "demo.consul.io"
        path = "terraform/myproject"
      }
    }

A more common example using S3:

    terraform {
      backend "s3" {
        bucket = "mybucket"
        key = "terraform/myproject"
        region = "us-east-1"
      }
    }

When using the S3 backend, you need to have the credentials already configured before the apply, because terraform can not use the variables specified in the resource block. Run `aws configure` to make sure they are available.

Using remote state ensures you always have the latest version of the state. It avoids having to commit and push the `.tfstate` file to git.

You can also use a `data` block in a .tf file to specify a _read-only_ access to a remote store. This could be useful to generate outputs.

### Datasources

Certain providers like AWS make available a lot of dynamic information via their API. example:
- a list of AMIs
- a list of availability zones
- all IP addresses in use

Terraform exposes this info using _data sources_. You can for example use a datasource of IP addresses to create security groups that are filtered by protocol, IP range, and port.

Here is an example of using a datasource of IP ranges to create a security group. At the end it has a **SyncToken** which will update the IPs whenever apply is run:

    data "aws_ip_ranges" "european_ec2" {
      regions = [ "eu-west-1", "eu-central-1" ]
      services = [ "ec2" ]
    }

    resource "aws_security_group" "from_europe" {
     name = "from_europe"

      ingress {
        from_port = "443"
        to_port = "443"
        protocol = "tcp"
        cidr_blocks = [ "${data.aws_ip_ranges.european_ec2.cidr_blocks}" ]
      }
      tags {
        CreateDate = "${data.aws_ip_ranges.european_ec2.create_date}"
        SyncToken = "${data.aws_ip_ranges.european_ec2.sync_token}"
      }

    }

### Template Provider

One of the providers in Terraform is the template provider. For example you can use a template provider to help creating customized config files.

In AWS you can pass commands to be executed when an instance starts for the first time. AWS refers to this as **user-data**.

If you want to pass user-data that depends on other info in Terraform such as IP addresses, you can use the template provider for this.

Basically you create a _template file_ which has a script in it, and then you create a resource of type **template_file**, which will read in the script in the template file and populate the variables defined in the template file with data from an AWS data source.

To use the template, you create a new resource, and in a **user-data** block you specify the template file created earlier, then AWS will create the instance and execute the commands from the template file.

For more coverage there is a section on user-data later in the course.

### Other providers

Although the primary provider used in this course is **AWS**, potentially any API can be used as a terraform provider. There are many other providers such as Google, DigitalOcean.

There are providers for other things besides cloud providers, such as **Datadog** for monitoring, **GitHub**, **Mailgun** or **DNSSimple** for DNS hosting.

### Modules

Same concept as Puppet modules or Ansible roles. Reusable code, derived from either your pre-existing code or an external module from Github for instance. To use an existing module you create a module block and reference the module in it:

    module "consul" {
      source = "github.com/wardviaene/terraform-consul-module.git"
      key_name = "${aws_key_pair.mykey.key_name}"
      key_path = "${var.PATH_TO_PRIVATE_KEY}"
      region = "${var.AWS_REGION}"
      vpc_id = "${aws_default_vpc.default.id}"
      subnets {
        "0" = "${aws_default_subnet.default_az1.id}"
        "1" = "${aws_default_subnet.default_az2.id}"
        "2" = "${aws_default_subnet.default_az3.id}"
      }
    }
    output "consul-output" {
      value = "${module.consul.server_address}"
    }

Then, from within the project folder you run `terraform get` which will download the module. Then you will see a new folder named **modules**. The you run `terraform apply` and whatever the module creates it will create.

More on this later, from multiple sources.

### Terraform commands overview

Terraform focuses heavily on the resource definitions, and has a limited toolset available to modify, import and create these definitions.

Command | Description
---------- | ----------
`terraform apply` |  apply state
`terraform destroy` | destroy all terraform managed state
`terraform fmt` | rewrite terraform config files to cononical format
`terraform get` | donload and update modules
`terraform graph` | create visual representation of a config or plan
`terraform import [options] address id` | find infra resource by id, import state into terraform.tfstate
`terraform output [options] [name]` | output any of your resources. using name outputs specific resource
`terraform plan` | show the changes to be made to infrastructure
`terraform push` | push changes to _Atlas_ a centralized server available with Enterprise version
`terraform refresh` | refresh the remote state. show diff between state file and remote state
`terraform remote` | configure remote state storage such as S3
`terraform show` | show human readable output from state of plan
`terraform state` | advanced state mgmt. rename resource for example
`terraform taint` | mark a resource as tainted, it will be destroyed and recreated on next apply
`terraform validate` | validate terraform syntax
`terraform untaint` | undo a taint

**NOTE:** In the terraform docs, for every resource there is an example of import at the bottom.

## Section 5: Terraform with AWS

All topics related to using Terraform for creating infrastructure on AWS.

### Intro to VPC (Virtual Private Cloud)

**NOTE:** A VPC is sometimes called a virtual private _network_ by other providers. However, a VPC has nothing in common with a VPN really, so I will not mix the two.

AWS provides a _default_ VPC when creating resources. This is what I have been creating instances and CF templates on until now. Unless you specifically create your own VPC, your infrastructure will be sitting on a _default_ VPC.

A VPC pretty much does what it sounds like, it isolates your instances at the network level, like having your own private network in the cloud.

It is best practice to launch your instances in a VPC, either the default VPC or one you create yourself and manage with Terraform.

For smaller to medium setups, one VPC per region will suffice. Instances in separate VPCs cannot connect via their _private_ ip addresses, only via the DNS resolved _public_ address. This is not good as it traverses the internet.

You _could_ use **peering** to link VPCs together. But it is far more common to simply put all resources that need to connect together in one VPC.

!!! AWS has an **EC2 Classic** platform, which launches all instances on a flat network space. This is the original platform from AWS early days. It relies on you properly firewalling your instances away from the rest of the community.

A VPC is limited to one region, but can span multiple availability zones within that region:

![](./vpc1.png)

!!! To get info on availability zones, use `aws ec2 describe-availability-zones --region region-name`

#### Private subnets

A VPC divides resources using _private_ subnets, just as with anywhere else, they are the following universally denoted subnet ranges which do not route out to the internet:

- **10.0.0.0/8 - the largest one with an 8 bit mask. You can of course use the 10.0 numbering with a higher mask
- **172.16.0.0/12 - the second largest, and used by AWS by default.
- **192.168.0.0/16 - the smallest

Within a VPC, each availability zone (data center) has a _public_ and a _private_ subnet. From the graphic you can see that the _main_ VPC is **10.0.0.0/16** while the zones are differentiated using the 3d byte in the mask:  **10.0.X.0/24**.

![](./vpc2.png)

**NOTE:** Instances in the _main-private_ subnets can use a NAT gateway to get to the internet, but of course they are not reachable from the internet. As explained in the following graphic, public and private subnets are each suited to particular instances:

![](./vpc3.png)

**create a vpc**

The demo will create a VPC with NAT, using 2 .tf files; `nat.tf` and `vpc.tf`. I will adapt the `vpc.tf` file for my region _us-east-1_.

The `nat.tf`, needs no changes, it is relative to the vpc and creates the route rules for NAT forwarding, which allows instances on the private subnets to access the internet for updates:

    # nat gw
    resource "aws_eip" "nat" {    # assign an elastic IP (a sort of static IP)
      vpc      = true
    }
    resource "aws_nat_gateway" "nat-gw" {
      allocation_id = "${aws_eip.nat.id}"
      subnet_id = "${aws_subnet.main-public-1.id}"
      depends_on = ["aws_internet_gateway.main-gw"]
    }

    # VPC setup for NAT
    resource "aws_route_table" "main-private" {     # set up default gw
        vpc_id = "${aws_vpc.main.id}"
        route {
            cidr_block = "0.0.0.0/0"
            nat_gateway_id = "${aws_nat_gateway.nat-gw.id}"
        }

        tags {
            Name = "main-private-1"
        }
    }
    # route associations private                                  # set default gw on instances
    resource "aws_route_table_association" "main-private-1-a" {
        subnet_id = "${aws_subnet.main-private-1.id}"
        route_table_id = "${aws_route_table.main-private.id}"
    }
    resource "aws_route_table_association" "main-private-2-a" {
        subnet_id = "${aws_subnet.main-private-2.id}"
        route_table_id = "${aws_route_table.main-private.id}"
    }
    resource "aws_route_table_association" "main-private-3-a" {
        subnet_id = "${aws_subnet.main-private-3.id}"
        route_table_id = "${aws_route_table.main-private.id}"
    }

The `vpc.tf` adapted to my region _us-east-1_. This file creates the VPC itself and the subnets:

    # Internet VPC
    resource "aws_vpc" "main" {           # the main resource, a vpc named main
        cidr_block = "10.0.0.0/16"
        instance_tenancy = "default"      # allow multiple instances per physical machine
        enable_dns_support = "true"
        enable_dns_hostnames = "true"
        enable_classiclink = "false"    # link to ec2-classic, if you have any instances there
        tags {
            Name = "main"
        }
    }


    # Subnets                                 # create 3 public and 3 private subnets
    resource "aws_subnet" "main-public-1" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.1.0/24"
        map_public_ip_on_launch = "true"    #instances on the public subnet get a public IP
        availability_zone = "us-east-1a"

        tags {
            Name = "main-public-1"
        }
    }
    resource "aws_subnet" "main-public-2" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.2.0/24"
        map_public_ip_on_launch = "true"
        availability_zone = "us-east-1b"

        tags {
            Name = "main-public-2"
        }
    }
    resource "aws_subnet" "main-public-3" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.3.0/24"
        map_public_ip_on_launch = "true"
        availability_zone = "us-east-1c"

        tags {
            Name = "main-public-3"
        }
    }
    resource "aws_subnet" "main-private-1" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.4.0/24"
        map_public_ip_on_launch = "false"       # instances on private subnets do not get a public IP
        availability_zone = "us-east-1a"

        tags {
            Name = "main-private-1"
        }
    }
    resource "aws_subnet" "main-private-2" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.5.0/24"
        map_public_ip_on_launch = "false"
        availability_zone = "us-east-1b"

        tags {
            Name = "main-private-2"
        }
    }
    resource "aws_subnet" "main-private-3" {
        vpc_id = "${aws_vpc.main.id}"
        cidr_block = "10.0.6.0/24"
        map_public_ip_on_launch = "false"
        availability_zone = "us-east-1c"

        tags {
            Name = "main-private-3"
        }
    }

    # Internet GW                               # the public subnets need a gateway out to internet
    resource "aws_internet_gateway" "main-gw" {
        vpc_id = "${aws_vpc.main.id}"

        tags {
            Name = "main"
        }
    }

    # route tables
    resource "aws_route_table" "main-public" {    # give the instances on public subnets a default gw
        vpc_id = "${aws_vpc.main.id}"
        route {
            cidr_block = "0.0.0.0/0"
            gateway_id = "${aws_internet_gateway.main-gw.id}"
        }

        tags {
            Name = "main-public-1"
        }
    }

    # route associations public                                   # give public subnets a route table
    resource "aws_route_table_association" "main-public-1-a" {
        subnet_id = "${aws_subnet.main-public-1.id}"
        route_table_id = "${aws_route_table.main-public.id}"
    }
    resource "aws_route_table_association" "main-public-2-a" {
        subnet_id = "${aws_subnet.main-public-2.id}"
        route_table_id = "${aws_route_table.main-public.id}"
    }
    resource "aws_route_table_association" "main-public-3-a" {
        subnet_id = "${aws_subnet.main-public-3.id}"
        route_table_id = "${aws_route_table.main-public.id}"
    }

I copied in the `vars.tf`, `provider.tf` and `terraform.tfvars` files then ran `terraform init` followed by plan and apply, and ended up with a VPC as desired. 3 public and 3 private subnets across the first 3 availability zones in my region _us-east-1_.

### Launch instances in a VPC

We want to launch an instance in the VPC created above called **main**. The instance  will be launched with security groups, and using a keypair uploaded by terraform.

This will require an `instance.tf` file with 2 new variables to:
- link it to the subnet in our VPC
- assign a newly created security group to the instance

Also a `key.tf` file to place a public key on the instance, and a `securitygroup.tf` file to create the security group and tie it to the VPC.

Here is the `instance.tf` file:

    resource "aws_instance" "example" {
      ami           = "${lookup(var.AMIS, var.AWS_REGION)}"
      instance_type = "t2.micro"

      # the VPC subnet
      subnet_id = "${aws_subnet.main-public-1.id}"

      # the security group
      vpc_security_group_ids = ["${aws_security_group.allow-ssh.id}"]

      # the public SSH key
      key_name = "${aws_key_pair.mykeypair.key_name}"
    }

The security group we create will only allow SSH, and only from our workstation public IP **136.49.228.197**.

Here is the `securitygroup.tf` file:

    resource "aws_security_group" "allow-ssh" {
      vpc_id = "${aws_vpc.main.id}"               # attach the sg to our vpc
      name = "allow-ssh"
      description = "security group that allows ssh and all egress traffic"
      egress {
          from_port = 0
          to_port = 0
          protocol = "-1"               # -1 means all protocols
          cidr_blocks = ["0.0.0.0/0"]
      }

      ingress {
          from_port = 22
          to_port = 22
          protocol = "tcp"
          cidr_blocks = ["136.49.228.197/32"]
      }
    tags {
        Name = "allow-ssh"
      }
    }

The VPC had to be recreated because terraform does not know about the VPC created earlier, because the .state file for that VPC is not in the directory for this project. I'm sure there is a way to add a new instance to an existing VPC but it is not by simplying copying in new .tf files. Terraform apply seems to parse the files independent of the .tfstate in the directory.

This will be explained later I am sure.

**UPDATE:** You need to copy in the state files along with the VPC files used to create it, and terraform will add the instance to the existing VPC. Files to copy in are:
- **.tfstate**
- **.tfstate.backup**
- **vpc.tf**
- **nat.tf**

At this point you can run the apply and it shows 3 resources to be added, so obviously its not recreating the VPC.

Since the new instance was slated for a public subnet it did receive a public IP.

### EBS Volumes

**EBS**, or _Elastic Block Storage_ is separate from the instance itself, unlike _local storage_ which is ephemeral, and is always lost when the instance terminates. The AMI we have used for this course so far comes with 8GB of EBS storage. By default, the EBS volume attached to the instance is set to be removed when the instance is terminated. Although you could choose to retain the EBS volume, that is counter-intuitive.

The default EBS volume contains the root filesystem. If state needs to be preserved, it should be placed on an extra, dedicated volume. That volume will be retained unless you expressly remove it.

Below are 2 resources which can be added to the `instance.tf` to facilitate an extra EBS volume for persistent data:

    resource "aws_ebs_volume" "ebs-volume-1" {
        availability_zone = "eu-west-1a"
        size = 20
        type = "gp2"
        tags {
            Name = "extra volume data"
        }
    }

    resource "aws_volume_attachment" "ebs-volume-1-attachment" {
      device_name = "/dev/xvdh"
      volume_id = "${aws_ebs_volume.ebs-volume-1.id}"
      instance_id = "${aws_instance.example.id}"
    }

The first creates defines the volume itself, while the second block connects it to the instance.

The following steps are necessary to use this volume on the instance:
- format the volume with `mkfs.ext4 /dev/xvdh`
- mount it to the filesystem.

Of course, the instance root volume is ephemeral, including `/etc/fstab` so any use beyond the current session of the instance will require scripts to be added to the deployment .tf files using the _user-data_ variable, to do things like:
- to reuse existing data; add it to `/etc/fstab` to persist the mount.
- for a new deployment; format and mount the volume, then add to fstab.

Or some mixture of the above depending on the lifecycle of the deployment.

**ON A SIDE NOTE:** Rather than create an extra persistent volume for data, you could also modify the size or type of the root volume by adding to the instance resource block like below:

![](./ebs1.png)

Note above that by specifying the _delete_on_termination_ to true, you get the bigger root volume, but it still goes away on termination.

### Userdata

Userdata is an AWS feature used to do customization of an instance at creation, after the instance launches. Things like:
- install software
- prepare the instance to join a cluster like _consul_ or _ECS_
- execute commands/scripts
- mount volumes

Terraform can add userdata to the resource:
-  using a string for simple commands
-  using Terraform's template system for more complex operations

A simple example of a string to run a bash command:

![](./userdata1.png)

For complex operations, the terraform template system can be used, by using a provider and a template.

For our example, we will use the _cloudinit_ provider to handle the extra EBS volume from last section:

#### add an extra EBS volume to an instance with userdata

**step 1**

    Create `scripts/init.cfg` for initial init, to update and upgrade apt, then install lvm2:

        repo_update: true
        repo_upgrade: all

        packages:
          - lvm2

        output:
          all: '| tee -a /var/log/cloud-init-output.log'

**step 2**

Create `scripts/volumes.sh`, a shell script to prepare the extra volume. If the volume exists, it will be mounted but NOT formatted, otherwise format and mount:

    #!/bin/bash

    set -ex

    vgchange -ay

    DEVICE_FS=`blkid -o value -s TYPE ${DEVICE} || echo ""`
    if [ "`echo -n $DEVICE_FS`" == "" ] ; then
      # wait for the device to be attached
      DEVICENAME=`echo "${DEVICE}" | awk -F '/' '{print $3}'`
      DEVICEEXISTS=''
      while [[ -z $DEVICEEXISTS ]]; do
        echo "checking $DEVICENAME"
        DEVICEEXISTS=`lsblk |grep "$DEVICENAME" |wc -l`
        if [[ $DEVICEEXISTS != "1" ]]; then
          sleep 15
        fi
      done
      pvcreate ${DEVICE}
      vgcreate data ${DEVICE}
      lvcreate --name volume1 -l 100%FREE data
      mkfs.ext4 /dev/data/volume1
    fi
    mkdir -p /data
    echo '/dev/data/volume1 /data ext4 defaults 0 0' >> /etc/fstab
    mount /data

    # install docker
    curl https://get.docker.com |

**step 3**

Create

Create `cloudinit.tf` and in this file declare data sources to be consumed by _userdata_:

    data "template_file" "init-script" {
      template = "${file("scripts/init.cfg")}"    # use template_file to define the init.cfg
      vars {
        REGION = "${var.AWS_REGION}"
      }
    }
    data "template_file" "shell-script" {
      template = "${file("scripts/volumes.sh")}"    #use template_file to define the volumes.sh script
      vars {
        DEVICE = "${var.INSTANCE_DEVICE_NAME}"
      }
    }
    data "template_cloudinit_config" "cloudinit-example" {    # put it all together for handoff to userdata

      gzip = false
      base64_encode = false

      part {
        filename     = "init.cfg"
        content_type = "text/cloud-config"
        content      = "${data.template_file.init-script.rendered}"
      }

      part {
        content_type = "text/x-shellscript"
        content      = "${data.template_file.shell-script.rendered}"
      }

    }

**step 4**

Add a new variable to `vars.tf` for the device name, to be consumed above by `cloudinit.tf`:

    variable "INSTANCE_DEVICE_NAME" {
      default = "/dev/xvdh"
    }


**step 5**

Add the **user_data** line to `instance.tf` to run the complete template on the new instance:

    user_data = "${data.template_cloudinit_config.cloudinit-example.rendered}"

Now we need to first re-run `terraform init` because we added a new provider (template) and need to get the plugins.

It all worked, except the docker install fails. This is because there is no docker package for Ubuntu Dingo. Not a Terraform issue.

**NOTE:** The destroy fails because the volume stays attached to the instance, preventing the instance from being destroyed. Fixed by adding a line to the **aws_volume_attachment** resource block:

`skip_destroy = true`

### Static IPs, EIPs, and Route53

**static private IPs**

Private IP addresses are _auto-assigned_ to all instances in a subnet, according to the range for that subnet. If you want to keep a static private IP for an instance, add the following to the resource block:

![](./static1.png)

**static public IPs using EIP**

Public IP addresses are also auto-assigned. To specify a static _public_ IP for an instance, use the **aws_eip** resource type, and specify whether the instance is in a VPC:

![](./static2.png)

This uses an **EIP** (Elastic IP Address). If you reserve an EIP, but do not attach it to an instance, you will be charged for it.

To get the IP in an output block use the **public_ip** attribute of the aws_eip resource; `aws_eip.example-eip.public_ip` for the above example.

**hostnames with Route53**

Rather than using IP addresses, you will typically use hostnames, via the **Route 53** DNS service.

You can host a domain name on Route 53 by first registering a domain name and then creating a zone in Route 53.

After you have pointed your domain DNS to the AWS nameservers (which is explained on AWS website) you create a zone and add records in Terraform, as illustrated below:

![](./dns1.png)

### RDS Relational Database Services

RDS is a managed database solution with advantages including:
- easy setup for replication
- automated snapshots
- automated security updates
- easy instance replacement for vertical scaling

Supported databases:
- Mysql
- mariadb
- postgresql
- MS Sql
- Oracle

Steps to create an RDS instance:
- create a subnet group
  - specify what subnets the database will be in
- create a parameter group
  - specify parameters to change settings in the database
- create a security group allowing incoming traffic to the RDS instance
- create the RDS instance itself

The parameter group is the only way to specify settings in the instance as you do not have SSH access to the instance. Here is an example:

![](./rds1.png)

An example of the subnet block is below:

![](./rds2.png)

In the above subnet block, the instance will be placed in either of the 2 private subnets specified. If HA is enabled, it will be placed in both subnets.

Since the instance is going in a private subnet, it will only be accessible by instances in the same VPC, not from the internet.

In the following security group example, instead of specifying access for an entire CIDR range, egress is targeted at a particular security group. In this case it is the security group belonging to the _example_ instance.

![](./rds3.png)

Also, since it is a specific security group rather than a range, moving the RDS instance to another subnet would not cut off access to the _example_ instance.

Finally, here is the RDS resource itself:

![](./rds4.png)

- allocated_storage - 100 GB is a good minimum, otherwise the IO access will be slower
- instance_class - a _micro_ instance would be free
- db_subnet_group_name - specify subnet group created above
- parameter_group_name - same, you specify the group created above
- vpc_security_group_ids - also created above

To add the RDS instance to our running config:

Add the following new `rds.tf` file to specify the **subnet** and **parameter** groups, and to create the RDS instance itself:

    resource "aws_db_subnet_group" "mariadb-subnet" {
        name = "mariadb-subnet"
        description = "RDS subnet group"
        subnet_ids = ["${aws_subnet.main-private-1.id}","${aws_subnet.main-private-2.id}"]
    }

    resource "aws_db_parameter_group" "mariadb-parameters" {
        name = "mariadb-parameters"
        family = "mariadb10.1"
        description = "MariaDB parameter group"

        parameter {
          name = "max_allowed_packet"
          value = "16777216"
       }

    }


    resource "aws_db_instance" "mariadb" {
      allocated_storage    = 100    # 100 GB of storage, gives us more IOPS than a lower number
      engine               = "mariadb"
      engine_version       = "10.1.14"
      instance_class       = "db.t2.small"    # use micro if you want to use the free tier
      identifier           = "mariadb"
      name                 = "mariadb"
      username             = "root"   # username
      password             = "${var.RDS_PASSWORD}" # password
      db_subnet_group_name = "${aws_db_subnet_group.mariadb-subnet.name}"
      parameter_group_name = "${aws_db_parameter_group.mariadb-parameters.name}"
      multi_az             = "false"     # set to true to have high availability: 2 instances synchronized with each other
      vpc_security_group_ids = ["${aws_security_group.allow-mariadb.id}"]
      storage_type         = "gp2"
      backup_retention_period = 30    # how long you’re going to keep your backups
      availability_zone = "${aws_subnet.main-private-1.availability_zone}"   # prefered AZ
      skip_final_snapshot = true   # skip final snapshot when doing terraform destroy
      tags {
          Name = "mariadb-instance"
      }
    }

Add the **allow-mariadb** security group to the existing `securitygroup.tf` file:

    resource "aws_security_group" "example-instance" {
      vpc_id = "${aws_vpc.main.id}"
      name = "allow-ssh"
      description = "security group that allows ssh and all egress traffic"
      egress {
          from_port = 0
          to_port = 0
          protocol = "-1"
          cidr_blocks = ["0.0.0.0/0"]
      }

      ingress {
          from_port = 22
          to_port = 22
          protocol = "tcp"
          cidr_blocks = ["0.0.0.0/0"]
      }
    tags {
        Name = "example-instance"
      }
    }

    resource "aws_security_group" "allow-mariadb" {
      vpc_id = "${aws_vpc.main.id}"
      name = "allow-mariadb"
      description = "allow-mariadb"
      ingress {
          from_port = 3306
          to_port = 3306
          protocol = "tcp"
          security_groups = ["${aws_security_group.allow-ssh.id}"]              # allowing access from our example instance
      }
      egress {
          from_port = 0
          to_port = 0
          protocol = "-1"
          cidr_blocks = ["0.0.0.0/0"]
          self = true
      }
      tags {
        Name = "allow-mariadb"
      }
    }

Add an output block for the RDS instance endpoint to the existing `output.tf` file:

  `  output "rds" {
      value = "${aws_db_instance.mariadb.endpoint}"
    }`

Add the RDS password to the existing `vars.tf` file:

`variable "RDS_PASSWORD" { }`

And of cource add the actualy pw string to the `terraform.tfvars` file:

`RDS_PASSWORD = "nikkyron"`.

I ran the apply, it took nearly 10 minutes, waiting for the snapshot of the rds instance to finish.

After all done, I ssh to the EC2 instance, installed the mariadb client and was able to connect to the RDS instance.

### IAM

IAM (Identity & Access Management) contains **user, groups, and roles**.

Users can be in groups, such as _Administrators_  to grant admin privileges to users.

Roles can give users and services temporary access they normally would not have. For example:
- create a role called _mybucket-access_
- assign the role to an EC2 instance
- grant the role permissions to read and write to the _bucket _mybucket_
- when a user logs in to the instance, the can assume the role and access the bucket

A service can also assume a role, if the service has implemented the **AWS SDK**. If the instance running the service has the role assigned to it, the **AWS API** will give the temporary access to the service. Once the service assumes the role it will operate as if it has normal credentials.

IAM roles only work on EC2 instances, and only on instances in AWS. The temporary access credentials must be renewed, they only work for a predefined amount of time. This is something the AWS SDK can take care of.

**create an admin group in terraform**

Below is an example of a resource used to create an AWS _administrators_ group and assign the predefined AWS **AdministratorAccess** policy to it, which allows admin rights for users in that group:

![](./iam1.png)

Rather then using an AWS predefined policy, you can also create your own custom policy. The resource below effectively gives the same level of access as the predefined **AdministratorAccess** policy:

![](./iam2.png)

**create user and assign to admin group**

The resource declaration for this is below:

![](./iam3.png)

For this demo to create a new group, 2 new users, and add them to the group I created a new directory, copied the **vars.tf, .tfvars, and provider.tf** files to the new directory. I then added a new **iam.tf** to create the new users and group, and to assign the group to the users:

    # group definition
    resource "aws_iam_group" "administrators" {
        name = "administrators"
    }
    resource "aws_iam_policy_attachment" "administrators-attach" {
        name = "administrators-attach"
        groups = ["${aws_iam_group.administrators.name}"]
        policy_arn = "arn:aws:iam::aws:policy/AdministratorAccess"
    }
    # user
    resource "aws_iam_user" "admin1" {
        name = "admin1"
    }
    resource "aws_iam_user" "admin2" {
        name = "admin2"
    }
    resource "aws_iam_group_membership" "administrators-users" {
        name = "administrators-users"
        users = [
            "${aws_iam_user.admin1.name}",
            "${aws_iam_user.admin2.name}",
        ]
        group = "${aws_iam_group.administrators.name}"
    }

    output "warning" {
        value = "WARNING: make sure you're not using the AdministratorAccess policy for other users/groups/roles. If this is the case, don't run terraform destroy, but manually unlink the created resources"
    }

I ran `terraform init` to pull in the plugins, then ran apply and the new group was created with the new users assigned. As advised in the output, I did not run destroy, instead I removed the users from the group then removed the group, and finally I removed the users.

### IAM Roles

#### Create a role and assign to an instance

Step 1; add the role to the `instance.tf`:

    # role:
    iam_instance_profile = "${aws_iam_instance_profile.s3-mybucket-role-instanceprofile.name}"

Step 2; create the `iam.tf` to create the role, policy for the role, and the instance profile for the instance:

```
resource "aws_iam_role" "s3-mybucket-role" {
    name = "s3-mybucket-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "ec2.amazonaws.com"
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}

resource "aws_iam_instance_profile" "s3-mybucket-role-instanceprofile" {
    name = "s3-mybucket-role"
    role = "${aws_iam_role.s3-mybucket-role.name}"
}

resource "aws_iam_role_policy" "s3-mybucket-role-policy" {
    name = "s3-mybucket-role-policy"
    role = "${aws_iam_role.s3-mybucket-role.id}"
    policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
              "s3:*"
            ],
            "Resource": [
              "arn:aws:s3:::mybucket-nikkyrron",
              "arn:aws:s3:::mybucket-nikkyrron/*"
            ]
        }
    ]
}
EOF
}
```

Step 3; create the `s3.tf` file to create the s3 bucket resource:

    resource "aws_s3_bucket" "b" {
        bucket = "mybucket-nikkyrron"
        acl = "private"

        tags {
            Name = "mybucket-nikkyrron"
        }
    }

Next I ran the apply, then logged into the instance and installed **python-pip**, **python-dev**. and **awscli**. Then using awscli I created a file and pushed it to s3 with `aws s3 cp test.txt s3://mybucket-nikkyrron/test.txt`.

The file was successfully uploaded to s3, illustrating that the role was successfully allowing read/write access as expected.

Roles are a secure way to grant access because you are not exposing the main access keys and ID this way. The role uses a temporary key and ID, plus the role itself has an expiration date.

**see how the role is accessed via API**

**NOTE:** To see how the AWS API stores the role, which awscli is able to access, you can curl an AWS service like so:

`curl http://169.254.169.254/latest/meta-data/iam/security-credentials/s3-mybucket-role/`

and this will return the role:

    {
      "Code" : "Success",
      "LastUpdated" : "2019-07-03T21:34:25Z",
      "Type" : "AWS-HMAC",
      "AccessKeyId" : "ASIAUACKSF65HRVSTCWB",
      "SecretAccessKey" : "k/YerMupvfFY5y9OTdt9/mcfFiRfyUzMWduk1Jjk",
      "Token" : "AgoJb3JpZ2luX2VjEJb//////////wEaCXVzLWVhc3QtMSJGMEQCIB8jwf5cYf/RJHd+8uk+ymknCs1vwwhJSjWbdN+QV+qjAiBzD2B4+iTYnbnSFWbFF561IGLC/o3uTBA9HT9AdAOi5yrjAwjf//////////8BEAEaDDI3NTAzNDI4ODA1OCIMY6muBFUH7+WEkbxaKrcD7pJjZwOlTxUydsZMHr6Yake6WV1xXbeNtdAGTVSuOuqfBnNGNawSvHDgkC43LynQA1o6KYrIkvNnzO6hoTQGQdONvqydCWf2oJpnIuTny++xuVIMINaYbrlTX0fvBPieuiQmnzGa8si569FBzcD5COT1GpPDHlloSAtkepKNYmqxbs+/ar2YiM0Av72kIMVLKd9aONcs34ecamWVuiOwNBdEReIdUevweU6mESKixNJhzRitGHojeTXOsvFHCqkbu9Xh2LZyfu1fww5wikHWXnsL7QMNXv8X+Q3aPIgNAWKBDbkSB2sQlHKP4Oh6CCss7US10AiNEpKXhqdOFyxCPSIRb4qk+0PFXkxuzwYQV37oX+FtQuvwFzjsIo9QjiZFsdN+LE8JvccKIQElvVeKHhNurnKVK/wClEGNyT064W+V/vYU7H3zUaLe1QtG5IheYriovrAs0Fcsq8z5Zj8j8lhl4ZPLZxybzlMJLi7/lCHqewBdTtGlE9EmmYjLxXxGe1weqttzQM2RY6wfTKlpazVJFt+FPNoGZeyeIQPWW5DcKgT67i6v3gAOojACbU3Vgze2JfzPPTDfvvToBTq1AapjzEdnuof1l5BcS6wRpMu3a4HO16lL/5/LSw8JmdTLTTZZ8o5h0IJMoAi085lzNK9hZmJhm8303PPd3xdyai3PwMEIVzKSfjtkXi1M3rVJB2Dr0hQDE3BTB1oT9JkAnhbVylzNcM+/DkowEVKW3VEphmuAib3PdxrzbGSBunvTbTTLd9vXa7tHLDKEc+SwqE/a/Glxvlu+xU1we4vA/THjMbNkhbOxIZd62eoObUlV/8Wm6Sw=",
      "Expiration" : "2019-07-04T04:09:23Z"

      This is possible from the instance since the instance has the role assigned and thus access to retrieve this info.

### Autoscaling

In AWS, **autoscaling groups** automatically add/remove instances when thresholds are reached. Such as scaling out your app when traffic reaches a point.

To setup autoscaling you need at minimum 2 resources:
- the AWS **launch configuration - similar to an instance resource it specifies AMI, instance type etc
- an autoscaling group - specifies the scaling properties (min and max instances, health checks)
  - the health checks allow autoscaling to replace a failing instance

To scale based on defined policies such as CPU or memory usage:
- create an **autoscaling policy** which specifies how to scale when a threshold is reached
- create a **cloudwatch alarm**  which defines the threshold and when reached triggers the policy

Scaling may be either up or down based on minimum or maximum thresholds.

Below are launch configuration and autoscaling group resources:

    resource "aws_launch_configuration" "example-launchconfig" {
      name_prefix          = "example-launchconfig"
      image_id             = "${lookup(var.AMIS, var.AWS_REGION)}"
      instance_type        = "t2.micro"
      key_name             = "${aws_key_pair.mykeypair.key_name}"
      security_groups      = ["${aws_security_group.allow-ssh.id}"]
    }

    resource "aws_autoscaling_group" "example-autoscaling" {
      name                 = "example-autoscaling"
      vpc_zone_identifier  = ["${aws_subnet.main-public-1.id}", "${aws_subnet.main-public-2.id}"]
      launch_configuration = "${aws_launch_configuration.example-launchconfig.name}"
      min_size             = 1
      max_size             = 2
      health_check_grace_period = 300   # health checks can also be done with load balancers if available
      health_check_type = "EC2"
      force_delete = true     # instances that are kicked out are deleted

      tag {
          key = "Name"
          value = "ec2 instance"
          propagate_at_launch = true
      }
    }

Below are policies and alarms to scale based on stated thresholds:

    # scale up alarm

    resource "aws_autoscaling_policy" "example-cpu-policy" {
      name                   = "example-cpu-policy"
      autoscaling_group_name = "${aws_autoscaling_group.example-autoscaling.name}"
      adjustment_type        = "ChangeInCapacity"
      scaling_adjustment     = "1"
      cooldown               = "300"
      policy_type            = "SimpleScaling"
    }

    resource "aws_cloudwatch_metric_alarm" "example-cpu-alarm" {
      alarm_name          = "example-cpu-alarm"
      alarm_description   = "example-cpu-alarm"
      comparison_operator = "GreaterThanOrEqualToThreshold"
      evaluation_periods  = "2"     # average of 2 eval periods is evaluated for threshold
      metric_name         = "CPUUtilization"
      namespace           = "AWS/EC2"
      period              = "120"
      statistic           = "Average"
      threshold           = "30"

      dimensions = {
        "AutoScalingGroupName" = "${aws_autoscaling_group.example-autoscaling.name}"
      }

      actions_enabled = true
      alarm_actions   = ["${aws_autoscaling_policy.example-cpu-policy.arn}"]
    }

    # scale down alarm
    resource "aws_autoscaling_policy" "example-cpu-policy-scaledown" {
      name                   = "example-cpu-policy-scaledown"
      autoscaling_group_name = "${aws_autoscaling_group.example-autoscaling.name}"
      adjustment_type        = "ChangeInCapacity"
      scaling_adjustment     = "-1"
      cooldown               = "300"
      policy_type            = "SimpleScaling"
    }

    resource "aws_cloudwatch_metric_alarm" "example-cpu-alarm-scaledown" {
      alarm_name          = "example-cpu-alarm-scaledown"
      alarm_description   = "example-cpu-alarm-scaledown"
      comparison_operator = "LessThanOrEqualToThreshold"
      evaluation_periods  = "2"     # average of 2 eval periods determines threshold
      metric_name         = "CPUUtilization"
      namespace           = "AWS/EC2"
      period              = "120"
      statistic           = "Average"
      threshold           = "5"

      dimensions = {
        "AutoScalingGroupName" = "${aws_autoscaling_group.example-autoscaling.name}"
      }

      actions_enabled = true
      alarm_actions   = ["${aws_autoscaling_policy.example-cpu-policy-scaledown.arn}"]
    }

For notifications such as email when autoscaling is evoked, an SNS topic is required. Here is an example of an SNS resource:

    resource "aws_sns_topic" "example-sns" {
      name         = "sg-sns"
      display_name = "example ASG SNS topic"
    } # email subscription is currently unsupported in terraform and can be done using the AWS Web Console

And here is another resource to attach the topic to the autoscaling group:

    resource "aws_autoscaling_notification" "example-notify" {
      group_names = ["${aws_autoscaling_group.example-autoscaling.name}"]
      topic_arn     = "${aws_sns_topic.example-sns.arn}"
      notifications  = [
        "autoscaling:EC2_INSTANCE_LAUNCH",
        "autoscaling:EC2_INSTANCE_TERMINATE",
        "autoscaling:EC2_INSTANCE_LAUNCH_ERROR"
      ]
    }

I applied the directory and all was created. Next I ran `stress` on the instance to spin the CPU and put the instance in alarm. The autoscaling group created a second instance at that point.

When the 5 min window for `stress` expired, the CPU wound down and the instance was terminated. All works.
**NOTE:** Not sure how to set an output for an instance started from a launch config. Will look into it.

### Elastic Load Balancers

This will be about load balancing autoscaled instances. The **ELB** automatically distributes incoming traffic among multiple instances.
- the ELB itself scales when traffic increases
- the ELB will healthcheck your instances
- if an instance fails healthcheck, the ELB will stop sending traffic to it
- if new instances are added via autoscaling, the ELB will automatically add them and start health checking them

The ELB can be used as an SSL terminator
- it can take the encryption load off of the instances
  - all traffic between the ELB and instances will then be unencrypted
- AWS can manage the certs for you

- ELBs can be spread across multiple availability zones for higher fault tolerance
- in general an ELB provides higher fault tolerance for apps
- an ELB is similar to an nginx/haproxy, it is provided as a service by AWS.

There are 2 types of load balancers on AWS
- the classic load balancer **(ELB)**
  - routes traffic based on network info like forwarding incoming port 80 to 8080 on the instance

- the Application Load Balancer **(ALB)**
  - routes traffic at the application level, like /api and /website to two different instances

#### Defining the ELB with Terraform

Creating an ELB load balancer for the autoscaling group requires 4 actions:

1. Add a new security group resource for the ELB:
```
resource "aws_security_group" "elb-securitygroup" {
  vpc_id = "${aws_vpc.main.id}"
  name = "elb"
  description = "security group for load balancer"
  egress {
      from_port = 0
      to_port = 0
      protocol = "-1"
      cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
      from_port = 80
      to_port = 80
      protocol = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
  }
  tags {
    Name = "elb"
  }
}
```
2. Add a block to the instance security group to allow port 80 ingress from the load balancer:
```
resource "aws_security_group" "allow-ssh" {
  vpc_id = "${aws_vpc.main.id}"
  name = "allow-ssh"
  description = "security group that allows ssh and all egress traffic"
  egress {
      from_port = 0
      to_port = 0
      protocol = "-1"
      cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
      from_port = 22
      to_port = 22
      protocol = "tcp"
      cidr_blocks = ["136.49.228.197/32"]
  }
  ingress {
      from_port = 80
      to_port = 80
      protocol = "tcp"
      security_groups = ["${aws_security_group.elb-securitygroup.id}"]
  }
tags {
    Name = "allow-ssh"
  }
}
resource "aws_security_group" "elb-securitygroup" {
  vpc_id = "${aws_vpc.main.id}"
  name = "elb"
  description = "security group for load balancer"
  egress {
      from_port = 0
      to_port = 0
      protocol = "-1"
      cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
      from_port = 80
      to_port = 80
      protocol = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
  }
  tags {
    Name = "elb"
  }
}
```

3. Add 2 new variables to the autoscaling group resource:
```
health_check_type = "ELB"
load_balancers = ["${aws_elb.my-elb.name}"]
```
4. Create  the ELB resource:
```
resource "aws_elb" "my-elb" {
  name = "my-elb"
  subnets = ["${aws_subnet.main-public-1.id}", "${aws_subnet.main-public-2.id}"]
  security_groups = ["${aws_security_group.elb-securitygroup.id}"]
 listener {
    instance_port = 80
    instance_protocol = "http"    # take incoming traffic on port 80 and send to port 80 on instances
    lb_port = 80
    lb_protocol = "http"
  }
  health_check {
    healthy_threshold = 2       # pass or fail is averaged across 2 checks
    unhealthy_threshold = 2
    timeout = 3           # for health check wait 3 sec for response from port 80 every 30 sec
    target = "HTTP:80/"
    interval = 30
  }

  cross_zone_load_balancing = true    # allow traffic to be forwarded to a different subnet
  connection_draining = true
  connection_draining_timeout = 400   # wait 400 sec for connections to end before terminating any instance
  tags {
    Name = "my-elb"
  }
}
```
I deployed the stack without errors, but the userdata script is not correct to populate the webroot on the instances.

Possibly related, the health checks fail on both instances, causing them to repeatedly get recycled in the autoscaling group. Possibly the lifecycle variable in `autoscaling.tf`. It is present in the directory, but not on the demo video. Will investigate further.

**UPDATE:** The health checks failed because I overlooked a needed modification to the **allow-ssh** security group. It needed port 80 added with ingress coming from the load balancer. After adding that all works fine now.

### Application Load Balancer (ALB)

Application Load Balancers route traffic to instances at the application level, rather than at the network level. This is called **rule based load balancing**.

For example, the ELB used in the previous example forwards incoming port 80 packets to a designated port on the instances behind it. With an ALB, traffic coming in on port 80 could _still_ be routed differently based on _rules_ defined for the ALB. In this way you can have a single website serviced by multiple instances, with each instance doing a different function of the website.

To setup an ALB, you first create the ALB resource and define some general settings:

![](alb1.png)

Next, define a _target group_ resource, which will direct traffic to instances that are attached to it:

![](alb2.png)

Here is an _attachment_ resource, which attaches an instance to a target group. Create multiple attachment resources to attach other instances to the same or other target groups:

![](./alb3.png)

Next we need _listener_ resources to tell the ALB what ports to listen on, plus _where_ and _how_ to deal with packets that match the incoming port. Each port needs a separate listener.

In the example below, the **default_action** block always matches, unless other rules are specified:

![](alb4.png)

Here is an _alb listener rule_ to further route incoming port 80 traffic based on a _condition_ block which matches if the URL contains _/static_:

![](alb5.png)

The above example illustrates how instances can service different functions of the same website completely independent of each other. They can scale parts of the web service in a fine tuned way.

There will be more on ALBs later in the course.

### Elastic Beanstalk

**Elastic Beanstalk** is an AWS **Platform as a Service (PaaS)** offering. The instances run Amazon Linux. While you are still responsible for the instances themselves, AWS will help with OS updates. They can be applied manually or automatically.

Elastic Beanstalk can also handle app scaling for you by managing the load balancers and autoscaling groups.

You can schedule scaling events or use metrics to scale.

Elastic Beanstalk is similar to **Heroku** except that Heroku manages instances and all.

You can have your app running with just a few clicks on the web console. Or you can create an _elasticbeanstalk_ resource with Terraform.

Supported platforms are:
- PHP
- Jave SE, Tomcat
- .NET on Windows with IIS
- node.js
- python
- ruby
- go
- Docker, either a single container or multiple containers using ECS

After deploying an Elastic Beanstalk environment, you will get a hostname as an endpoint, then you can use Route53 to point your domain to it.

Once the beanstalk is running, you can deploy your app using the _EB command line utility_ which can be downloaded from AWS.

#### Launch an Elastic Beanstalk using Terraform

For this demo we will host a PHP application using Elastic Beanstalk.

Steps to build the app:

1. Create `elasticbeanstalk.tf` to create the _application_ and _environment_ resources, along with setting several environment variables using _setting_ blocks:

```
resource "aws_elastic_beanstalk_application" "app" {      #create app resource
  name = "app"
  description = "app"
}

resource "aws_elastic_beanstalk_environment" "app-prod" {   #the environment for the app to run in
  name = "app-prod"
  application = "${aws_elastic_beanstalk_application.app.name}"
  solution_stack_name = "64bit Amazon Linux 2016.09 v2.3.0 running PHP 7.0"
  setting {
    namespace = "aws:ec2:vpc"
    name      = "VPCId"                 # env settings, some are just overrides for defaults
    value     = "${aws_vpc.main.id}"
  }
  setting {
    namespace = "aws:ec2:vpc"
    name = "Subnets"
    value = "${aws_subnet.main-private-1.id},${aws_subnet.main-private-2.id}"
  }
  setting {
    namespace = "aws:ec2:vpc"
    name = "AssociatePublicIpAddress"
    value = "false"
  }
  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name = "IamInstanceProfile"
    value = "${aws_iam_instance_profile.app-ec2-role.name}"
  }
  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name = "SecurityGroups"
    value = "${aws_security_group.app-prod.id}"
  }
  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name = "EC2KeyName"
    value = "${aws_key_pair.mykeypair.id}"
  }
  setting {
    namespace = "aws:autoscaling:launchconfiguration"
    name = "InstanceType"
    value = "t2.micro"
  }
  setting {
    namespace = "aws:elasticbeanstalk:environment"
    name = "ServiceRole"
    value = "${aws_iam_role.elasticbeanstalk-service-role.name}"
  }
  setting {
    namespace = "aws:ec2:vpc"
    name = "ELBScheme"
    value = "public"
  }
  setting {
    namespace = "aws:ec2:vpc"
    name = "ELBSubnets"
    value = "${aws_subnet.main-public-1.id},${aws_subnet.main-public-2.id}"
  }
  setting {
    namespace = "aws:elb:loadbalancer"
    name = "CrossZone"
    value = "true"
  }
  setting {
    namespace = "aws:elasticbeanstalk:command"
    name = "BatchSize"
    value = "30"
  }
  setting {
    namespace = "aws:elasticbeanstalk:command"
    name = "BatchSizeType"
    value = "Percentage"
  }
  setting {
    namespace = "aws:autoscaling:asg"
    name = "Availability Zones"
    value = "Any 2"
  }
  setting {
    namespace = "aws:autoscaling:asg"
    name = "MinSize"
    value = "1"
  }
  setting {
    namespace = "aws:autoscaling:updatepolicy:rollingupdate"
    name = "RollingUpdateType"
    value = "Health"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name = "RDS_USERNAME"
    value = "${var.RDS_USERNAME}"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name = "RDS_PASSWORD"
    value = "${var.RDS_PASSWORD}"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name = "RDS_DATABASE"
    value = "mydb"
    value = "${aws_db_instance.mariadb.name}"
  }
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name = "RDS_HOSTNAME"
    value = "${aws_db_instance.mariadb.endpoint}"
  }
}
```

2. Create an `iam.tf` file to define some roles and attach existing AWS policies to the roles:

```
# iam roles
resource "aws_iam_role" "app-ec2-role" {
    name = "app-ec2-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "ec2.amazonaws.com"
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}
resource "aws_iam_instance_profile" "app-ec2-role" {
    name = "app-ec2-role"
    role = "${aws_iam_role.app-ec2-role.name}"
}

# service
resource "aws_iam_role" "elasticbeanstalk-service-role" {
    name = "elasticbeanstalk-service-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "elasticbeanstalk.amazonaws.com"
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}

# policies
resource "aws_iam_policy_attachment" "app-attach1" {
    name = "app-attach1"
    roles = ["${aws_iam_role.app-ec2-role.name}"]
    policy_arn = "arn:aws:iam::aws:policy/AWSElasticBeanstalkWebTier"
}
resource "aws_iam_policy_attachment" "app-attach2" {
    name = "app-attach2"
    roles = ["${aws_iam_role.app-ec2-role.name}"]
    policy_arn = "arn:aws:iam::aws:policy/AWSElasticBeanstalkMulticontainerDocker"
}
resource "aws_iam_policy_attachment" "app-attach3" {
    name = "app-attach3"
    roles = ["${aws_iam_role.app-ec2-role.name}"]
    policy_arn = "arn:aws:iam::aws:policy/AWSElasticBeanstalkWorkerTier"
}
resource "aws_iam_policy_attachment" "app-attach4" {
    name = "app-attach4"
    roles = ["${aws_iam_role.elasticbeanstalk-service-role.name}"]
    policy_arn = "arn:aws:iam::aws:policy/service-role/AWSElasticBeanstalkEnhancedHealth"
}
```

3. Create 2 security groups, one to SSH access the instances running the app and one for mariadb access for the app itself:

```
resource "aws_security_group" "app-prod" {
  vpc_id = "${aws_vpc.main.id}"
  name = "application - production"
  description = "security group for my app"
  egress {
      from_port = 0
      to_port = 0
      protocol = "-1"
      cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
      from_port = 22
      to_port = 22
      protocol = "tcp"
      cidr_blocks = ["0.0.0.0/0"]
  }


  tags {
    Name = "myinstance"
  }
}
resource "aws_security_group" "allow-mariadb" {
  vpc_id = "${aws_vpc.main.id}"
  name = "allow-mariadb"
  description = "allow-mariadb"
  ingress {
      from_port = 3306
      to_port = 3306
      protocol = "tcp"
      security_groups = ["${aws_security_group.app-prod.id}"]              # allowing access from our example instance
  }
  egress {
      from_port = 0
      to_port = 0
      protocol = "-1"
      cidr_blocks = ["0.0.0.0/0"]
      self = true
  }
  tags {
    Name = "allow-mariadb"
  }
}
```

4. Create 2 new variables for the **RDS_USERNAME** and **RDS_PASSWORD**

Didn't quite work, but I did use an incorrect solution stack. Will try again later after correcting it.

## Section 6: Advanced Terraform Usage

### Interpolation

Interpolation means placing other values into an expression using **${}** syntax. The values come from variables or othe blocks declared in Terraform.

You can interpolate:
- simple math functions
- other variables
- conditionals like **ef-else**

They have already been used in this course:
- variables - **${var.VARIABLE_NAME}** refers to a variable
- resources - **${aws_instance.name.id}** _(type.resource-name.attr)_
- data source - **${data.tamplate_file.name.rendered}** _(data.type.resource-name.attr)_

#### Variable interpolation types

Name  | Syntax  | Example
------|---------|--------
String variable | _var.name_  | **${var.SOMETHING}**
Map variable  | _var.MAP["key"]_  | **${var.AMIS["us-east-1"]}**
    ""          |    ""     | **${lookup(var.AMIS,var.AWS_REGION)}**
List variable   | _var.LIST,var.LIST[i]_  | **${var.subnets[i]}**
""            |         ""        |   **{join(",",var.subnets)}**

#### Other various interpolations:

![](./interpolation1.png)

#### Math interpolation

![](./interpolation2.png)

### Conditionals

Conditonals evaluate **true/false** and perform an action based on the condition. The format is:

**condition ? <do if true> : <do if false>**

![](./condition1.png)

The above determines the number (_count_) of instances to launch based on the environment being production.

The conditional operators available:

![](./operators.png)

#### demo using interpolations, conditionals, and the VPC module

**steps to create**

1. For this demo we will be using a vpc module from Terraform, rather than creating our own vpc resource. We will use the module twice to create a **prod** and **dev** VPC:

```
module "vpc-prod" {
  source = "terraform-aws-modules/vpc/aws"

  name = "vpc-prod"
  cidr = "10.0.0.0/16"

  azs             = ["${var.AWS_REGION}a", "${var.AWS_REGION}b", "${var.AWS_REGION}c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets  = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]

  enable_nat_gateway = false
  enable_vpn_gateway = false

  tags = {
    Terraform   = "true"
    Environment = "prod"
  }
}

module "vpc-dev" {
  source = "terraform-aws-modules/vpc/aws"

  name = "vpc-dev"
  cidr = "10.0.0.0/16"

  azs             = ["${var.AWS_REGION}a", "${var.AWS_REGION}b", "${var.AWS_REGION}c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets  = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]

  enable_nat_gateway = false
  enable_vpn_gateway = false

  tags = {
    Terraform   = "true"
    Environment = "dev"
  }
}
```

2. Create 2 security groups, one for prod, one for dev. Each allowing a simple SSH connection:

```
resource "aws_security_group" "allow-ssh-prod" {
  vpc_id      = "${module.vpc-prod.vpc_id}"
  name        = "allow-ssh"
  description = "security group that allows ssh and all egress traffic"

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags {
    Name = "allow-ssh"
  }
}

resource "aws_security_group" "allow-ssh-dev" {
  vpc_id      = "${module.vpc-dev.vpc_id}"
  name        = "allow-ssh"
  description = "security group that allows ssh and all egress traffic"

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags {
    Name = "allow-ssh"
  }
}
```

3. Add a line to the `vars.tf` file making the default environment **prod**:

    variable "ENV" {
      default = "prod"
    }
**NOTE:** The course has the private key in a variable. Not sure why yet, will try without first.

4. Create an `instance.tf` file, which uses a _data_ resource (datasource) of type **aws_ami** to gather and filter an AMI based on _name_ and _virtualization type_. It then uses conditionals for what **subnet** and **security group** to assign the instance to:

```
data "aws_ami" "ubuntu" {
  most_recent = true

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-trusty-14.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = ["099720109477"] # Canonical
}

resource "aws_instance" "example" {
  ami           = "${data.aws_ami.ubuntu.id}"
  instance_type = "t2.micro"

  # the VPC subnet
  subnet_id = "${var.ENV == "prod" ? module.vpc-prod.public_subnets[0] : module.vpc-dev.public_subnets[0] }"

  # the security group
  vpc_security_group_ids = ["${var.ENV == "prod" ? aws_security_group.allow-ssh-prod.id : aws_security_group.allow-ssh-dev.id }"]

  # the public SSH key
  key_name = "${aws_key_pair.mykeypair.key_name}"
}
```

First we run `terraform init` which in this case will first download the VPC module, then the plugin for the provider as usual.

The apply will place the instance in the _prod_ VPC as that is the default set in `vars.tf` earlier.

If you want to override the default and specify the env at apply time, use `terraform apply -var ENV=dev`.

### Built-in Functions

To call a function in Terraform the syntax is **name(arg1,arg2,...)** and the function call is wrapped in **${..}, for example:

`${file("mykey.pub")}` to call the _file_ function with argument **mykey.pub**. This would read in the contents of the _mykey.pub_ file.

 Some most commonly used functions:

 ![](./functions1.png)

 ![](./functions2.png)

 ![](./functions3.png)

 ![](./functions4.png)

 ![](./functions5.png)

Terraform has the `terraform console` command which gives you a console to experiment with your interpolations and functions when creating them. It has access to the resources in state, if you have any.

The instructor ran thru several examples of using list, map, slice, index and other functions. This is available anytime so I will reference when needed, if needed. It is pretty straightforward.

### Terraform Project Structure

In production environments, you need a good project structure.

Ideally you should separate dev and prod completely:
- you can always test changes in dev first to catch mistakes
- for complete isolation, use muptiple AWS accounts, one for dev, one for prod, one for billing
- splitting out terraform projects reduces the number of resources in any one apply

The below illustration demonstrates how you would write your resources as modules in a directory, and use those modules in separate directories for dev or prod. Finally, you should apply the resources using separate accounts:

![](structure1.png)

On AWS there is an _organizations_ page where you can set all this up.

#### Example project structure

This project structure has 3 subdirectories:
- modules - contains 2 modules; **instance.tf** and **vpc.tf** to create the instance and the vpc
- dev - contains **dev.tf** to instantiate the dev environment, **provider.tf** and **vars.tf**
- prod - contains **prod.tf** to instantiate the prod environment, **provider.tf** and **vars.tf**

**NOTE:** Within the **dev** and **Prod** directories I have also included the **.tfvars** so I can use the existing key in my home directory.

** the vpc module**

This module creates the vpc based on the standard vpc module from terraform. I do have to pin the version since I am using terraform 11. The main thing to note in these module files is what variables are declared and how they are populated.

Here is the `vpc.tf` from the **modules/vpc** directory:
```

variable "ENV" {}         # this value comes from the instantiation dev.tf or prod.tf
variable "AWS_REGION" {}    # the .tfvars file forces this

module "main-vpc" {
  source = "terraform-aws-modules/vpc/aws"
  version = "1.67.0"      # pin to version for terraform 11

  name = "vpc-${var.ENV}"
  cidr = "10.0.0.0/16"

  azs             = ["${var.AWS_REGION}a", "${var.AWS_REGION}b", "${var.AWS_REGION}c"]
  private_subnets = ["10.0.1.0/24", "10.0.2.0/24", "10.0.3.0/24"]
  public_subnets  = ["10.0.101.0/24", "10.0.102.0/24", "10.0.103.0/24"]

  enable_nat_gateway = false
  enable_vpn_gateway = false

  tags = {
    Terraform   = "true"
    Environment = "${var.ENV}"
  }
}

output "vpc_id" {         # generated by AWS, and output here for use by dev.tf or prod.tf
  description = "The ID of the VPC"
  value       = "${module.main-vpc.vpc_id}"
}

output "private_subnets" {
  description = "List of IDs of private subnets"
  value       = ["${module.main-vpc.private_subnets}"]
}

output "public_subnets" {       # the instance.tf needs this to place the instance
  description = "List of IDs of public subnets"
  value       = ["${module.main-vpc.public_subnets}"]
}
```

**the instance module**

This module creates the instances that run in the vpc.

Here is the `instance.tf` file from **modules/instances**. The variables in the beginning are all supplied elsewhere. The datasource provides the AMI to use. Next are 3 resource blocks:
- the instance itself
- the security group
- the keypair

**NOTE:** To use my existing keypair for this module I had to place a new set of `vars.tf` and `terraform.tfvars` files in the **modules/instances** directory, containing only the **PATH_TO_PUBLIC_KEY** variable.

I then had to remove the variable block from the top of `instance.tf`. The module cannot be passed the **PATH_TO_PUBLIC_KEY** variable needed to create the keypair. At least not from the `prod.tf` file. Maybe on the command line, but that to me is messy.

```
variable "ENV" {}

variable "INSTANCE_TYPE" {
  default = "t2.micro"
}

variable "PUBLIC_SUBNETS" {
  type = "list"
}

variable "VPC_ID" {}

data "aws_ami" "ubuntu" {
  most_recent = true

  filter {
    name   = "name"
    values = ["ubuntu/images/hvm-ssd/ubuntu-trusty-14.04-amd64-server-*"]
  }

  filter {
    name   = "virtualization-type"
    values = ["hvm"]
  }

  owners = ["099720109477"] # Canonical
}

resource "aws_instance" "instance" {
  ami           = "${data.aws_ami.ubuntu.id}"   # supplied by above datasource
  instance_type = "${var.INSTANCE_TYPE}"        # supplied by the default for variable above

  # the VPC subnet
  subnet_id = "${var.PUBLIC_SUBNETS[0]}"    # use the first public subnet from the vpc module

  # the security group
  vpc_security_group_ids = ["${aws_security_group.allow-ssh.id}"] # supplied below

  # the public SSH key
  key_name = "${aws_key_pair.mykeypair.key_name}"   # supplied below

  tags {
    Name         = "instance-${var.ENV}"
    Environmnent = "${var.ENV}"
  }
}

resource "aws_security_group" "allow-ssh" {
  vpc_id      = "${var.VPC_ID}"
  name        = "allow-ssh-${var.ENV}"
  description = "security group that allows ssh and all egress traffic"

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags {
    Name         = "allow-ssh"
    Environmnent = "${var.ENV}"
  }
}

resource "aws_key_pair" "mykeypair" {
  key_name   = "mykeypair-${var.ENV}"
  public_key = "${var.PATH_TO_PUBLIC_KEY}")}"
}
```

Next is the instantiation file for prod, `prod.tf` from the **prod** directory:

```
module "main-vpc" {     # call the vpc module to build the vpc
  source = "../modules/vpc"
  ENV = "prod"
  AWS_REGION = "${var.AWS_REGION}"
}

module "instances" {      # call the instances module to build the instance
  source = "../modules/instances"
  ENV = "prod"
  VPC_ID = "${module.main-vpc.vpc_id}"
  PUBLIC_SUBNETS = ["${module.main-vpc.public_subnets}"]    # this will be overridden by the instance module
}
```

Finally here is the instantiation file for dev, `dev.tf` from the **dev** directory, it works the same as the prod, only difference is the environment will be dev:

```
module "main-vpc" {
  source = "../modules/vpc"
  ENV = "dev"
  AWS_REGION = "${var.AWS_REGION}"
}

module "instances" {
  source = "../modules/instances"
  ENV = "dev"
  VPC_ID = "${module.main-vpc.vpc_id}"
  PUBLIC_SUBNETS = ["${module.main-vpc.public_subnets}"]
}
```

With the above mentioned mods in place the process works.

## Section 7: Packer

Packer is a tool that can build AMIs using templates.

Instead of installing software on an instance at boot, you can preinstall the software on your own instance. Some obvious advantages include faster boot times and easier horizontal scaling since the AMIs can just boot and go.

Packer can also be used to prebuild AMIs for running docker containers.

Although primarily packer is a tool for building a custom AMI and storing it on AWS, the next demo uses a script to first build the AMI, then run terraform to deploy an instance based off the new AMI onto a VPC.

### deploy a custom AMI with Packer

In the **packer** directory, there are the files necessary to first build a custom AMI using Packer, then deploy an instance derived from that custom AMI. The files needed are:
- the usual **vars.tf**, **.tfvars** files
- a **vpc.tf** to create the generic VPC not from module
- a **securitygroup.tf** to create the security group _allow-ssh_ for the instance
- the **key.tf** file to create the key resource
- an **instance.tf** file to create an instance resource from the newly minted AMI

Plus these new files:

The packer template file, a JSON file `packer-example.json`:

```
{
  "variables": {
    "aws_access_key": "",
    "aws_secret_key": ""
  },
  "builders": [{
    "type": "amazon-ebs",
    "access_key": "{{user `aws_access_key`}}",
    "secret_key": "{{user `aws_secret_key`}}",
    "region": "",
    "source_ami": "ami-0111e8c43a763eb71",
    "instance_type": "t2.micro",
    "ssh_username": "ubuntu",
    "ami_name": "packer-example {{timestamp}}"
  }],
  "provisioners": [{
    "type": "shell",
    "scripts": [  "scripts/install_software.sh"   ],
    "execute_command": "{{ .Vars }} sudo -E sh '{{ .Path }}'",
    "pause_before": "10s"
  }]
}
```

The **execute command** line explained:
- **{{ .Vars}}** - use any environment variables defined earlier
- **{{ .Path}}** - the path of the script, defined above

Next, the **install_software** script that executes on the new AMI:

```
#!/bin/bash
apt-get update
apt-get install -y nginx docker.io vim lvm2
```

Finally, a bash script to launch the whole process in one shot. It does the following:
-  runs packer to build the new AMI
-  parses the output from packer thru `awk` to extract the new AMI ID
-  writes the new AMI ID to a file in the project directory called `amivar.tf`
  -  This creates the AMI variable needed by `instance.tf` later

- runs terraform init
- runs terraform apply

Here is the script:

```
#!/bin/bash
ARTIFACT=`packer build -machine-readable packer-example.json |awk -F, '$0 ~/artifact,0,id/ {print $6}'`
AMI_ID=`echo $ARTIFACT | cut -d ':' -f2`
echo 'variable "AMI_ID" { default = "'${AMI_ID}'" }' > amivar.tf
terraform init
terraform plan
```

After running the launch script, a new instance is running on the VPC with nginx, docker, vim, and lvm2 preinstalled. And of course the custom AMI is listed on AWS for later use.

### Terraform with Packer and Jenkins

This section is about using Terraform and Packer in the software development lifecycle.

As illustrated in the image below, Jenkins provides the build and test phase, while Terraform and Packer can be used for the release cycle:

![](jenkins1.png)

The workflow is explained in the next image:
- jenkins will clone the app repo and build the app
- jenkins will run `packer build` to create a new AMI with the app on it and push it to AWS
- jenkins will clone the terraform repo
- jenkins will run terraform apply, creating infrastructure and running the new AMI on it

![](jenkins2.png)

#### Build a node.js app and deploy with Jenkins, Terraform and Packer

 Steps to deploy:

 1. Create `instance.tf` to deploy the jenkins instance, ebs volume and attachment, and the node.js instance:

 ```
 resource "aws_instance" "jenkins-instance" {
   ami           = "${lookup(var.AMIS, var.AWS_REGION)}"
   instance_type = "t2.small"

   # the VPC subnet
   subnet_id = "${aws_subnet.main-public-1.id}"

   # the security group
   vpc_security_group_ids = ["${aws_security_group.jenkins-securitygroup.id}"]

   # the public SSH key
   key_name = "${aws_key_pair.mykeypair.key_name}"

   # user data
   user_data = "${data.template_cloudinit_config.cloudinit-jenkins.rendered}"

 }

 resource "aws_ebs_volume" "jenkins-data" {
     availability_zone = "us-east-1a"
     size = 20
     type = "gp2"
     tags {
         Name = "jenkins-data"
     }
 }

 resource "aws_volume_attachment" "jenkins-data-attachment" {
   device_name = "${var.INSTANCE_DEVICE_NAME}"
   volume_id = "${aws_ebs_volume.jenkins-data.id}"
   instance_id = "${aws_instance.jenkins-instance.id}"
   skip_destroy = true
 }

 resource "aws_instance" "app-instance" {
   count = "${var.APP_INSTANCE_COUNT}"    # use an instance count variable to launch multiple instances
   ami = "${var.APP_INSTANCE_AMI}"
   instance_type = "t2.micro"

   # the VPC subnet
   subnet_id = "${aws_subnet.main-public-1.id}"

   # the security group
   vpc_security_group_ids = ["${aws_security_group.app-securitygroup.id}"]

   # the public SSH key
   key_name = "${aws_key_pair.mykeypair.key_name}"
 }
```

2. Create `cloudinit.tf` to apply the `jenkins-init.sh` which installs jenkins on the instance:

```
data "template_file" "jenkins-init" {
  template = "${file("scripts/jenkins-init.sh")}"   # create datasource to be rendered and passed to template below
  vars {
    DEVICE = "${var.INSTANCE_DEVICE_NAME}"
    JENKINS_VERSION = "${var.JENKINS_VERSION}"
    TERRAFORM_VERSION = "${var.TERRAFORM_VERSION}"
  }
}
data "template_cloudinit_config" "cloudinit-jenkins" {      # create cloudinit template which instance.tf will apply

  gzip = false
  base64_encode = false

  part {
    content_type = "text/x-shellscript"
    content      = "${data.template_file.jenkins-init.rendered}"
  }

}
```

3. Create `jenkins-init.sh` to install jenkins on the jenkins instance:

```
#!/bin/bash

# volume setup
vgchange -ay

DEVICE_FS=`blkid -o value -s TYPE ${DEVICE}`
if [ "`echo -n $DEVICE_FS`" == "" ] ; then
  # wait for the device to be attached
  DEVICENAME=`echo "${DEVICE}" | awk -F '/' '{print $3}'`
  DEVICEEXISTS=''
  while [[ -z $DEVICEEXISTS ]]; do
    echo "checking $DEVICENAME"
    DEVICEEXISTS=`lsblk |grep "$DEVICENAME" |wc -l`
    if [[ $DEVICEEXISTS != "1" ]]; then
      sleep 15
    fi
  done
  pvcreate ${DEVICE}
  vgcreate data ${DEVICE}
  lvcreate --name volume1 -l 100%FREE data
  mkfs.ext4 /dev/data/volume1
fi
mkdir -p /var/lib/jenkins
echo '/dev/data/volume1 /var/lib/jenkins ext4 defaults 0 0' >> /etc/fstab
mount /var/lib/jenkins

# jenkins repository
wget -q -O - https://pkg.jenkins.io/debian-stable/jenkins.io.key | sudo apt-key add -
echo "deb http://pkg.jenkins.io/debian-stable binary/" >> /etc/apt/sources.list
apt-get update

# install dependencies
apt-get install -y python3 openjdk-8-jre
update-java-alternatives --set java-1.8.0-openjdk-amd64
# install jenkins
apt-get install -y jenkins=${JENKINS_VERSION} unzip

# install pip
wget -q https://bootstrap.pypa.io/get-pip.py
python3 get-pip.py
rm -f get-pip.py
# install awscli
pip install awscli

# install terraform
wget -q https://releases.hashicorp.com/terraform/${TERRAFORM_VERSION}/terraform_${TERRAFORM_VERSION}_linux_amd64.zip \
&& unzip -o terraform_${TERRAFORM_VERSION}_linux_amd64.zip -d /usr/local/bin \
&& rm terraform_${TERRAFORM_VERSION}_linux_amd64.zip

# install packer
cd /usr/local/bin
wget -q https://releases.hashicorp.com/packer/0.10.2/packer_0.10.2_linux_amd64.zip
unzip packer_0.10.2_linux_amd64.zip
# clean up
apt-get clean
rm terraform_0.7.7_linux_amd64.zip
rm packer_0.10.2_linux_amd64.zip
```

4. Create `output.tf` to output the IPs for the jenkins and app instances:

```
output "jenkins-ip" {
  value = ["${aws_instance.jenkins-instance.*.public_ip}"]
}
output "app-ip" {
  value = ["${aws_instance.app-instance.*.public_ip}"]
}
```

5. Create `s3.tf` to create the s3 bucket containing terraform state:

```
resource "aws_s3_bucket" "terraform-state" {
    bucket = "nikkyrron-state"
    acl = "private"

    tags {
        Name = "Terraform state"
    }
}
```

6. The `vars.tf` has new variables:

```
variable "AWS_REGION" {}
variable "PATH_TO_PRIVATE_KEY" {}
variable "PATH_TO_PUBLIC_KEY" {}
variable "AMIS" {
  type = "map"
  default = {
    us-east-1 = "ami-13be557e"
    us-east-2 = "ami-06b94666"
    us-west-1 = "ami-844e0bf7"
  }
}
variable "INSTANCE_DEVICE_NAME" {
  default = "/dev/xvdh"
}
variable "JENKINS_VERSION" {
  default = "2.121.2"
}
variable "TERRAFORM_VERSION" {
  default = "0.11.14"
}

variable "APP_INSTANCE_COUNT" {
  default = "0"
}
```

**the process**

1. Comment lines in `backend.tf` the bucket has to be created first.
2. create mykey keypair.
3. remove pub and priv key from .tfvars
4. run `terraform init`
5. run `terraform apply` to create vpc etc and put jenkins instance online
6. uncomment lines in `backend.tf` and run `terraform init` again to set up backend
7. ssh to jenkins and start the service
8. su to jenkins and run `aws configure` on jenkins to set up credentials
9. go to jenkins GUI and create the packer-demo project
10. run the packer-demo project
11. create the jenkins terraform project
12. run `terraform apply`, it will fail but will checkout the repo
13. run `terraform init` to initialize the backend for jenkins
14. rerun the `terraform apply`, when finished it will output the **app-ip**.
15. go to the **app-ip** in the browser and you will get **"Hello World!"** confirming the app is deployed.

**NOTE:** I had to increase the **pause before** setting in the **packer-demo.json** file on the packer-demo repo before apt-get would install nginx and nodejs from the **deploy.sh** script.

## Section 8: Docker on AWS using ECS and ECR

Below is a diagram of the workflow:

![](./docker1.png)

In the above diagram there is a local and a production environment for running docker images. In the local env, you use the dockerfile and run `docker build`, then you run the docker images with `docker compose`. In this scenario you manage the docker hosts yourself.

In the production scenario, you create dockerfiles on a local workstation with docker installed, then check them in to source control. Then you use jenkins to build the images. Next, jenkins will push the images to the Amazon **EC2 Container Registry** (ECR). Finally, the Amazon **EC2 Container Service** (ECS) will run your containers on 1 or several EC2 powered docker hosts.

The hosts that run the containers are called **EC2 instances**, while the containers themselves are called **tasks**. A _service_ in this scenario is the mechanism that keeps the desired number of healthy tasks running, and relaunches failed ones if need be.

### Build docker image and push it to ECR

The ECR registry will be created using Terraform. The folder is `docker-demo-1`.

Here are the files:

`ecr.tf`:

    resource "aws_ecr_repository" "myapp" {     #create the ecr resource
      name = "myapp"
    }

`output.tf`:

    output "myapp-repository-URL" {               # output the URL for the ECR repo
      value = "${aws_ecr_repository.myapp.repository_url}"
    }

Here are the commands used to build and push the image:

![](./docker2.png)

- **terraform init**
- **terraform apply**

On the Outputs line you will get:

**myapp-repository-URL = 275034288058.dkr.ecr.us-east-1.amazonaws.com/myapp**

Once the ECR registry is created it is time to build the docker image using the **docker-demo** repo.

CD to the repo and run:
`docker build -t 275034288058.dkr.ecr.us-east-1.amazonaws.com/myapp:1 .` This will build it and tag it with the URL of the ECR registry.

Next, to push the image to the registry you first need to login to the registry, much the same as using docker hub:

Unfortunately, when running `aws ecr get-login` the command produced includes a deprecated **-e** flag which causes it to fail. So have to run the command, then remove the **-e none** from near the end of it. It is a flag used to set the email to _none_.

Run `aws ecr get-login`, then copy and paste the resulting command _without_ the **-e none**, then you will see login successful.

Now push the image with `docker push 275034288058.dkr.ecr.us-east-1.amazonaws.com/myapp:1`.

Once the push is done, I went to the console under ECR and I see the image is sitting on ECR.

### Run the image on ECS

This section will use the terraform files in the **docker-demo-2** folder to create the entire infrastructure to store and run docker images using ECR and ECS. It will create an autoscaling group that uses a custom AMI with the ECS agent pre-installed.

**REMINDER:** In AWS speak, a container is called a _task_.

The relevant files:

`ecr.tf` - create the registry, the same as last section.

`ecs.tf` - create the ECS cluster and the autoscaling group:

```
# cluster
resource "aws_ecs_cluster" "example-cluster" {
    name = "example-cluster"
}
resource "aws_launch_configuration" "ecs-example-launchconfig" {    # create instances to run containers
  name_prefix          = "ecs-launchconfig"
  image_id             = "${lookup(var.ECS_AMIS, var.AWS_REGION)}"
  instance_type        = "${var.ECS_INSTANCE_TYPE}"
  key_name             = "${aws_key_pair.mykeypair.key_name}"
  iam_instance_profile = "${aws_iam_instance_profile.ecs-ec2-role.id}"  #instance needs role to have ECS permissions to access images
  security_groups      = ["${aws_security_group.ecs-securitygroup.id}"]
  user_data            = "#!/bin/bash\necho 'ECS_CLUSTER=example-cluster' > /etc/ecs/ecs.config\nstart ecs"   # have new instance join ECS cluster
  lifecycle              { create_before_destroy = true }
}
resource "aws_autoscaling_group" "ecs-example-autoscaling" {
  name                 = "ecs-example-autoscaling"
  vpc_zone_identifier  = ["${aws_subnet.main-public-1.id}", "${aws_subnet.main-public-2.id}"] # instances go in public subnet for SSH ability
  launch_configuration = "${aws_launch_configuration.ecs-example-launchconfig.name}"
  min_size             = 1        # set how many instances (hosts) for running tasks
  max_size             = 1
  tag {
      key = "Name"
      value = "ecs-ec2-container"
      propagate_at_launch = true
  }
}
```

`iam.tf` to create roles for instances to be able to access ECS to run the containers:

```
# ecs ec2 role
resource "aws_iam_role" "ecs-ec2-role" {    # the role for the launch config to attach to EC2 instances
    name = "ecs-ec2-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "ec2.amazonaws.com"
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}
resource "aws_iam_instance_profile" "ecs-ec2-role" {    # this is specified in the launch config to tie it to the role above
    name = "ecs-ec2-role"
    role = "${aws_iam_role.ecs-ec2-role.name}"
}

resource "aws_iam_role" "ecs-consul-server-role" {
    name = "ecs-consul-server-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "ec2.amazonaws.com"
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}

resource "aws_iam_role_policy" "ecs-ec2-role-policy" {    # a custom policy to allow instances to access EC2 and ECS
    name = "ecs-ec2-role-policy"
    role = "${aws_iam_role.ecs-ec2-role.id}"
    policy = <<EOF
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
              "ecs:CreateCluster",
              "ecs:DeregisterContainerInstance",
              "ecs:DiscoverPollEndpoint",
              "ecs:Poll",
              "ecs:RegisterContainerInstance",
              "ecs:StartTelemetrySession",
              "ecs:Submit*",
              "ecs:StartTask",
              "ecr:GetAuthorizationToken",
              "ecr:BatchCheckLayerAvailability",
              "ecr:GetDownloadUrlForLayer",
              "ecr:BatchGetImage",
              "logs:CreateLogStream",
              "logs:PutLogEvents"
            ],
            "Resource": "*"
        },
        {
            "Effect": "Allow",
            "Action": [
                "logs:CreateLogGroup",
                "logs:CreateLogStream",
                "logs:PutLogEvents",
                "logs:DescribeLogStreams"
            ],
            "Resource": [
                "arn:aws:logs:*:*:*"
            ]
        }
    ]
}
EOF
}

# ecs service role
resource "aws_iam_role" "ecs-service-role" {    # create a role, then below tie it to AWS default policy for ecs
    name = "ecs-service-role"
    assume_role_policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": "sts:AssumeRole",
      "Principal": {
        "Service": "ecs.amazonaws.com"      # this line makes this role an ecs service role
      },
      "Effect": "Allow",
      "Sid": ""
    }
  ]
}
EOF
}

resource "aws_iam_policy_attachment" "ecs-service-attach1" {    # this is attached to the service defined in myapp.tf
    name = "ecs-service-attach1"
    roles = ["${aws_iam_role.ecs-service-role.name}"]
    policy_arn = "arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceRole"
}
```

`myapp.tf` to create:
- the task definition, which pulls info from a datasource, with info on how to run the images:
  - which image to run
  - CPU and Memory limits
  - if containers should be linked such as app container to DB container
  - environment variables such as DB credentials
  - any other container definitions

- the service definition, to run a specific amount of containers based on the task definition:
  - a service always runs. if a container stops it will be restarted
  - a service can be scaled
  - an ELB can be in front of a service
  - typically multiple containers run spread over availability zones
  - if a container fails, the ELB will stop traffic to it
  - running multiple containers with an ELB provides HA


```
# app

data "template_file" "myapp-task-definition-template" {   # a datasource that uses a file as a template
  template               = "${file("templates/app.json.tpl")}"
  vars {
    REPOSITORY_URL = "${replace("${aws_ecr_repository.myapp.repository_url}", "https://", "")}" #give template the URL, remove https
  }
}

resource "aws_ecs_task_definition" "myapp-task-definition" {    #create the AWS resource itself using the template above
  family                = "myapp"
  container_definitions = "${data.template_file.myapp-task-definition-template.rendered}"
}

resource "aws_elb" "myapp-elb" {
  name = "myapp-elb"

  listener {
    instance_port = 3000
    instance_protocol = "http"
    lb_port = 80
    lb_protocol = "http"
  }

  health_check {
    healthy_threshold = 3
    unhealthy_threshold = 3
    timeout = 30
    target = "HTTP:3000/"
    interval = 60
  }

  cross_zone_load_balancing = true
  idle_timeout = 400
  connection_draining = true
  connection_draining_timeout = 400

  subnets = ["${aws_subnet.main-public-1.id}","${aws_subnet.main-public-2.id}"]
  security_groups = ["${aws_security_group.myapp-elb-securitygroup.id}"]

  tags {
    Name = "myapp-elb"
  }
}

resource "aws_ecs_service" "myapp-service" {      # run specified amount of containers according to task definition
  name = "myapp"
  cluster = "${aws_ecs_cluster.example-cluster.id}"   # the cluster to run the containers in
  task_definition = "${aws_ecs_task_definition.myapp-task-definition.arn}"    #use the task definition above
  desired_count = 1                                                           #desired count = number of tasks running
  iam_role = "${aws_iam_role.ecs-service-role.arn}"
  depends_on = ["aws_iam_policy_attachment.ecs-service-attach1"]

  load_balancer {   # create load balancer
    elb_name = "${aws_elb.myapp-elb.name}"
    container_name = "myapp"    # point the load balancer at the myapp containers
    container_port = 3000   # forward traffic to port 3000 on containers
  }
  lifecycle { ignore_changes = ["task_definition"] }    # allow changes to task definitions outside terraform, terraform wont revert them
}
```

With all files as they are in the **docker-demo-2** folder, the app is successfully created.

### Add Jenkins to the nodejs project

Use Jenkins to build the docker images, push them to ECR and run them on ECS.

This is the ECS lab with a jenkins instance added in. The folder is **docker-demo-3** and there are some minor differences:

In the `myapp.tf` file, the service resource now has a count, since no service is needed unless jenkins has run a job and launched the app. The default value is **0**, so running terraform initially will not create the service since there will not yet be an app image deployed:

```
resource "aws_ecs_service" "myapp-service" {
  count = "${var.MYAPP_SERVICE_ENABLE}"   # the default set in the myapp-vars.tf is 0
  name = "myapp"
  cluster = "${aws_ecs_cluster.example-cluster.id}"
  task_definition = "${aws_ecs_task_definition.myapp-task-definition.arn}"
  desired_count = 1
  iam_role = "${aws_iam_role.ecs-service-role.arn}"
  depends_on = ["aws_iam_policy_attachment.ecs-service-attach1"]
```

Also in the `myapp.tf` the datasource template used to build the task definition now includes the _version_ of the image to pull and run:

```
data "template_file" "myapp-task-definition-template" {
  template               = "${file("templates/app.json.tpl")}"
  vars {
    REPOSITORY_URL = "${replace("${aws_ecr_repository.myapp.repository_url}", "https://", "")}"
    APP_VERSION = "${var.MYAPP_VERSION}"
  }
}
```

In order to use my dingo _ami-0111e8c43a763eb71_ I had to add the following items:

```
# install dependencies
apt-get update
apt-get install -y openjdk-8-jre
update-java-alternatives --set java-1.8.0-openjdk-amd64
```
```
# install pip
apt-get install -y python3-pip
# install awscli
pip3 install awscli
```
and change the terraform version: `TERRAFORM_VERSION="0.11.14"`

**build steps for this project**

1. comment out `backend.tf` for first run.
2. wipe out **terraform.tfstate and backup** and **.terraform**
3. run `terraform init`
4. run `terraform apply`
5. uncomment `backend.tf` and run `terraform init` again to put backend to s3
6. SSH to jenkins instance and wait for scripts to finish.
7. get root, then `su - jenkins` then run `aws configure`
  - secret ID: `AKIAUACKSF65GPB6OBWU`
  - secret key: `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`
8. as root get jenkins pw `cat /var/lib/jenkins/secrets/initialAdminPassword`
9. go to jenkins GUI and do setup. install `parameterized trigger` plugin
10. create **docker-demo** project:
  - `https://github.com/roninhockley/docker-demo.git`
  - enter below as shell build step (change ecr repo to current):
```
  docker build -t 275034288058.dkr.ecr.us-east-1.amazonaws.com/myapp:${GIT_COMMIT} .
eval "$(aws ecr get-login --no-include-email --region us-east-1)"
docker push 275034288058.dkr.ecr.us-east-1.amazonaws.com/myapp:${GIT_COMMIT}
```
11. create **docker-demo-deploy** project:
  - `https://github.com/roninhockley/terraform-course.git`
  - enter below as shell build step:

```
cd docker-demo-3/
touch mykey
touch mykey.pub
terraform apply -auto-approve -target aws_ecs_service.myapp-service -var MYAPP_SERVICE_ENABLE="1" -var MYAPP_VERSION=${MYAPP_VERSION}
```
  - Project Parameterized: `MYAPP_VERSION`
11. go back to **docker-demo** project config and enter post build step to trigger parameterized build **docker-demo-deploy** only when stable.
  - add predefined parameter: `MYAPP_VERSION=${GIT_COMMIT}`

12. run the **docker-demo** job. It will trigger **docker-demo-deploy** which will fail.
13. on jenkins instance, as user jenkins: `cd jobs/docker-demo-deploy/workspace/docker-demo-3` and run `terraform init` to remote the backend.
14. re-run the `docker-demo` job, this time you should end up with a successful build of `docker-demo-deploy`. At the end of the console output there will a URL for the load balancer. Pull up the URL in a browser and you will see the **Hello World!** indicating the task is running in the ECS cluster.

**NOTE** that when destroying this infrastructure, you will have to manually remove the jenkins EC2 instance. Terraform does not remove it.

When destroy is complete, you have to manually remove the jenkins volume and the bucket, because they are not empty. You also have to manually remove the keypair. Terraform seems to complain when trying to create an existing resource. Need to figure out how to use an existing keypair.

## Section 9: Module Development

Modules are a powerful way to reuse code. You can use either external modules, or write modules yourself. When using external modules, which are maintained by the community, you will get updates and fixes for free.

External modules for AWS are listed on github at `https://github.com/terraform-aws-modules`.

Some popular modules are:
- **terraform-aws-vpc** for vpc creation
- **terraform-aws-alb** for application load balancer creation
- **terraform-aws-eks** for kubernetes cluster creation

To use the modules above and all others at that site, go to the github page, then copy out the **Usage** block and place it in a .tf file. That will create the resource.


**NOTE:** Since many modules rely on other modules, there are also example folders containing a complete deployment with dependencies included. This amounts to several files that you would place in a project folder and tweak to your specifics.

When using a module that is complex and depends on other modules, you will often specify a variable in the instantiation of that module which referrences another module that needs to be started first. In this way, the other module must start for the variable to be populated. This ensures dependencies are met and errors do not occur.

Writing your own module gives you full flexibility, and you can maintain the module in a github repo for reuse over multiple projects

### build modules for an application load balancer

The following diagram illustrates the 4 modules needed for an application load balancer and the underlying ECS cluster it will service.

![](module1.png)

These modules are cloned from `https://github.com/in4it/terraform-modules.git`:

**ECS Cluster** - `modules/ecs.tf`:
- the ECS resource to start the cluster
- CloudWatch for the container logs
- IAM roles for the EC2 instances. Can also be used for the tasks themselves
- Security Groups for the EC2 instances for ingress and egress rules
- Autoscaling group for starting, restarting, and scaling EC2 instances

**ECS Service** -
- ECS Service running on the cluster
- ECS Service template for creating the ECS service and specifying the container etc
- ALB Target Group contains the containers and their ports for the load balancer to use

**ALB**
- ALB Security Group has ingress rules and ports for the containers
- ACM Certificate for reading certs to accomodate SSL on AWS
- HTTP/HTTPS listeners to listen for incoming connections to the ALB

**ALB Rule** needed to direct to appropriate target groups. not needed if only one target group

## AWS EKS

The **Elastic Kubernetes Service** is a managed service offering highly available, scalable and secure kubernetes clusters. EKS is an alternative the Elastic Container Service, which is the container orchestration tool offering by Amazon.

Whereas ECS is restricted to Amazon, kubernetes can run on any cloud provider or on-premises.

The EKS service manages the _master node_ of the kubernetes cluster only. On EKS, the master nodes are available in multiple AZs for redundancy, and will scale automatically when the worker load requires it.

Since EKS integrates with IAM it is secure by default.

### EKS vs ECS

- EKS costs more than ECS - .20 per hour in us-east-1 for example
  - for smaller setups ECS is cheaper
- kubernetes is more popular than ECS. if planning to use more cloud providers or on-prem kubernetes is a better choice
- kubernetes has more features and is more complicated. For simpler apps ECS is preferable
- ECS is tightly integrated with other AWs services, but EKS will catch up.

Overview of setup for EKS cluster

![](eks1.png)

**NOTE:** The **Heptio auth** tool is necessary for kubectl to access the cluster API on AWS. This is because EKS is integrated with IAM security. The Heptio tool is used to obtain a _token_ needed for kubectl to communicate with the cluster.

#### Launch EKS Cluster with Terraform
