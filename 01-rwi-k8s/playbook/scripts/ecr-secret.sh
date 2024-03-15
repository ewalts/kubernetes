#!/bin/bash

kubectl create secret docker-registry ec2cred \
--docker-server=$(echo $AWS_ECR_SERVER) \
--docker-username=AWS \
--docker-password=$(aws ecr get-login-password) \
--namespace=nginx-php

