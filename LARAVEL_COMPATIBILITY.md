# Laravel Compatibility Guide

## Supported Versions

### ✅ Fully Supported

- **Laravel 11.x** (PHP 8.2+) - **Recommended**
  - All features work perfectly
  - Full test coverage
  - Complete CI/CD validation
  - Latest console architecture support

### ⚠️ Experimental Support

- **Laravel 10.x** (PHP 8.1, 8.2) - **Limited Support**
  - Basic functionality works
  - Some console commands may have compatibility issues
  - Tests marked as experimental in CI
  - Not recommended for production use with this SDK

### ❌ Not Supported

- **Laravel 9.x** - Deprecated due to fundamental compatibility issues
- **Laravel 8.x and below** - Not supported

## Known Issues

### Laravel 10 Compatibility

The SDK was designed primarily for Laravel 11, which introduced significant changes to the console architecture and service provider system. Laravel 10 compatibility is experimental because:

1. **Console Architecture Changes**: Laravel 11 uses `#[AsCommand]` attributes extensively, which may not be fully supported in Laravel 10.

2. **Service Provider Registration**: The new service provider registration system in Laravel 11 may cause issues in Laravel 10.

3. **Configuration System**: The profile-based configuration system relies on Laravel 11 features.

### Console Commands

Our custom console commands (`mpesa:config`, etc.) may not work properly in Laravel 10 due to:
- Different console application bootstrapping
- Attribute-based command registration differences
- Service container resolution changes

## Recommendations

### For New Projects
Use **Laravel 11** with **PHP 8.2** or **PHP 8.3** for the best experience.

### For Existing Laravel 10 Projects
- Consider upgrading to Laravel 11 if possible
- If you must use Laravel 10, thoroughly test all functionality
- Avoid using console commands until compatibility is verified
- Monitor the CI status for your specific use case

### For Laravel 9 Projects
Please upgrade to Laravel 11. Laravel 9 support has been discontinued.

## Testing Matrix

Our CI/CD pipeline tests the following combinations:

| PHP Version | Laravel Version | Status | Notes |
|-------------|-----------------|---------|-------|
| 8.2 | 11.x | ✅ Stable | Recommended |
| 8.3 | 11.x | ✅ Stable | Recommended |
| 8.1 | 10.x | ⚠️ Experimental | Limited support |
| 8.2 | 10.x | ⚠️ Experimental | Limited support |

## Migration Guide

### From Laravel 10 to Laravel 11

1. Follow the [official Laravel 11 upgrade guide](https://laravel.com/docs/11.x/upgrade)
2. Update your `composer.json`:
   ```json
   "require": {
     "laravel/framework": "^11.0",
     "breviam/mpesa-sdk": "^1.0"
   }
   ```
3. Run `composer update`
4. Test all M-Pesa functionality thoroughly

### Configuration Changes

No configuration changes are required when upgrading Laravel versions. The SDK's configuration system is forward-compatible.

## Support

- For Laravel 11 issues: Full support via GitHub issues
- For Laravel 10 issues: Limited support, upgrade to Laravel 11 recommended
- For Laravel 9 and below: Not supported, upgrade required

## Contributing

When contributing to this project:
- Primary development targets Laravel 11
- Laravel 10 compatibility PRs are welcome but marked experimental
- All new features must work with Laravel 11
- Tests for Laravel 10 are optional and marked as experimental
