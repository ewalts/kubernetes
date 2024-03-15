# Autoscaling k8s ubuntu cluster deployment by Richard Eric Walts


Self Managed Kubernetes Cluster with Ubuntu Instances Auto-scaling
---
Designed to be a low cost kuberentes cluster in AWS.
Many of the shelf solutions make things simple. But cost significantly more.

#Required Prerequisites:
 - AWS account with administrative access to EC2
 - Ansible installed and configured w/access to run plays through localhost for AWS
 - PHP8.

# NOTE: Regarding Update inventory PHP Script

This script is used by: 
 - deployment_install.yml 	_primary deployment playbook_ Manual
 - add_k8swn.yml 		_secondary playbook to add nodes created by the ASG_ Manual

 Currently it does not prune dead hosts.

 There is a short bash script 'clear_inventory.sh'.  It is not fancy. It just replaces the current inventory.yml with the backup inventory.yml_bkp. It is not fancy.
 I want to enrich the features of this script. But I don't think it will happen so much locally. I'm leaning toward storing the inventory in s3 or codecommit. The file would be updated by lambda and cloud watch event triggered by ASG activity.

#Overview:
- The process begins by creating dependencies in AWS: 
  [subnet, security group, iam policy, role, instance-profile, ELB, host A DNS record].
 One EC2 instance is created. The Ubuntu AMI could come straight from the AWS catalog.
  Currently using Ubuntu 22.04 LTS. 
 
 The deployment process defines some of my standard configurtions to the initial Ubuntu Instance. 
 Including, not limited to: firewall settings, SSH group membership restrictions, NTP service, currently installed packages updated, installs Java, boto, aws-cli, etc.

 After basic configurations, runs the install/configure play for Kubernetes apps container runtime.

 After the software is installed on the first instance, it is cloned into the AMI for the ASG launch template.

 After the AMI is created, the cluster initialization play is imported. After installing Calico Tigera operator, the join command/token is generated and stored in an s3 bucket.  

 The user-data script used with ASG fetches and runs that command joining the worker nodes to the cluster.
 
# The deployment records identfying information for each service created.
 -  The default location where these details are stored: [~/.ansible/output/deployment_register_vars.yml]
 -  This is useful to rollback the deployment.
 -  One future enhancement will include the ability for automatic rollback for everything deployed in the event of a task failure.

 - Sample output/deployment_register_vars.yml

      vpc_id: vpc-03171xxxxxxxxxxxxxxx
      subnet_id: subnet-015xxxxxxxxxxxxxx
      sg_id: sg-082e894xxxxxxxxxxxx
      iam_role: AROxxxxxxxxxxxxxx
      iam_policy: 01-k8s-w2-EC2Policy
      instance_profile: 01-k8s-w2-EC2Profile
      elb: 01-k8s-w2-lb01
      instance_id: i-094xxxxxxxxxxxxxxxxxxxx
      dns_record: 34.x.x.x A 01-k8s-cp.west2.mydomain.edu.
      ami_id: ami-02c7xxxxxxxxxxxxxxx
      lt_id: lt-00e4xxxxxxxxxxxxx
      asg_id: arn:aws:autoscaling:us-west-2:xxxx:autoScalingGroup:09sbs88f-xxxx-xxx2-xxd2-xxxxxa079:autoScalingGroupName/01-k8s-w2-cl
 



#Playbook tasks:

host_infrastructure.yml
- ###> Task -name: AWS - Create EC2 Instance
- ###> Task -name: Create VPC for Kubernetes 
- ###> Task -name: Create subnet for kkubernetes servers
- ###> Task -name: Create security group
- ###> Task -name: Show security group sg instance_id
- ###> Task -name: Create K8s ECR Role
- ###> Task -name: AWS IAM Instance Profile - Create
- ###> Task -name: Create K8s ECR Policy
- ###> Task -name: Attach K8s IAM Role to Profile
- ###> Task -name: create classic elb for http
- ###> Task -name: New EC2 Instance
- ###> Task -name: Create a DNS record-

- ###> Task -name: Run server setup playbook

ubuntu_server_setup.yml
 - ###> Task -name: Initial server setup tasks
 - ###> Task -name: Wait for SSH
 - ###> Task -name: Update apt cache
 - ###> Task -name: Update installed packages
 - ###> Task -name: Define prefered timezone
 - ###> Task -name: Make sure NTP service exists
 - ###> Task -name: Make sure NTP service is running
 - ###> Task -name: Create the ssh _group
 - ###> Task -name: Create the ssh _group
 - ###> Task -name: Make sure we have a 'sudo' group
 - ###> Task -name: Create a user with sudo privileges
 - ###> Task -name: Add ubuntu user to the allowed group
 - ###> Task -name: Set authorized key for remote user
 - ###> Task -name: Grant SUDO access
 - ###> Task -name: Disable remote login for root
 - ###> Task -name: Change the SSH port
 - ###> Task -name: UFW - Allow ssh connections
 - ###> Task -name: UFW - Allow http/https connections
 - ###> Task -name: UFW - Allow http/https connections
 - ###> Task -name: Brute-force attempt protection for SSH
 - ###> Task -name: UFW - Deny other incoming traffic and enable UFW
 - ###> Task -name: Remove excess packages no longer needed
 - ###> Task -name: Reboot all hosts

- ###> Task -name: Run K8s installation playbook

k8s_install.yml
 - ###> Task -name: Wait for ssh
 - ###> Task -name: Create the k8s modules file.
 - ###> Task -name: Include and load k8s kernel modules.
 - ###> Task -name: modprobe
 - ###> Task -name: System configurations for Kubernetes networking
 - ###> Task -name: Add conf for containerd
 - ###> Task -name: Apply new settings
 - ###> Task -name: Install containerd
 - ###> Task -name: Turning off swap space
 - ###> Task -name: Install and configure dependencies
 - ###> Task -name: Kubernetes repository file creation
 - ###> Task -name: K8s installation Source source repository
 - ###> Task -name: Package install ation kubelet kubeadm kubectl docker.io


