# Version Control & Git Workflow

This document outlines the version control practices and Git workflow for the M-Pesa SDK project.

## Table of Contents

- [Branching Strategy](#branching-strategy)
- [Commit Message Format](#commit-message-format)
- [Pull Request Process](#pull-request-process)
- [Release Management](#release-management)
- [Git Hooks](#git-hooks)
- [CI/CD Pipeline](#cicd-pipeline)
- [Development Setup](#development-setup)

## Branching Strategy

We follow a **Git Flow** inspired branching strategy:

### Main Branches

- **`main`** - Production-ready code, protected branch
- **`develop`** - Integration branch for features, default branch for PRs

### Supporting Branches

- **`feature/*`** - New features (`feature/stk-timeout-config`)
- **`bugfix/*`** - Bug fixes (`bugfix/auth-token-cache`)
- **`hotfix/*`** - Critical production fixes (`hotfix/security-patch`)
- **`release/*`** - Release preparation (`release/v1.2.0`)

### Branch Naming Conventions

```bash
# Features
feature/descriptive-name
feature/stk-push-enhancements
feature/profile-inheritance

# Bug fixes
bugfix/issue-description
bugfix/auth-token-expiry
bugfix/config-validation

# Hot fixes
hotfix/critical-issue
hotfix/security-vulnerability

# Releases
release/v1.2.0
release/v2.0.0-beta
```

## Commit Message Format

We use **Conventional Commits** specification:

```
<type>(<scope>): <description>

[optional body]

[optional footer(s)]
```

### Types

- **feat** - New feature
- **fix** - Bug fix
- **docs** - Documentation changes
- **style** - Code style changes (formatting, etc.)
- **refactor** - Code refactoring
- **test** - Adding or updating tests
- **chore** - Maintenance tasks
- **perf** - Performance improvements
- **ci** - CI/CD changes
- **build** - Build system changes
- **revert** - Revert previous commit

### Scopes

- **auth** - Authentication service
- **stk** - STK Push service
- **c2b** - Customer to Business
- **b2c** - Business to Customer
- **b2b** - Business to Business
- **balance** - Balance inquiry
- **reversal** - Transaction reversal
- **config** - Configuration system
- **docs** - Documentation
- **tests** - Test files

### Examples

```bash
feat(stk): add timeout configuration for STK push requests
fix(auth): resolve token caching issue in production environment
docs: update installation guide with Laravel 11 support
refactor(config): implement profile-based credential system
test(stk): add comprehensive validation for STK push parameters
chore(deps): update PHPUnit to version 10.5
```

### Setup Commit Message Template

```bash
git config commit.template .gitmessage
```

## Pull Request Process

### 1. Create Feature Branch

```bash
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name
```

### 2. Make Changes

- Write clean, well-documented code
- Add/update tests for your changes
- Update documentation if needed
- Follow the coding standards

### 3. Commit Changes

```bash
git add .
git commit -m "feat(scope): your descriptive commit message"
```

### 4. Push and Create PR

```bash
git push origin feature/your-feature-name
```

Create a Pull Request on GitHub:
- Use the PR template
- Fill out all required sections
- Request appropriate reviewers
- Link related issues

### 5. Review Process

- All PRs require at least one review
- CI/CD pipeline must pass
- No unresolved conversations
- Code quality checks must pass

### 6. Merge

- Use "Squash and merge" for feature branches
- Use "Create a merge commit" for release branches
- Delete feature branch after merge

## Release Management

### Version Numbering

We follow [Semantic Versioning](https://semver.org/):

- **MAJOR.MINOR.PATCH** (e.g., 1.2.3)
- **MAJOR** - Breaking changes
- **MINOR** - New features (backward compatible)
- **PATCH** - Bug fixes (backward compatible)

### Release Process

1. **Create Release Branch**
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b release/v1.2.0
   ```

2. **Prepare Release**
   - Update version in `composer.json`
   - Update `CHANGELOG.md`
   - Update documentation
   - Run final tests

3. **Create Release PR**
   ```bash
   git push origin release/v1.2.0
   # Create PR: release/v1.2.0 → main
   ```

4. **Tag Release**
   ```bash
   git checkout main
   git pull origin main
   git tag -a v1.2.0 -m "Release version 1.2.0"
   git push origin v1.2.0
   ```

5. **Merge Back to Develop**
   ```bash
   git checkout develop
   git merge main
   git push origin develop
   ```

### Hotfix Process

1. **Create Hotfix Branch**
   ```bash
   git checkout main
   git pull origin main
   git checkout -b hotfix/critical-fix
   ```

2. **Make Fix and Test**
   ```bash
   git commit -m "fix: critical security vulnerability"
   ```

3. **Create PR to Main**
   ```bash
   git push origin hotfix/critical-fix
   # Create PR: hotfix/critical-fix → main
   ```

4. **Tag and Merge Back**
   ```bash
   # After merge to main
   git checkout main
   git pull origin main
   git tag -a v1.2.1 -m "Hotfix version 1.2.1"
   git push origin v1.2.1
   
   # Merge to develop
   git checkout develop
   git merge main
   git push origin develop
   ```

## Git Hooks

We use Git hooks to maintain code quality:

### Pre-commit Hook

Automatically runs before each commit:
- PHP syntax validation
- PHPUnit tests
- Check for debugging statements
- Validate configuration files

### Commit Message Hook

Validates commit message format:
- Ensures conventional commit format
- Checks message length
- Provides helpful error messages

### Installation

Hooks are automatically installed in `.git/hooks/` when you clone the repository.

To manually install:
```bash
chmod +x .git/hooks/pre-commit
chmod +x .git/hooks/commit-msg
```

## CI/CD Pipeline

Our GitHub Actions pipeline includes:

### On Pull Request
- **Tests** - Multiple PHP/Laravel versions
- **Code Quality** - Syntax, standards, debugging checks
- **Security** - Composer audit
- **Documentation** - Validate required files

### On Main Branch Push
- All PR checks
- **Auto-tagging** - Creates tags for new versions
- **Release Notes** - Automatically generated

### On Release
- **Package Building** - Prepare distribution
- **Documentation Deployment** - Update docs site

## Development Setup

### 1. Clone Repository
```bash
git clone https://github.com/breviam/mpesa-sdk.git
cd mpesa-sdk
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Git Configuration
```bash
# Set commit message template
git config commit.template .gitmessage

# Set your identity
git config user.name "Your Name"
git config user.email "your.email@example.com"

# Setup useful aliases
git config alias.co checkout
git config alias.br branch
git config alias.ci commit
git config alias.st status
git config alias.unstage 'reset HEAD --'
git config alias.last 'log -1 HEAD'
git config alias.visual '!gitk'
```

### 4. Verify Setup
```bash
# Test pre-commit hook
git add .
git commit -m "test: verify git hooks setup"

# If everything works, amend or reset
git reset --soft HEAD~1
```

## Best Practices

### Commits
- Make atomic commits (one logical change per commit)
- Write descriptive commit messages
- Commit frequently, push when feature is complete
- Never commit sensitive data or credentials

### Branches
- Keep branches focused and short-lived
- Delete merged feature branches
- Regularly sync with develop/main
- Use descriptive branch names

### Pull Requests
- Keep PRs focused and small
- Provide thorough descriptions
- Include tests for new features
- Update documentation

### Code Quality
- Run tests before pushing
- Fix linting/style issues
- Remove debugging statements
- Add inline documentation

## Troubleshooting

### Common Issues

**Hook Permission Denied**
```bash
chmod +x .git/hooks/pre-commit
chmod +x .git/hooks/commit-msg
```

**Tests Failing in Hook**
```bash
composer install
vendor/bin/phpunit
```

**Commit Message Rejected**
Follow the conventional commit format:
```bash
git commit -m "feat(stk): add new feature description"
```

**Merge Conflicts**
```bash
git status
# Edit conflicted files
git add .
git commit
```

## Resources

- [Conventional Commits](https://www.conventionalcommits.org/)
- [Semantic Versioning](https://semver.org/)
- [Git Flow](https://nvie.com/posts/a-successful-git-branching-model/)
- [GitHub Flow](https://guides.github.com/introduction/flow/)

---

For questions or issues with the Git workflow, please create an issue or contact the maintainers.
