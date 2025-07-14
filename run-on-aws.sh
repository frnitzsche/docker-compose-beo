#!/bin/bash

aws ec2 run-instances --image-id ami-0af9b40b1a16fe700 --instance-type t3a.small --user-data file://user-data.sh
