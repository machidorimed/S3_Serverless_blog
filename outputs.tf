output "dns_record" {

  value = aws_acm_certificate.zerocla-acm.domain_validation_options

}

output "cloudfront_domain_name" {

  description = "CloudFront distribution domain name"

  value = aws_cloudfront_distribution.zerocla-cf.domain_name

}

output "acm_certificate_arn" {

  description = "ACM certificate ARN used by the CloudFront distribution"

  value = aws_acm_certificate.zerocla-acm.arn

}
