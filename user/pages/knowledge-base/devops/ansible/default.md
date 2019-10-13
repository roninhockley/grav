---
visible: true
title: Ansible Quick Reference
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,ansible]
---
[toc]

## SSH Connections

Connections involve 2 factors:
- initial connection over SSH to the host
  - By default, ansible connects with the user running the ansible command, using a key.
  Without a key on the target, you must use `--ask-pass` to get connected.
- escalated user to perform ansible tasks
  - the *ansible_user* is an existing user on the target machine with privileges to perform the changes to the host, via a _become_ method of either `sudo` (the default)  or `su`. This is configured either in the inventory variables, on the command line, or in the playbook itself.



## Inventory

Here is a sample inventory file that demonstrates both how the INI file is structured and the use of host variables:

    [web]
    mastery.example.name ansible_host=192.168.10.25

    [dns]
    backend.example.name

    [database]
    backend.example.name

    [frontend:children]
    web

    [backend:children]
    dns
    database

    [web:vars]
    http_port=88
    proxy_timeout=5

    [backend:vars]
    ansible_port=314

    [all:vars]
    ansible_ssh_user=ron

[**The full list of available inventory variables:**](https://docs.ansible.com/ansible/latest/user_guide/intro_inventory.html#connecting-to-hosts-behavioral-inventory-parameters)

## ad-hoc commands

To execute an ad-hoc from the command line use this format:

    ansible <host> -m <module> -a "<arguments>"

## Playbooks

### Order of Operations

For the most part, items in a playbook are executed in the order they are listed in the playbook itself.

Within a play, the order of operations is as follows:

- variable loading
- fact gathering
- pre_tasks execution
- handlers notified from pre_task execution
- roles execution
- tasks execution
- handlers notified from roles and tasks
- post_tasks execution
- handlers notified from post_tasks

The paths for variable files, included task files, included playbook files, files to copy, templates, and scripts are all relative to the directory of the file calling them.

### Playbook directives

Keywords such as **become**, **gather_facts**, or **connection** are examples of keywords which control the behavior of ansible when executing a playbook.

For the full list of playbook keywords go [here](https://docs.ansible.com/ansible/latest/reference_appendices/playbooks_keywords.html#play).

### Playbook execution strategy

There are two options for how the hosts execute each task:  _linear_ and _free_.

- linear - every host must complete each task before the next task is initiated. this is the default.
- free - each host will move on to the next task as soon as it finishes.

### Host selection patterns

To define the hosts that the play will run against, a host pattern can contain one of more blocks with a host, group, wildcard or regex. Blocks are separated by a colon, wildcards are just an asterisk, and regex patterns start with a tilde:

`hostname:groupname:*.example:~(web|db)\.example\.com `

Advanced usage can include group index selection or even ranges within a group:

`webservers[0]:webservers[2:4] `.

### Play and task names

They are not strictly, but they will show up in logs and on command line output, so are good practice.

- names of plays and tasks should be unique
- variables can be used to name tasks and plays, but they must be defined statically because the variables are expanded at playbook **parse** time.

## Module reference

Every task is made up of a _name_ (optional), _module reference_, _module arguments_, and _task control directives_.

**example**

    tasks:
      - name: update repo for debian
        apt:
          update_cache: yes
        when: ansible_facts['os_family'] == "Debian"

### How modules are executed

When a playbook is run, the following actions are taken by ansible to execute it on the remote host:
- ansible locates the module file in `/usr/lib/python2.7/dist-packages/ansible/modules`
- the module file is read into memory, and the arguments are added in. this creates a file object in memory
- the core ansible code for executing modules is added to this file object
- the collection is compressed, Base64 encoded, and wrapped into a script.
- ansible connects to the remote host, by default over SSH, and creates a temp directory.
- ansible connects again over SSH, uncompresses the base64 script, and writes it into a file in the temp directory
- ansible connects again, executes the script from the temp directory on the remote host, then deletes the temp directory containing it.

Since there are 3 SSH connections occuring for every task, on a larger environment this becomes time consuming.

To mitigate this there are 2 solutions:
- **ControlPersist** - an SSH feature to create a persistent socket to avoid 3 separate handshakes. This is used if the system running ansible supports it.
- **pipelining** - rather than using 3 connections, ansible will open an SSH connection on the remote host, and pipe in the module code and script for execution. This uses one connection, not 3.

Ansible forks off a separate connnect process for a number of hosts, governed by the config `forks=`. This controls how many hosts ansible will execute tasks on at one time in parallel. The default is 5 forks.

## Variables

### variable types

- **inventory variables** - pertain to inventory. *host_vars* for individual hosts, or *group_vars* for groups of hosts. They are supplied by _inventory files_, a _dynamic inventory plugin_, or from *host_vars/<host>* or *group_vars/<group>* directories.
  - whether they come from *host_vars/<host>* or *group_vars/<group>*, they are assigned to a host's `hostvars`, and they are accessible from playbooks and template files.
  - These variables define behavior when dealing with certain hosts, or maybe site-specific pertaining to certain app environments running on particular hosts.

- **role variables** - variables specific to a role, and used by role tasks.
  - **NOTE** Once a role is added to a playbook, its variables are global in scope and available throughout the rest of the playbook including to other roles. In simple playbooks, roles are run one at a time. But in complex playbooks, care must be taken to avoid collisions like a variable being set by multiple roles.

- **play variables** - variables defined within a play, either by the **vars:** key or sourced from external files via the **vars_files:** key.
  - play variables can also be user supplied by using **vars_prompt**.
  - these variables are scoped to the play and any tasks within the play.

- **task variables** - these are from data discovered while executing tasks or from fact gathering. They are host specific and are added to the **hostvars**.
  - They are discovered via **gather_facts**, **fact modules** (modules that return data rather than alter state), from task return data via the **register** key, or defined directly in a task by using **set_fact** or **add_host** modules.
  - this data can also be obtained from the user by using the **prompt** argument to the **pause** module:

      - name: get the operators name
      pause:
        prompt: "Please enter your name"
      register: opname

- extra variables - supplied on the command line when executing **ansible-playbook** by using the **--extra-vars** option:
```
    --extra-vars "foo=bar owner=fred"
    --extra-vars '{"services":["nova-api","nova-conductor"]}'
    --extra-vars @/path/to/data.yaml
```
### Magic variables

These are reserved variables which are always set at playbook execution. They provide information such as the current hostname which a task is being appied to, or what groups that host is in.

A full list of magic variables is [here](https://docs.ansible.com/ansible/latest/reference_appendices/special_variables.html).

### External data

Data for role variables, play variables, and task variables can also come from external sources. Ansible provides a mechanism to access and evaluate data from the control machine (the machine running `ansible-playbook`). The mechanism is called a **lookup plugin**, and a number of them come with Ansible.

These plugins can be used to look up or access data by reading files, generate and locally store passwords on the Ansible host for later reuse, evaluate environment variables, pipe data in from executables, access data in the Redis or etcd systems, render data from template files, query dnstxt records, and more. The syntax is as follows:

`lookup('<plugin_name>', 'plugin_argument') `

**example for adding a public key on a remote host**

    - name: Set authorized key for user ron
      authorized_key:
        user: ron
        state: present
        key: "{{ lookup('file', '/home/ron/.ssh/id_rsa.pub') }}"

### Variable precedence

In a situation where the same variable name may be used, ansible has an order for loading variable data to decide which variable definition will win:

**in descending order of precedence**

1. **extra vars** from command line (always win)
2. include parameters
3. role and include role parameters
4. **set_facts**, and those created with **register**
5. **include_vars**
6. task **vars**
7. block **vars** for tasks within a block
8. role **vars**  (defined in **main.yml** in the _vars_ subdirectory of the role).
9. play **vars_files**
10. play **vars_prompt**
11. play **vars**
12. Host facts (and also cached **set_facts**)
13. **host_vars** from playbook
14. **host_vars** from inventory
15. inventory file or script defined _host_ **vars**
16. **group_vars** playbook
17. **group_vars** inventory
18. **group_vars/all** playbook
19. **group_vars/all** inventory
20. inventory file or script defined _group_ **vars**
21. role defaults
22. command line values such as **-u REMOTE_USER**

### Variable group priority

In the following example, the **host1.example.com** server is effectively in multiple groups:

    [frontend]
    host1.example.com
    host2.example.com

    [web:children]
    frontend

    [web:vars]
    http_port=80
    secure=true

    [proxy]
    host1.example.com

    [proxy:vars]
    http_port=8080
    thread_count=10

And the **http_port** variable is set in both the **[web:vars]** and the **[proxy:vars]** group variable stanzas. This will conflict for the **host1.example.com** server.

By default ansible assigns them according to the alphabretical name of the groups. So in this case the **[web:vars]** setting *http_port=80* will apply to the server.

To override this, use the **ansible_group_priority** group variable like so:

    [proxy:vars]
    http_port=8080
    thread_count=10
    ansible_group_priority=10

Now the **[proxy:vars]** block will win. This is better than having to rename groups if you want to change the order.

#### Summary

- Playbooks contain variables and tasks.
- Tasks link bits of code called modules with arguments, which can be populated by variable data.
- These combinations are transported to selected hosts from provided inventory sources.

## Protecting Secrets with Ansible

The Ansible facility for encrypting text files is **Vault**. Before 2.4, all secrets were accessed with a single Vault password. Now, by using _Vault IDs_ you can have multiple passwords for varying levels of security, depending on the asset being protected. For example you could have one vault ID for testing, and another vault ID for production, with a stronger password.

Ansible Vault passwords can come from one of the following three sources:
- a user entered string which ansible will prompt for when required
- a secured, flat text file with the vault password in unencrypted form.
- an executable such as a python script which fetches the pw from a credential mgmt system

Here is an example for using each of the 3 methods on the command line, for vault ID **prod**:

    ansible-playbook --vault-id prod@prompt playbook.yaml
    ansible-playbook --vault-id prod@/path-to/vault-password-text-file playbook.yaml
    ansible-playbook --vault-id prod@/path-to/vault-password-script.py playbook.yaml

They can also be combined:
```
ansible-playbook --vault-id prod@prompt testing@/path-to/vault-password-text-file playbook.yaml
```

Vault can encrypt any _structured_ data used by Ansible. This can be almost any YAML or JSON file, or even a single variable in an otherwise unencrypted YAML file such as a playbook or role.

Examples of encrypted files that ansible can work with include:
- group_vars/ files
- host_vars/ files
- include_vars targets
- vars_files targets
- --extra-vars targets
- Role variables
- Role defaults
- Task files
- Handler files
- Source files for the copy module

If a file can be expressed in YAML and read by Ansible, or if a file is to be transported with the **copy** module, it is a valid file for encryption in Vault.

Because the entire file will be unreadable at rest, care should be taken to not be overzealous in picking which files to encrypt. Any source control operations with the files will be done with the encrypted content, making it very difficult to peer-review.

To avoid having to encrypt an entire YAML file, ansible has the **encrypt_string** feature for ansible vault, which allows to encrypt a string within a YAML file.

Creating and interacting with vault-encrypted files is done using **ansible-vault** on the command line.

### Create a new encrypted file

Here are 3 examples of using **ansible-vault create** to create a new encrypted file:

**prompting for the vault password**

`ansible-vault create --vault-id @prompt secrets.yaml` -  first it will prompt for the password to set, then an editor will open to enter the items in the file.

**using a password file**

    echo "password" > password_file
    ansible-vault create --vault-id ./password_file secrets.yaml

Again it will open an editor

**using a password script**

`ansible-vault create --vault-id ./password.sh secrets.yaml` - the script will retrieve the password and ansible will assign it, then open the editor for entering items.

### Encrypt an existing file

Use **ansible-vault encrypt** to encrypt existing files.

`ansible-vault encrypt --vault-id @prompt existing_file.yaml` - each of the three methods for setting the password apply here as above.

Multiple files can be entered on the command line at the same time. This gives you one password for all the files.

### Editing encrypted files

After encryption, the files cannot be edited directly. Use **ansible-vault edit** to edit them.

`ansible-vault edit --vault-id @prompt encrypted_file.yaml` - the file will open in the editor.

### Decrypting files

Use **ansible-vault decrypt** for this.

`ansible-vault decrypt --vault-id @prompt encrypted_file.yaml`

### Executing playbooks with encrypted files

Using an encrypted file in a playbook means entering the vault id and password method on the command line when running **ansible-playbook**:

`ansible-playbook -i hosts --vault-id prod@prompt playbook.yaml`

Within the playbook the yaml file is stated as normal:

    ---
    - name: show me an encrypted var
      hosts: localhost
      gather_facts: false

      vars_files:
        - a_vars_file.yaml

      tasks:
        - name: print the variable
          debug:
            var: something

### Encrypting a string

To use an encrypted string in a playbook, without having to create file for the string and encrypting the file, use **ansible-vault encrypt_string** to first generate a hash for the string and echo it to stdout. Then in the playbook you just paste in the hash at the variable block:

    vars:
      my_secret: !vault ....

**NOTE** The book example is a typo, cannot show it above.

### Protecting secrets on remote hosts

When ansible runs against a remote host, you can prevent your secrets from showing up in log files on that host by setting the key **no_log: true** within the task block that uses the secret.

    ---
    - name: show me an encrypted var
      hosts: localhost
      gather_facts: false

      vars_files:
        - a_vars_file.yaml

      tasks:
        - name: print the variable
          debug:
            var: something
          no_log: true

You can also set ansible to log locally instead of on the remote host by either:
- setting **log_path** in the ansible.cfg to a local path on the machine running ansible
- setting the **ANSIBLE_LOG_PATH** env variable to a path writable by the user running ansible.

## Ansible AWX

**AWX** is the open source upstream version of Ansible Tower. It is a web GUI frontend which users can use to run the playbooks centrally, for easier tracking of user activity and management of secrets etc.

It runs in docker containers and therefore can even be run in kubernetes or openshift. It offers many advantages for enterprise:

- RBAC control
- integration with LDAP, AD etc
- secure credential management
- auditability
- accountability
- lower barrrier to entry for new ansible users
- improved management of playbook version control

### Setup AWX

I created `awx.yml` playbook to install rerequites on the docker host:

    ---
      - name: install everything for AWX
        hosts: docker
        vars:
          packages:
            - git
            - python-pip
            - ansible
          pips:
            - setuptools
            - docker-compose
        tasks:
          - name: install packages for awx
            package:
              name: "{{packages}}"
              state: present
          - name: install python tools
            pip:
              name: "{{pips}}"

Next clone the aws repo:
`git clone https://github.com/ansible/awx.git` then cd to **aws/installer**

Open the `inventory` file and set the following parameters:
- admin_password = nikkyron
- pg_password = nikkyron
- postgres_data_dir = /var/lib/awx/pgdocker
- project_data_dir = /var/lib/awx/projects
- rabbitmq_password = nikkyron
- secret_key = nikkyron

Finally, install with `sudo ansible-playbook -i inventory install.yml`.

This launches the containers on port 80, then go to `http://docker:80` and login with admin/nikkyron.

The book goes thru a very basic demo of running a single playbook against the docker host.

For usage on AWX go to the Tower docs page [here](https://docs.ansible.com/ansible-tower/index.html).

## Jinja2 Templates

**Jinja2** is a modern and designer-friendly templating language for Python. In this section we will cover **Control structures**, **Data manipulation**, and **Comparisons**.

### Control Structures

Control structures include conditionals, loops, and macros, and they appear in `[%....%]` format.

#### Conditionals

Conditionals are **if** statements that work much the same as in python. They can have one or more optional **elif** blocks, and an optional **else** block. Unlike python, the if statement requires an explicit **endif**.

    setting = {{ setting }}
    {% if feature.enabled %}
    feature = True
    {% else %}
    feature = False
    {% endif %}
    another_setting = {{ another_setting }}
