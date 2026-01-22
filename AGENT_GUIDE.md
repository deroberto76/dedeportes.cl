# Agent Guide & Development Workflow

This document outlines the standard operating procedures for developing and releasing themes for Dedeportes.cl.

## Core Directives

1.  **Version Control First**: All changes must be tracked in Git.
2.  **Semantic Versioning**: Use standard version numbers (v1.0.0, v1.1.0) in both file headers and git tags.
3.  **Clean Repository**: Binaries (.zip) should not be committed to the repo. Use releases/tags for that.

## Step-by-Step Workflow

### 1. Creating/Modifying a Theme
- Navigate to the relevant directory (e.g., `dedeportes-modern`).
- Ensure `style.css` and `functions.php` have matching version numbers.
- **Cache Busting**: If changing CSS/JS, ALWAYS increment the version constant in `functions.php` to bypass browser cache.

### 2. Committing Changes
- Stage changes: `git add .`
- Commit with a descriptive message:
    ```bash
    git commit -m "Feat: Add hamburger menu support"
    # or
    git commit -m "Fix: Inline JS logic in footer"
    ```

### 3. Releasing & Tagging
When a feature set is complete and verified:

1.  **Create a Tag**: Use the pattern `theme-name-vX.Y`.
    ```bash
    git tag -a modern-v1.4 -m "Release v1.4: Inline JS fix"
    ```
2.  **Push Code & Tags**:
    ```bash
    git push origin main --tags
    ```

### 4. Handoff
- Always leave the repository in a clean state (`git status` should be clean).
- Inform the user of the new version tag.

## Useful Commands
- Check status: `git status`
- View logs: `git log --oneline --graph --decorate --all`
