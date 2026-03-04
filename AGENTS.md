# AGENTS.md - Project-specific instructions

## Git Commit Prefixing
- All commits must be prefixed with `(AI based: <model-name>)`

## GitHub Actions
- Run on every push: `branches: ['**']`
- Include codecov upload

## PHP Version Compatibility
- Account for output format differences between PHP versions (e.g., var_export, __set_state)
- Write flexible assertions to handle PHP version-specific outputs

## Code Changes
- Don't remove comments unless they are clearly outdated
