---
title: 'KVM Host Setup for Home Lab'
taxonomy:
    category:
        - KB
    tag:
        - kvm
        - homelab
visible: true
author:
    name: 'Ron Fish'
---
[toc]

#### Server layout

- server1 -  (fast CPUs and mage RAM) centos 7.6 - container workloads
- server2 - (mega cores and 64G RAM) centos 7.6 - VM workloads, packer image creation and storage
- server3 - (8 reasonably fast cores, 32G RAM) - core infra services like Jira

#### Build process for Centos KVM hosts

Use the gparted live USB stick to preformat the drives before installing.

After install of OS :
```
usermod -aG libvirt ron
```

Set vnc and spice to listen on all ports in `/etc/libvirt/qemu.conf`

Uncomment these lines:
```
vnc_listen = "0.0.0.0"
spice_listen = "0.0.0.0"
```

Set up firewall rules for spice and vnc
```
firewall-cmd --permanent --add-port=5900-5950/tcp
firewall-cmd --reload
```

Create br0 bridge for KVM
```
nano /etc/sysconfig/network-scripts/ifcfg-br0

DEVICE=br0
TYPE=Bridge
PREFIX=24
BOOTPROTO=dhcp
ONBOOT=yes
DELAY=0
```

Point the default adapter to the bridge
```
nano /etc/sysconfig/network-scripts/ifcfg-<adapter name>

TYPE="Ethernet"
BOOTPROTO="none"
NAME="bridge-br0"
DEVICE="enp0s25"
ONBOOT="yes"
BRIDGE="br0"
HWADDR="f8:b1:56:d7:61:3c"
```

#### Packer workflow for creating images

**server2**

Virtual machine **packer**:
- Debian stretch
- **/home/ron/packer** contains the packer templates and associated scripts and ansible playbooks for building base vm images
- after images are created they are stored on a USB drive attached to server2. The images are pulled from that usb drive to the **/var/lib/libvirt/images** folder for use on new vm's.

#### Workflow for creating new vm's

New vm's are provisioned via ansible playbooks run against one of the vm hosts. This will become a Jenkins job running from a jenkins vm on server3.




#### Create sparse qcow2 disk image

`sudo qemu-img create -f qcow2 -o preallocation=metadata /var/lib/libvirt/images/<name>.qcow2 50G`
