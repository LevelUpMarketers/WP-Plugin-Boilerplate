# AGENTS

Welcome to the Codex Plugin Boilerplate repository.

## Development Workflow

1. Run syntax checks on all PHP files:
   ```bash
   find . -name "*.php" -not -path "*/vendor/*" -print0 | xargs -0 -n1 php -l
   ```
2. If adding JavaScript or CSS, ensure files are linted if linters are available.
3. Update `DEV_DIARY.md` with a new numbered, timestamped entry after every change.
4. Update `README.md` when plugin usage changes.

## Notes
- Keep code modular and translation ready.
- Custom database table prefix is `cpb_`.
- Remove `includes/update-server/` before submitting to WP.org.
