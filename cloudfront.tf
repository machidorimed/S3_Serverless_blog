# CloudFront
resource "aws_cloudfront_distribution" "zerocla-cf" {

  enabled = true

  default_root_object = "index.html"

  is_ipv6_enabled = true

  # コンソールで自動作成された既存のWAF Web ACL (手動設定、Terraformでは新規作成しない)
  web_acl_id = "arn:aws:wafv2:us-east-1:398692021932:global/webacl/CreatedByCloudFront-541322f4/36398912-8108-4bd2-aeb1-3f89b5d61674"

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
    compress               = true

    # AWSマネージドポリシー "Managed-CachingOptimized"
    cache_policy_id = "658327ea-f89d-4fab-a63d-7e88639e58f6"

    function_association {
      event_type   = "viewer-request"
      function_arn = aws_cloudfront_function.rewrite_index.arn
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
    minimum_protocol_version = "TLSv1.3_2025"
  }

  tags = merge(var.tags, {
    Name = "zerocla-cf-distribution"
  })
}

# 末尾スラッシュ・拡張子なしURLに index.html を補完する
# (S3 REST APIオリジンはS3の静的サイトホスティングと違い、ルート以外で自動補完しないため)
resource "aws_cloudfront_function" "rewrite_index" {
  name    = "zerocla-rewrite-index"
  runtime = "cloudfront-js-1.0"
  publish = true
  code    = file("${path.module}/functions/rewrite-index.js")
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

  tags = var.tags
}
