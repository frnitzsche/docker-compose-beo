#!/bin/bash
# echo "alias log='tail -n 25 -f /var/log/custamization-script.log'" >> /home/ec2-user/.bash_profile
echo "alias log='while ! tail -n 25 -f /var/log/custamization-script.log
do
    sleep 1s
done 2>/dev/null'" >> /home/ec2-user/.bash_profile

sudo yum update -y && \
sudo yum install git git-lfs -y && \
git clone https://github.com/frnitzsche/docker-compose-beo.git && \
cd docker-compose-beo && \
chmod a+x customization-script.sh && \
/bin/bash customization-script.sh > /var/log/custamization-script.log 2>&1
