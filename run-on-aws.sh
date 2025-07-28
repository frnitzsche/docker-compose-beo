#!/bin/bash
ins_type='t4g.small'
region='eu-central-1'
architecture=$(aws ec2 describe-instance-types --instance-types $ins_type --query 'InstanceTypes[0].ProcessorInfo.SupportedArchitectures[-1]' --output text)
ami_id=$(aws ec2 describe-images --region $region --filters 'Name=name,Values=al2023-ami-2023.8*' 'Name=architecture,Values='$architecture --query 'Images[-1].ImageId' --output text)
# aws ec2 run-instances --region $region --image-id $ami_id --instance-type $ins_type --iam-instance-profile '{"Arn":"arn:aws:iam::337590134778:instance-profile/aws-create-ec2-instance"}' --block-device-mappings DeviceName=/dev/xvda,Ebs={VolumeSize=30} --user-data file://user-data.sh
aws-create-ec2-instance.sh -r $region -t $ins_type -u user-data.sh -v 30
