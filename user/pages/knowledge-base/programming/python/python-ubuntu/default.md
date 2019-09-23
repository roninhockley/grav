---
visible: true
title: Python 3 on Ubuntu 18.04
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [linux,python]
---

How to properly set up a python 3 dev environment on Ubuntu 18.04.

#### Configure Python 3

Ubuntu 18 ships with Python 3.6.7 out of the box. What we will do is install pip and ensure we have a healthy dev environment.

Install **pip**.

    sudo apt install -y python3-pip

Add to the build environment.

    sudo apt install build-essential libssl-dev libffi-dev python3-dev

#### Configure virtual environment

Virtual environments enable you to have an isolated space on your server for Python projects, ensuring that each of your projects can have its own set of dependencies that won’t disrupt any of your other projects.

Install **venv**

sudo apt install -y python3-venv

Create a directory for your virtual environments

    mkdir environments
    cd environments

Create the virtual environment

    python3.6 -m venv my_env

To use this environment, you need to activate it, which you can achieve by typing the following command that calls the activate script:

    source my_env/bin/activate

    (my_env) ron@jupyter:~/environments$   #  the prompt looks like this

!!! Within the virtual environment, you can use the command python instead of python3, and pip instead of pip3 if you would prefer. If you use Python 3 on your machine outside of an environment, you will need to use the python3 and pip3 commands exclusively.
