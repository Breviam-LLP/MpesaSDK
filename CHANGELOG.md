# Changelog

All notable changes to `breviam/mpesa-sdk` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Profile-based configuration system with inheritance support
- Comprehensive Git workflow and version control setup
- GitHub Actions CI/CD pipeline
- Pre-commit and commit message validation hooks
- Configuration validation command (`mpesa:config-status`)
- Enhanced callback URL management system
- Comprehensive development documentation

### Changed
- **BREAKING**: Refactored configuration structure to use profile-based system
- Reduced configuration redundancy by 67%
- Enhanced BaseService with profile resolution and validation
- Improved STK service to use configured transaction type
- Restructured `.env.example` with comprehensive documentation

### Removed
- Unused `MPESA_TILL` configuration and all references
- Redundant credential definitions across services
- Malformed configuration comments

### Fixed
- Configuration validation and error handling
- Service-specific credential resolution
- Callback URL fallback mechanism

## [1.0.0] - 2025-07-08

### Added
- Initial release of M-Pesa Laravel SDK
- STK Push (Lipa na M-Pesa Online) integration
- C2B (Customer to Business) payments support
- B2C (Business to Customer) payments support
- B2B (Business to Business) payments support
- Transaction Reversal functionality
- Account Balance inquiry
- Comprehensive webhook handling with automatic route registration
- OAuth token management with automatic caching
- Laravel Facade support for easy usage
- Artisan commands for testing and debugging:
  - `mpesa:token` - Manage access tokens
  - `mpesa:simulate-payment` - Simulate C2B payments in sandbox
- Event-driven architecture for webhook handling
- Comprehensive logging with sensitive data masking
- Full test coverage with PHPUnit
- PSR-4 autoloading compliance
- Support for Laravel 9.x, 10.x, and 11.x
- Support for PHP 8.1+

### Security
- Sensitive data masking in logs
- Secure OAuth token caching
- HTTPS enforcement for all API calls
- Webhook request logging with IP tracking

### Documentation
- Comprehensive README with usage examples
- Inline code documentation
- Configuration examples
- Error handling examples
