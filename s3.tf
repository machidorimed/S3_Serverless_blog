# S3
resource "aws_s3_bucket" "zerocla-s3" {

  bucket = var.bucket_name

  tags = var.tags

}

resource "aws_s3_bucket_public_access_block" "zerocla-s3-block" {

  bucket = aws_s3_bucket.zerocla-s3.id

  block_public_acls       = true
  block_public_policy     = true
  ignore_public_acls      = true
  restrict_public_buckets = true

}

resource "aws_s3_bucket_policy" "zerocla-s3-policy" {

  bucket = aws_s3_bucket.zerocla-s3.id

  policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Sid       = "AllowCloudFrontServicePrincipal"
        Effect    = "Allow"
        Principal = { Service = "cloudfront.amazonaws.com" }
        Action    = "s3:GetObject"
        Resource  = "${aws_s3_bucket.zerocla-s3.arn}/*"
        Condition = {
          StringEquals = {
            "AWS:SourceArn" = aws_cloudfront_distribution.zerocla-cf.arn
          }
        }
      }
    ]
  })

}
