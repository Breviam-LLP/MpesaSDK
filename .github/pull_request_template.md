## Description
Brief description of the changes introduced by this PR.

## Type of Change
Please mark the relevant option:

- [ ] 🐛 Bug fix (non-breaking change which fixes an issue)
- [ ] ✨ New feature (non-breaking change which adds functionality)
- [ ] 💥 Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] 📚 Documentation update
- [ ] 🎨 Code style/formatting changes
- [ ] ♻️ Refactoring (no functional changes)
- [ ] ⚡ Performance improvement
- [ ] 🧪 Test updates
- [ ] 🔧 Build/CI changes

## Related Issues
Fixes # (issue number)
Closes # (issue number)
Related to # (issue number)

## Changes Made
List the specific changes made in this PR:

- [ ] Change 1
- [ ] Change 2
- [ ] Change 3

## Configuration Changes
If this PR includes configuration changes, please describe them:

- [ ] New environment variables added
- [ ] Configuration structure changes
- [ ] Profile changes
- [ ] Service mappings updated

## Testing
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
- [ ] I have tested this with both sandbox and production configurations (if applicable)

## Testing Instructions
Steps for reviewers to test the changes:

1. Step 1
2. Step 2
3. Step 3

## Screenshots/Examples
If applicable, add screenshots or code examples to help explain your changes.

```php
<?php
// Example usage of new feature
use Breviam\MpesaSdk\Facades\Mpesa;

$result = Mpesa::service()->method([
    'parameter' => 'value'
]);
```

## Backwards Compatibility
- [ ] This change is backwards compatible
- [ ] This change requires migration/upgrade steps
- [ ] This is a breaking change

If breaking change, please describe the migration path:

## Documentation
- [ ] I have updated the README.md
- [ ] I have updated CONFIGURATION.md
- [ ] I have updated inline code documentation
- [ ] I have updated CHANGELOG.md

## Security Considerations
- [ ] This change doesn't introduce security vulnerabilities
- [ ] Sensitive data is properly handled
- [ ] API credentials are not exposed in logs

## Performance Impact
- [ ] No performance impact
- [ ] Minimal performance impact
- [ ] Significant performance improvement
- [ ] Potential performance concerns (please explain)

## Checklist
- [ ] My code follows the project's coding style
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
- [ ] Any dependent changes have been merged and published

## Additional Notes
Any additional information that reviewers should know about this PR.
