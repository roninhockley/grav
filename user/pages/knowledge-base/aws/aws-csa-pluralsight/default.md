---
visible: true
title: AWS CSA - Architecting for Reliability
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,aws]
---
[toc]

### The solution being build in this course
A wordpress app spanning multiple AZ. redundant and resilient across data centers using app servers on an auto scaling group:

![](./3.png)

**guide for well architected framework**

[https://aws.amazon.com/architecture/well-architected](https://aws.amazon.com/architecture/well-architected)

Key concepts:
- availability and fault tolerance
- reliable virtual networks
- multi-tier application
- deployment automation
- multi-region solution

## Key Concepts and Core Services
- IAM 
- audit logging with CloudTrail
- Alerts with CloudWatch
- DDoS protection with Shield
- Change Management with Config
- managing service limits

### IAM

The **principal** could be a user or app which passes thru authentication with either user/pw or programmatic keys. 

Once this passes, it is on to a request for an action or a resource. The request is driven by API. 

Either command line, or the web interface uses the same API.

Once the request is received, it is authorized via **access policy** assigned directly to a user, a group, or a role which an app could assume, to perform the actions or access the resources in the request.



