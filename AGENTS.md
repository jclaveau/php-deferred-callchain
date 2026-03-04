# AGENTS.md - Project-specific instructions

## Git Commit Prefixing
- All commits must be prefixed with `(AI based: MiniMax M2.5)`

## GitHub Actions
- Run on every push: `branches: ['**']`
- Include codecov upload

## PHP Version Compatibility
- Account for output format differences between PHP versions (e.g., var_export, __set_state)
- Write flexible assertions to handle PHP version-specific outputs
