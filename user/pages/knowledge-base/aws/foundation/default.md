---
visible: true
title: AWS Services
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,AWS]
---

[toc]

This page contains foundational knowledge organized as AWS organizes it on the Web Console.

- Compute
- Storage
- Database
- Migration and Transfer
- Networking and Content Delivery
- Developer Tools
- Management and Governance
- Media Services
- Machine Learning
- Analytics
- Security, Identity, and Compliance
- Mobile
- AR & VR
- Application Integration
- Cost Management
- Customer Engagement
- Business Applications
- End User Computing
- IoT - Internet of Things
- Game Development

## Analytics

- **EMR Elastic MapReduce** - a HADOOP framework as a service for analyzing huge amounts of data
- **Athena** - analyze data stored in S3 buckets using SQL statements
- **Amazon Elasticsearch Service** - the Elasticsearch framework as a service for accruing and analysing data stored on AWS
- **Kinesis** - collect, process, and analyze real time streaming data
- **QuickSight** - a fully managed business intelligence reporting tool

## Application Integration

- **Step Functions** - coordinate components of distributed apps and microservices by defining your app as a series of steps
- **SWF Simple WorkFlow Service** - similar to step functions. for new apps the Step Function is recommended
- **SNS Simple Notification Service** - a _pub/sub_ messaging service, where you publish messages and subscribed users receive them. also used for push notifications to mobile devices
- **SQS Simple Queue Service** - a fully managed message qeuing service that performs _Process Decoupling_ by buffering incoming messages to an app instance until the app can catch up. This _decouples_ the app from its demand, and, in the meantime SQS works together with CloudWatch to provide metrics to the autoscaling group which will ultimately scale the app instance up to meet the demand going forward.

## Compute Services

- **EC2 Elastic Compute Cloud** - runs virtual servers called _instances_
- **EC2 Autoscaling** - horizontal scaling for EC2 instances according to predefined conditions including health checks
- **Amazon Lightsail** - easy way to launch virtual servers running apps in AWS. AWS manages DNS, storage etc
- **ECS Elastic Container Service** - highly scalable container management service for docker containers.
- **AWS Lambda** - run code without a server under it to manage.

## Customer Engagement

- **Amazon Connect** - a self service contact center in AWS provided on a pay as you go basis for handling customer interaction without having to code anything. It uses a drag n drop interface to build the interaction model.
- **Amazon Pinpoint** - send email, SMS and mobile push messages for targeted marketing campaigns, as well as direct messages to individual customers for something like order confirmations.
- **SES Simple Email Service** - a cloud based bulk email sending service

## Database Services

- **DynamoDB** - a serverless NoSQL managed service.
- **Redshift** - a petabyte scale data warehouse for big data.
- **Elasticache** - is in memory data cache. Placed between consumer and database to enhance access time and lessen database server load
- **DMS Database Migration Service** - used to migrate data from one platform to another, including onprem to cloud.
- **Neptune** - a managed _Graph_ database for data with many relationships

## Developer Services

- **Cloud9** - an IDE in the cloud, for deploying servers out of an IDE
- **Codestar** - a CICD pipeline manager with a project mgmt dashboard and JIRA integrated for issue tracking
- **XRay** - analyze and debug apps
- **CodeCommit** - a GIT repo running in the cloud
- **CodePipeline** - a CICD service for building, testing, and deploying apps upon changes in source repos
- **CodeBuild** - compiles, runs, tests, and packages software for deployment on AWS
- **CodeDeploy** - automates deployments to a variety of platforms like EC2, Lambda or even instances to run on prem

## AWS Media Services

- **Elemental MediaConvert** - a file based media transcoding service for converting video formats
- **Elemental MediaPackage** - prepares video for delivery over the internet and offers DRM
- **Elemental MediaTailor** - inserts targeted ads into video streams
- **Elemental MediaLive** - a broadcast grade live video processing service for streams to TV
- **Elemental MediaStore** - storage service for media
- **Kinesis Video Streams** - used to stream video from devices thru the cloud for machine learning

## Mobile Services

- **AWS Mobile Hub** - configure AWS services for mobile apps in one place
- **AWS Device Farm** - an app testing service for Android IOS and web apps, allows you to test your code on actual devices
- **AWS AppSync** - graphql backend for mobile and web apps

## Migration Services

- **AWS Application Discovery Service** - analyzes your data center to help plan migration of apps and services to AWS
- **AWS Database Migration Service** - migrate databases to the cloud, even one DB engine to another such as Oracle to AWS Aurora
- **AWS Server Migration Service** - can migrate thousands of on prem workloads to the cloud
- **AWS Snowball** - a portable petabyte scale device to migrate data from onr prem to the cloud storage

