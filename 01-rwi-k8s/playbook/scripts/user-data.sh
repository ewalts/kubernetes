#!/bin/bash
aws s3 cp s3://S3_BUCKET/kubernetes_join_command /tmp/kubernetes_join_command
sudo sh /tmp/kubernetes_join_command

