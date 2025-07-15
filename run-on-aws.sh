#!/bin/bash
aws ec2 run-instances --region eu-central-1 --image-id ami-0af9b40b1a16fe700 --instance-type t2.micro --block-device-mappings DeviceName=/dev/xvda,Ebs={VolumeSize=30} --user-data file://user-data.sh
