# Contributing to M-Pesa Laravel SDK

Thank you for considering contributing to the M-Pesa Laravel SDK! This document outlines the guidelines for contributing to this project.

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code.

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When creating a bug report, include:

- A clear and descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- Laravel and PHP versions
- Any relevant error messages or logs

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- A clear and descriptive title
- A detailed description of the enhancement
- Use cases and examples
- Why this enhancement would be useful

### Pull Requests

Please follow our [Git Workflow](GIT_WORKFLOW.md) for detailed guidelines. Quick steps:

1. **Fork** the repository
2. **Create** a feature branch following our naming convention:
   ```bash
   git checkout develop
   git checkout -b feature/descriptive-name
   ```
3. **Make** your changes following our coding standards
4. **Add** tests for your changes
5. **Ensure** all tests pass:
   ```bash
   composer test
   # or
   vendor/bin/phpunit
   ```
6. **Update** documentation if needed
7. **Commit** your changes using conventional commit format:
   ```bash
   git commit -m 'feat(scope): add some amazing feature'
   ```
8. **Push** to the branch:
   ```bash
   git push origin feature/descriptive-name
   ```
9. **Open** a Pull Request using our PR template

### Commit Message Format

We use [Conventional Commits](https://www.conventionalcommits.org/). See [GIT_WORKFLOW.md](GIT_WORKFLOW.md) for detailed guidelines.

Examples:
- `feat(stk): add timeout configuration for STK push`
- `fix(auth): resolve token caching issue`
- `docs: update configuration guide`

### Development Setup

1. Clone the repository:
```bash
git clone https://github.com/breviam/mpesa-sdk.git
cd mpesa-sdk
```

2. Install dependencies:
```bash
composer install
```

3. Run tests:
```bash
composer test
```

4. Run code style checks:
```bash
composer format
```

### Coding Standards

- Follow PSR-12 coding standards
- Use meaningful variable and method names
- Add proper type hints
- Document all public methods
- Write tests for new functionality

### Testing

- All new features must include tests
- Maintain or improve test coverage
- Use meaningful test names
- Mock external API calls in tests

### Documentation

- Update README.md for new features
- Add inline documentation for complex code
- Update CHANGELOG.md following the format
- Include usage examples where appropriate

### Git Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line

### Development Guidelines

#### Adding New M-Pesa APIs

When adding support for new M-Pesa APIs:

1. Create a new contract interface in `src/Contracts/`
2. Implement the service in `src/Services/`
3. Add the service to `MpesaService` and the facade
4. Update the service provider bindings
5. Add comprehensive tests
6. Update documentation

#### Error Handling

- Use specific exception types
- Provide meaningful error messages
- Include context information in exceptions
- Log errors appropriately

#### Configuration

- Add new configuration options to `config/mpesa.php`
- Use environment variables for sensitive data
- Provide sensible defaults
- Document all configuration options

### Release Process

Releases follow semantic versioning:

- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality additions  
- **PATCH** version for backwards-compatible bug fixes

### Questions?

If you have questions about contributing, please:

1. Check the existing documentation
2. Search existing issues
3. Create a new issue with the "question" label
4. Contact the maintainers

## Thank You!

Your contributions help make this package better for everyone. We appreciate your time and effort!
