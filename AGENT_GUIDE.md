# Agent Guide & Development Workflow

This document outlines the **Standard Operating Procedure (SOP)** for developing and releasing themes for Dedeportes.cl.

## 1. Core Philosophy
*   **Git is Truth**: All changes must be committed.
*   **Tags are Releases**: Every functional version must be tagged (e.g., `modern-v1.14`).
*   **No Binaries**: Do NOT commit `.zip` files to the repository. They bloat coverage. Use `git rm --cached *.zip` if necessary.

## 2. Development Cycle

### A. Setup
1.  Pull latest changes: `git pull origin main`
2.  Read `task.md` to understand context.

### B. Implementation
1.  Modify files in `dedeportes-modern/`.
2.  **Bump Version**:
    *   `style.css`: Update `Version: X.Y` header.
    *   `functions.php`: Update `define('DEDEPORTES_VERSION', 'X.Y');`.

### C. Verification
1.  Check for lint errors.
2.  Verify `style.css` is valid.

## 3. Deployment & Delivery

### A. Commit & Push
```bash
git add .
git commit -m "feat: Describe changes clearly"
git push origin main
```

### B. Tagging (The Release)
After pushing, create a tag to mark the release version.
```bash
git tag -a modern-v1.14 -m "Release v1.14: Navigation refactor"
git push origin modern-v1.14
```

### C. Generate Artifact
Only after tagging, generate the zip for the user.
```powershell
Compress-Archive -Path dedeportes-modern -DestinationPath dedeportes-modern.zip -Force
```
*Notify the user of the zip location.*
