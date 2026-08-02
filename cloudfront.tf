# CloudFront
resource "aws_cloudfront_distribution" "zerocla-cf" {

  enabled = true

  default_root_object = "index.html"

  aliases = [
    var.domain_name
  ]

  origin {
    domain_name              = aws_s3_bucket.zerocla-s3.bucket_regional_domain_name
    origin_id                = aws_s3_bucket.zerocla-s3.id
    origin_access_control_id = aws_cloudfront_origin_access_control.zerocla-cf-oac.id
  }

  default_cache_behavior {
    allowed_methods        = ["GET", "HEAD"]
    cached_methods         = ["GET", "HEAD"]
    target_origin_id       = aws_s3_bucket.zerocla-s3.id
    viewer_protocol_policy = "redirect-to-https"

    forwarded_values {
      query_string = false

      cookies {
        forward = "none"
      }
    }
  }

  restrictions {
    geo_restriction {
      restriction_type = "none"
    }
  }

  viewer_certificate {
    acm_certificate_arn      = aws_acm_certificate.zerocla-acm.arn
    ssl_support_method       = "sni-only"
    minimum_protocol_version = "TLSv1.2_2021"
  }

  tags = var.tags
}

resource "aws_cloudfront_origin_access_control" "zerocla-cf-oac" {

  name = "zerocla-oac"

  origin_access_control_origin_type = "s3"

  signing_behavior = "always"

  signing_protocol = "sigv4"

}

# ACM
resource "aws_acm_certificate" "zerocla-acm" {

  provider = aws.virginia

  domain_name = var.domain_name

  validation_method = "DNS"

}
