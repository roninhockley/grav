---
visible: true
title: SSH
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [linux,ssh]
---

This section is about all things to do with using SSH to connect and/or execute commands remotely on another host.

#### Passwordless access using key pairs

##### Generate the keypair

<pre>
$ <b>ssh-keygen -b 4096 -C "Example RSA Key"</b>
Generating public/private rsa key pair.
Enter file in which to save the key (/home/vagrant/.ssh/id_rsa):
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
Your identification has been saved in /home/vagrant/.ssh/id_rsa.
Your public key has been saved in /home/vagrant/.ssh/id_rsa.pub.
The key fingerprint is:
SHA256:hAUNhTqXtfnBOkXMuIpxkvtTkM6NYRYxRbT5QWSVbOk Example RSA Key
The key's randomart image is:
+---[RSA 4096]----+
|      =@*=+o.o   |
|      o++=+ =    |
|     o.=+*.o     |
|    * X.+.+.E    |
|     & *S+..     |
|    o = = .      |
|     . . .       |
|      o          |
|       .         |
+----[SHA256]-----+
</pre>

##### Copy key to target host

This command will place your public key into the `known_hosts` on the target server.

<pre>
$ <b>ssh-copy-id 192.168.33.11</b>
/usr/bin/ssh-copy-id: INFO: Source of key(s) to be installed: "/home/vagrant/.ssh/id_rsa.pub"
The authenticity of host '192.168.33.11 (192.168.33.11)' can't be established.
ECDSA key fingerprint is SHA256:LKhW+WOnW2nxKO/PY5UO/ny3GP6hIs3m/ui6uy+Sj2E.
ECDSA key fingerprint is MD5:d5:77:4f:38:88:13:e7:f0:27:01:e2:dc:17:66:ed:46.
Are you sure you want to continue connecting (yes/no)? yes
/usr/bin/ssh-copy-id: INFO: attempting to log in with the new key(s), to filter out any that are already installed
/usr/bin/ssh-copy-id: INFO: 1 key(s) remain to be installed -- if you are prompted now it is to install the new keys
vagrant@192.168.33.11's password:

Number of key(s) added: 1

Now try logging into the machine, with:   "ssh '192.168.33.11'"
and check to make sure that only the key(s) you wanted were added.
</pre>

##### Permississions for all SSH related files

Set all permissions as follows on your machine:

<pre>
chmod 700 ~/.ssh
chmod 644 ~/.ssh/authorized_keys
chmod 644 ~/.ssh/known_hosts
chmod 644 ~/.ssh/config
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub
</pre>

#### SSH to alternate port

    ssh <hostname> -p2020

#### Run a command on a remote host

>pre>

$ ssh centos2 "cat /etc/hostname"
Enter passphrase for key '/home/ron/.ssh/id_rsa':
centos2
[vagrant@centos1 ~]$
</pre>

#### SSH and forward X11 back to you

    $ ssh centos2 -X

#### Use and SSH config file

    Host * !CentOS2-V6              #  the * means this block applies to **all* hosts, except CentOS2-V6
     IdentityFile ~/.ssh/id_ed25519
     Port 22

    Host CentOS2-V4
     Hostname 192.168.33.11
     User vagrant

    Host CentOS2-V6
     Hostname fe80::a00:27ff:fe56:c5a7%%eth1
     IdentityFile ~/.ssh/id_rsa
     Port 22
     User vagrant

    Host CentOS2-Hostname
     Hostname centos2
     User vagrant

#### SSH local forwarding

Local forwarding means mapping a local TCP port or Unix socket to a remote port or socket. After local forwarding you would, for instance, curl the local port 9999 on your machine and it would be forwarded to port 8888 on a remote machine. This can be a security measure, forcing the connection to the remote webserver to go through an SSH tunnel.

First, do the mapping like so:

    $ ssh -f -L 9999:127.0.0.1:8888 192.168.33.11 sleep 120

This maps port 9999 on 127.0.0.1 (localhost) to port 8888 on remote host 192.168.33.11

Then, say there is a webserver listening on 192.168.33.11, you do:

    $ curl 127.0.0.1:9999

And the response header will come from 192.168.33.11

#### SSH remote forwarding

Remote forwarding means you forward a port on a remote machine to a local port on a local machine.

On the local machine do the following:

    $ ssh -R 5353:127.0.0.1:22 192.168.33.11

This maps port 5353 on the remote host 192.168.33.11 back to the local host on port 22

You can map a remote port to **any** local port, not just 22. For instance you can run a webserver on your local host and have it accessed thru the remote port.

1. start a simple python webserver on the local host, bound to port 8888

$ python -m SimpleHTTPServer 8888 &
[1] 6010

2. forward the remote port 7777 back to the local host at port 8888
$ ssh -R 7777:127.0.0.1:8888 192.168.33.11

3. Now on the remote host you can curl its port 7777 and be forwarded back to local port 8888
    $ curl 127.0.0.1:7777

#### Use SSH to create a jumpbox

You can use SSH from host1 to connect thru host2 and ultimately to host3 as follows:

    [ron@host1 ~]$ ssh -J ron@host2:22 host3

#### Use SSH to create a SOCKS proxy

Run this on the host to configure it as a SOCKS proxy

    host1 $ ssh -f -D9999 192.168.33.11 sleep 120