## Business Productivity and Streaming Services
- **Amazon Workdocs** - fully managed file collaboration service with a web client
- **Amazon WorkMail** - an email and calendar service
- **Amazon Chime** - an online meeting service
- **Amazon WorkSpaces** - a fully managed secure desktop as a service that can stream MS desktops
- **Amazon AppStream 2.0** - a fully managed app streaming service to stream apps to an html5 compatible browser

## IoT Internet of Things
- **AWS IoT** - a managed cloud platform allowing embedded devices like RaspBerry Pi to interact with cloud apps and other devices
- **Amazon FreeRTOS** - an OS for microcontrollers allowing small low power devices to connect to IoT

## Game Development
- **Amazon Gamelift** - deploy scale and manage game servers in the cloud
- **Amazon Lumberyard** - a game dev IDE and engine on the cloud

## Machine Learning

- **DeepLens** - a deep learning enable video camera with a deep learning SDk to create advanced vision system applications
- **SageMaker** - AWS' flagship machine learning product for building and trainging your own machine learning models, to be used as a backend for your own applications
- **Amazon Rekognition** - deep learning based analysis of video and images
- **Amazon Lex** - build conversational chat bots for many uses like first line support for customers
- **Amazon Polly** - natural sounding text to speech
- **Comprehend** - analyze text for insights and relationships for customer analysis
- **Translate** - translate text to different languages
- **Transcribe** - uses sound stored on S3 to transcribe to text

## Management and Governance

- **CloudFormation** - use a text file to define your infrastructure as code
- **Service Catalog** - allows organizations to catalog their services, manage permissions, and set constraints
- **CloudWatch** - a monitoring service for providing insight and triggering scaling
- **AWS Systems Manager** - gather operational data across services to shorten the detection of issues across services
- **CloudTrail** - monitors and logs AWS account activity across the console, SDK, and command line utilities
- **AWS Config** - a configuration/change management service
- **OpsWorks** - provides managed instances of Chef and Puppet
- **Trusted Advisor** - analyzes your services and advises on security and performance best practices

## Networking and Content Delivery

- **CloudFront** - CDN network that caches content on edge locations for fast delivery to end users, and protection from DDoS attacks
- **VPC Virtual Private Cloud** - provides a private space for your instances with security groups, routing, and subnets in multiple AZs
- **Direct Connect** - a private high-speed link to AWS from your data center
- **ELB Elastic Load Balancing** - distributes incoming traffic to multiple instances offering HA, redundancy, and fault-tolerance
- **Route 53** - AWS DNS service
- **API Gateway** - a fully managed scalable serverless service for developers to create and deploy APIs at any scale

## Security, Identity, and Compliance

- **AWS Artifact** - a portal to documentation on AWS security and compliance, readily available for auditing and compliance purposes
- **AWS Certificate Manager** - issues SSL certificates for HTTPS communication. It integrates with Route 53 and CloudFront
- **Amazon Cloud Directory** - a cloud directory service with hierarchies of data, unlike traditional LDAP which is limited to a single hierarchy
- **AWS Directory Service** - a fully managed Active Directory service
- **CloudHSM** - a dedicated hardware security module in the cloud
- **Cognito** - provides signin and signup capabilities for your web apps, can integrate with OAUTH and SAML providers like Google and Facebook
- **IAM Identity and Access Management** - manages user access to services and resources in your account.
- **AWS Organizations** - policy based mnanagement for multiple accounts, such as for large organizations using them
- **Amazon Inspector** - an automated security assessment service to identify potential security vulnerabilities
- **KMS Key Management Service** - a service to create and control keys for encrypted data, using HSM to secure your keys
- **AWS Shield** - protects against DDoS attacks, the standard version is automatically implemented on all AWS accounts
- **WAF Web Application Firewall** - provides additional protection for websites against SQL injection and cross site scripting. It has different sets of rules that can be used for different applications

## Storage

- **S3 Simple Storage Service** - designed to store any type of data. Sits on a serverless service entirely managed by AWS. Data sits in _Buckets_.
- **Glacier** - used for long term archiving of data. Less accessible, but less expensive.
- **EBS Elastsic Block Store** - low-latency, highly available block level storage, attached directly to instances
- **EFS Elastic File System** - network attached storage for EC2 instances. a datasource can be accessed by several instances (works like NFS)
- **Storage Gateway** - a bridge between on-prem storage and cloud storage. it caches frequently used data on-prem.
- **Snowball** - a portable, high capacity NAS-like device which is sent to company to load data onto it, then send the device back to AWS to load data to cloud storage.
