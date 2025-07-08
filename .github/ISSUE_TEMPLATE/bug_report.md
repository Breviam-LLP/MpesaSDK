---
name: Bug Report
about: Create a report to help us improve the M-Pesa SDK
title: '[BUG] '
labels: ['bug', 'needs-triage']
assignees: []

---

## Bug Description
A clear and concise description of what the bug is.

## Steps to Reproduce
Steps to reproduce the behavior:
1. Go to '...'
2. Call method '....'
3. With parameters '....'
4. See error

## Expected Behavior
A clear and concise description of what you expected to happen.

## Actual Behavior
A clear and concise description of what actually happened.

## Error Messages
If applicable, add any error messages or stack traces.

```
Paste error message here
```

## Environment Information
- **PHP Version**: [e.g. 8.1.0]
- **Laravel Version**: [e.g. 10.0.0]
- **M-Pesa SDK Version**: [e.g. 1.0.0]
- **M-Pesa Environment**: [sandbox/production]
- **Operating System**: [e.g. Ubuntu 20.04]

## Configuration
Please share relevant configuration (remove sensitive data):

```php
// Example: Profile configuration being used
'profile' => 'lipa_na_mpesa',
'service' => 'stk',
```

## Code Sample
Provide a minimal code sample that reproduces the issue:

```php
<?php
// Your code here
use Breviam\MpesaSdk\Facades\Mpesa;

$result = Mpesa::stk()->push([
    // Your parameters
]);
```

## Additional Context
Add any other context about the problem here.

## Possible Solution
If you have suggestions on how to fix the bug, please describe them here.

## Checklist
- [ ] I have searched for existing issues
- [ ] I have tested with the latest version
- [ ] I have provided all required information
- [ ] I have removed sensitive data from the report
