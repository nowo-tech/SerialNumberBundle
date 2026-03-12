# Release

Maintainers: follow this checklist before creating a new tag.

## Pre-release checklist

1. **Update version and docs**
   - Ensure [CHANGELOG.md](CHANGELOG.md) has an entry for the new version (e.g. `[1.0.0] - YYYY-MM-DD`) and that `[Unreleased]` is updated or empty.
   - Ensure [UPGRADING.md](UPGRADING.md) mentions any behaviour changes for that version if needed.

2. **Run quality checks**

   ```bash
   make release-check
   ```

   This runs: `composer-sync`, `cs-fix`, `cs-check`, `rector-dry`, `phpstan`, `test-coverage`, and demo verification.

3. **Commit** any changes (e.g. changelog, version bumps). Ensure the tree is clean and pushed:

   ```bash
   git status
   git add -A && git commit -m "Release v1.0.0"   # if needed
   git push origin main
   ```

## Tag and publish

4. **Create an annotated tag** (example for v1.0.0). Ensure you have at least one commit (e.g. initial commit or release commit) before tagging:

   ```bash
   git tag -a v1.0.0 -m "Release v1.0.0"
   git push origin v1.0.0
   ```

   If the bundle is developed in a monorepo and released from a separate clone (e.g. `nowo-tech/serial-number-bundle`), run these commands in the clone that is pushed to the release remote.

5. **GitHub release**  
   Open [Releases](https://github.com/nowo-tech/serial-number-bundle/releases), draft a new release from tag `v1.0.0`, and paste the relevant section from [CHANGELOG.md](CHANGELOG.md) as the release notes.

6. **Packagist**  
   If the package is on [Packagist](https://packagist.org/packages/nowo-tech/serial-number-bundle), the new tag will be picked up automatically (or use “Update” there).
