variable "bucket_name" {
  description = "S3 bucket name for the site content"
  type        = string
}

variable "domain_name" {
  description = "Domain name served via CloudFront / ACM"
  type        = string
}

variable "tags" {
  description = "Common tags applied to resources"
  type        = map(string)
}
