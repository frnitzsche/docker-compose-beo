#!/bin/bash
host="beo-anishev.mywire.org"
dir="/docker-compose-beo"

curl "http://api.dynu.com/nic/update?hostname=$host&myip=$(curl -s ifconfig.me)&password=faf0152cfacc4704af98927ae6dd55f4"
sudo yum install docker -y && \
sudo systemctl enable docker.service && \
sudo systemctl start docker.service && \
sudo curl -L https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m) -o /usr/bin/docker-compose && \
sudo chmod 755 /usr/bin/docker-compose && \
cd /$dir && \
sudo docker-compose up -d && \

sleep 5s && \
sudo yum install nginx certbot certbot-nginx -y && \
sudo sed -i "s/server_name  _;/server_name ${host};/g" /etc/nginx/nginx.conf && \
echo 'ec2-user:478312zxc' | sudo chpasswd && \

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

sudo systemctl enable nginx.service && \
sudo systemctl restart nginx.service && \
sleep 60s && \
sudo certbot --nginx -d $host -m my@mail.com --agree-tos -n --test-cert

echo '>>>>> Downloading beodb sql dump file from S3 service'
sudo aws s3 cp s3://beo-anishev/beodb.bz2 /$dir

echo '>>>>> Uncompressing beodb sql dump file'
sudo bzip2 -d beodb.bz2 && \

echo '>>>>> Installing MariaDB client tools'
sudo yum install mariadb1011-client-utils -y && \

echo '>>>>> Installing Pipe Viewer'
sudo yum install pv -y

echo '>>>>> Importing beodb sql dump file'
pv /$dir/beodb | mysql -h 127.0.0.1 -u myuser -p mypassword moussala
