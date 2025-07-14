#!/bin/bash
aws ec2 run-instances --region eu-central-1 --image-id ami-0af9b40b1a16fe700 --instance-type t3.micro --user-data file://user-data.sh
