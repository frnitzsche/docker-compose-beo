#!/bin/bash
host="beo-anishev.mywire.org"
dir="/docker-compose-beo"

echo ">>>>> Updating DNS entry for host $host" && \
curl "http://api.dynu.com/nic/update?hostname=$host&myip=$(curl -s ifconfig.me)&password=faf0152cfacc4704af98927ae6dd55f4"  && \

echo '>>>>> Installing docker' && \
sudo yum install docker -y && \

echo '>>>>> Strarting docker service' && \
sudo systemctl enable docker.service && \
sudo systemctl start docker.service && \

echo '>>>>> Installing docker-compose' && \
sudo curl -L https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m) -o /usr/bin/docker-compose && \
sudo chmod 755 /usr/bin/docker-compose && \
cd $dir && \

echo '>>>>> Running docker-compose' && \
sudo docker-compose up -d && \

echo '>>>>> Sleeping for 5 sec' && \
sleep 5s && \

echo '>>>>> Installing nginx certbot certbot-nginx' && \
sudo yum install nginx certbot certbot-nginx -y && \

echo '>>>>> Setting password for user ec2-user' && \
echo 'ec2-user:478312zxc' | sudo chpasswd && \

echo '>>>>> Modifying nginx.conf' && \
sudo sed -i "s/server_name  _;/server_name ${host};/g" /etc/nginx/nginx.conf && \

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
sudo systemctl enable nginx.service && \
sudo systemctl restart nginx.service && \

echo '>>>>> Sleeping for 60 sec' && \
sleep 60s && \

echo '>>>>> Running certbot' && \
sudo certbot --nginx -d $host -m my@mail.com --agree-tos -n --test-cert && \

echo '>>>>> Downloading beodb sql dump file from S3 service' && \
sudo aws s3 cp s3://beo-anishev/beodb.bz2 $dir && \

echo '>>>>> Uncompressing beodb sql dump file' && \
sudo bzip2 -d beodb.bz2 >  $dir/beodb.sql && \

echo '>>>>> Installing MariaDB client tools' && \
sudo yum install mariadb1011-client-utils -y && \

echo '>>>>> Installing Pipe Viewer' && \
sudo yum install pv -y # && \

echo '>>>>> Importing beodb.sql dump file into MarriaDB' && \
pv -c $dir/beodb.sql | mysql -h 127.0.0.1 -u root -pmypassword moussala
