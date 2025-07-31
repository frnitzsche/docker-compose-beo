#!/bin/bash
host="beo-anishev.mywire.org"
dir="/docker-compose-beo"

echo ">>>>> Updating DNS entry for host $host" && \
curl "http://api.dynu.com/nic/update?hostname=$host&myip=$(curl -s ifconfig.me)&password=faf0152cfacc4704af98927ae6dd55f4"  && \

echo '>>>>> Installing docker nginx certbot certbot-nginx pv mariadb1011-client-utils' && \
yum install -d1 docker nginx certbot certbot-nginx pv mariadb1011-client-utils -y && \

echo '>>>>> Strarting certbot-renew.timer' && \
systemctl start certbot-renew.timer && \

echo '>>>>> Strarting docker service' && \
systemctl enable --now docker.service && \

echo '>>>>> Installing docker-compose' && \
curl -L https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m) -o /usr/bin/docker-compose && \
chmod 755 /usr/bin/docker-compose && \
cd $dir && \

echo '>>>>> Running docker-compose' && \
docker-compose --progress quiet up -d && \

echo '>>>>> Sleeping for 5 sec' && \
sleep 5s && \

echo '>>>>> Setting password for user ec2-user' && \
echo 'ec2-user:478312zxc' | chpasswd && \

echo '>>>>> Modifying nginx.conf' && \
sed -i "s/server_name  _;/server_name ${host};/g" /etc/nginx/nginx.conf && \

sed '/root         \/usr\/share\/nginx\/html;/r'<(cat <<EOF
        location / {
            proxy_pass http://127.0.0.1:8081;
            proxy_set_header Host \$host;
            proxy_set_header X-Real-IP \$remote_addr;
            proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto \$scheme;
        }
EOF
) -i -- /etc/nginx/nginx.conf && \

echo '>>>>> Restarting nginx server' && \
systemctl enable --now nginx.service && \

echo '>>>>> Sleeping for 60 sec' && \
sleep 60s && \

echo '>>>>> Running certbot' && \
certbot --nginx -d $host -m my@mail.com --agree-tos -n --test-cert && \

echo '>>>>> Downloading beodb sql dump file from S3 service' && \
aws s3 cp s3://beo-anishev/beodb.bz2 $dir && \

echo '>>>>> Uncompressing beodb sql dump file' && \
pv  beodb.bz2 | bzip2 -dc > beo.sql

echo '>>>>> Importing beodb.sql dump file into MarriaDB' && \
pv  $dir/beo.sql | mysql -h 127.0.0.1 -u root -pmypassword moussala

# Certbot auto renewal timer is not started by default.
# Run 'systemctl start certbot-renew.timer' to enable automatic renewals.