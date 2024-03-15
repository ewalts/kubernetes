#!/bin/bash
###############################################################################################
###> New x bash -> update-token.sh  -> Initial creation user => eric => 2024-02-25_18:29:54 ###
###############################################################################################
#_#>
# CLI Colors
Red='\e[0;31m'; BRed='\e[1;31m'; BIRed='\e[1;91m'; Gre='\e[0;32m'; BGre='\e[1;32m'; BBlu='\e[1;34m'; BWhi='\e[1;37m'; RCol='\e[0m';
date=$(date +%Y-%m-%d-%T)
echo $date >> update-cron.log
sudo kubeadm token create  --print-join-command >> kubernetes_join_command

aws s3 cp kubernetes_join_command s3://MY_S3_BUCKET/ 

