# GitHub Workflows

This directory contains GitHub Actions workflows for automating releases and deployments.

## 📦 Available Workflows

### `release.yml` - Automated Release and S3 Deployment

**Triggers:** Push to `main` or `master` branch

**What it does:**
1. ✅ Extracts version from `digital-employee-addon-wc-subscriptions.php`
2. ✅ Checks if version tag already exists
3. ✅ Creates new git tag (e.g., `v1.0.0`)
4. ✅ Builds production zip file (excludes dev files)
5. ✅ Uploads to Amazon S3
6. ✅ Creates GitHub Release with download links

## 🚀 Quick Start

1. **Set up AWS credentials** - See [SETUP.md](./SETUP.md) for detailed instructions
2. **Update plugin version** in `digital-employee-addon-wc-subscriptions.php`
3. **Commit and push** to main branch
4. **Workflow runs automatically** - Check Actions tab

## 📋 Required Secrets

Add these in GitHub repository settings:
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_REGION`
- `S3_BUCKET`
- `S3_BUCKET_URL` (optional)

## 📖 Documentation

- [Complete Setup Guide](./SETUP.md)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

## 🔧 Version Format

Use semantic versioning: `MAJOR.MINOR.PATCH` (e.g., `1.0.0`, `1.2.3`)

Update both places in the main plugin file:
```php
* Version: 1.0.0  // Plugin header
define( 'DE_ADDON_WC_SUBSCRIPTIONS_VERSION', '1.0.0' );  // Constant
```

## 📦 Download Locations

After successful deployment, files are available at:
- **Versioned:** `s3://bucket/digital-employee-addon-wc-subscriptions/digital-employee-addon-wc-subscriptions-v1.0.0.zip`
- **Latest:** `s3://bucket/digital-employee-addon-wc-subscriptions/digital-employee-addon-wc-subscriptions-latest.zip`
- **GitHub Release:** Attached to the release on GitHub

## ⚠️ Important Notes

- Tags are created automatically - don't create them manually
- Only unique version numbers will trigger releases
- Pushing the same version again will skip release creation
- All development files are automatically excluded from the zip

## 🆘 Support

For workflow issues, check:
1. Actions tab for detailed logs
2. Verify secrets are configured correctly
3. Ensure version format is correct
4. Review [SETUP.md](./SETUP.md) for troubleshooting tips
