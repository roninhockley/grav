---
visible: true
title: AWS Cloud Practitioner
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,aws]
---

This page contains skills at the Certified Cloud Practitioner Level.
[toc]

# The AWS CLI

The AWS CLI is one of several methods for communicating with the AWS API.

The AWS API can be accessed via the _web console_, _CLI_, _SDKs_ or other AWS services.

There are many SDKs for various programming languages to make calls to the API from code. Even if there is not an SDK for a given language, you could still make direct HTTP/S calls to the AWS API.

API calls must use valid security credentials.
- **access key ID and secret** - download and use for using the CLI
- **IAM temporary credentials** for using SDKs. External users can use external apps by authenticating via SDK, to an OAUTH service to gain access

API Access can be logged via the CloudTrail service.

For help on the CLI go to https://docs.aws.amazon.com/cli

# AWS Architecting using the AWS Architecture Center

The AWS [Architecture Center](https://aws.amazon.com/architecture) has numerous resources for how to design and implement turn-key solutions for stakeholders using best practices.

- **AWS reference architectures** - datasheets with diagrams and basic descriptions of each service for high level architectural guidance
- **AWS Quick Starts** - automated deployments for fully functional software using modular, customizable CloudFormation templates
- **AWS Well-Architected** - a framework for building with best practices built around 5 pillars of excellence
  - This links to the **AWS Well Architected Tool** which analyzes your workloads against AWS best practices.

To target an architecture to a specific standard such as HIPAA, go to [AWS Compliance] (https://aws.amazon.com/compliance). In addition to compliance info there are deployment quick starts for compliance within a given standard.
