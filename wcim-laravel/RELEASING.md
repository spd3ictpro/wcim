# Releasing WCIM

## Prerequisites

- Push access to `spd3ictpro/wcim`
- All changes committed and pushed to `master`

## Release Steps

1. **Update version**

   Edit `.env` and bump `NATIVEPHP_APP_VERSION`:

   ```bash
   NATIVEPHP_APP_VERSION=1.1.0
   ```

2. **Update CHANGELOG.md** with the new version and changes.

3. **Commit and tag**

   ```bash
   git add .env CHANGELOG.md
   git commit -m "Release v1.1.0"
   git tag v1.1.0
   git push origin master --tags
   ```

4. **Test the build locally (optional)**

   ```bash
   composer run bundle
   ```

   This runs `npm run build` then `php artisan native:bundle`, producing the installer in `dist/`. Install the `.exe` on a test machine to verify everything works before pushing the tag.

5. **CI builds the release**

   The `Build & Deploy` workflow on GitHub Actions will:

   - Run on `windows-latest`
   - Install dependencies
   - Run `php artisan native:bundle` to produce the NSIS installer
   - Create a **draft** GitHub Release with the `.exe` attached

6. **Test the draft release**

   - Download the `.exe` from the draft release
   - Install on a test Windows 11 machine
   - Verify:
     - App launches and loads SQLite database
     - Existing data is preserved (if upgrading from a previous version)
     - All features work (inventory, indents, analytics, backup, stock receive)

7. **Publish the release**

   - Go to `https://github.com/spd3ictpro/wcim/releases`
   - Edit the draft release, add release notes
   - Click **Publish release**

8. **Deployed clients auto-update**

   Once published, all deployed WCIM instances will detect the new version and prompt the user to update.

## Versioning

Use semantic versioning:

| Change | Example |
|--------|---------|
| Bug fix (patch) | `1.0.0` → `1.0.1` |
| New feature (minor) | `1.0.0` → `1.1.0` |
| Breaking change (major) | `1.0.0` → `2.0.0` |

## Rolling Back

If a release has issues:

1. Create a new tag from the previous working commit: `git tag v1.0.1-fix && git push origin v1.0.1-fix`
2. The CI will build and draft a release
3. Test, then publish

Clients on the broken version will see the fix as an available update.
