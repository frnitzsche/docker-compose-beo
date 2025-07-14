#!/bin/bash
sudo yum update -y && \
sudo yum install git git-lfs -y && \
sudo git lfs install && \
git clone https://github.com/frnitzsche/docker-compose-beo.git && \
cd docker-compose-beo && \
chmod a+x customization-script.sh && \
/bin/bash customization-script.sh > /var/log/custamization-script.log 2>&1

sudo yum install git-lfs -y && \