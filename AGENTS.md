# AGENTS.md — Guide for AI Agents

> This file tells AI coding agents (Claude Code, Copilot, Cursor, etc.) how this
> project works, who the operator is, and how to behave. Place it at the root of
> your repository.

---

## About the operator

I am **not a traditional developer**. I manage production websites using AI
agents as my development team. I describe what I want in plain language and
expect the agent to interpret, propose, and execute.

- I may not know technical jargon. Explain things clearly when needed.
- If something I ask is a bad idea, say so directly and propose alternatives.
- Always propose before acting when there's more than one valid path.
- Prioritize working code first, then elegance — but flag technical debt.
- Remind me about security implications proactively.

---

## Infrastructure overview

### How deploys work

```
GitHub repo ──push──→ Cloudflare Pages ──auto build──→ Live site
```

1. Code lives in a **GitHub repository**.
2. **Cloudflare Pages** is connected to the repo and builds automatically on
   every push to `main`.
3. The build output (static files) is served globally via Cloudflare's CDN.
4. **There is no separate server.** The sites are static, built at deploy time.

### Cloudflare Pages projects

<!-- FILL IN: Replace with your actual projects -->

| CF Project name | GitHub repo | Domain | Build command | Output dir |
|-----------------|-------------|--------|---------------|------------|
| `my-site` | `username/my-site` | `example.com` | `npm run build` | `dist` |
| `my-blog` | `username/my-blog` | `blog.example.com` | `npm run build` | `dist` |

### Environment variables (Cloudflare Pages)

<!-- FILL IN: List the env vars each project needs (never the values) -->

Each project may require environment variables configured in Cloudflare Pages
dashboard (Settings > Environment variables). Common ones:

| Variable | Purpose | Example value |
|----------|---------|---------------|
| `NODE_VERSION` | Node.js version for build | `20` |
| `SITE_URL` | The production domain | `https://example.com` |

> **Never hardcode secrets in code.** API keys, tokens, and credentials go in
> environment variables or `.env` files (which must be in `.gitignore`).

### Domain setup

<!-- FILL IN: How are your domains configured? -->

- Domains are registered at: `[your registrar]`
- DNS is managed by: Cloudflare
- SSL: Automatic via Cloudflare (no manual certificate management needed)

---

## Git workflow

### Branches

- `main` is **always production**. Every push to `main` triggers a live deploy.
- **Never commit directly to `main`** unless it's a trivial one-file fix.
- Work in feature branches and merge via Pull Requests.

### Branch naming

```
feat/short-description     — new features
fix/short-description      — bug fixes
docs/short-description     — documentation changes
chore/short-description    — config, dependencies, tooling
```

### Commit messages

Use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add contact form to homepage
fix: broken link in footer navigation
docs: update deployment instructions
chore: upgrade dependencies
```

Rules:
- First line: **what** changed (max 72 characters)
- Body (optional): **why** it changed
- One logical change per commit
- Never commit `.env`, credentials, API keys, or debug logs

### Pull Request workflow

1. Create a branch: `git checkout -b feat/my-change`
2. Make commits on the branch
3. Push: `git push -u origin feat/my-change`
4. Create PR: `gh pr create --title "feat: my change" --body "What and why"`
5. Once approved/reviewed, merge to `main` (this triggers deploy)

---

## Commit authorship

- All commits must be authored by the repo owner.
- Do NOT add `Co-Authored-By` headers.
- Do NOT include `Generated with [Claude Code]` or similar AI attribution in
  commit messages.
- Do NOT include links to AI sessions in commit messages.

---

## Project structure

<!-- FILL IN: Describe your project structure -->

```
my-project/
├── src/              # Source code
├── public/           # Static assets (images, fonts)
├── dist/             # Build output (do not edit manually)
├── package.json      # Dependencies and scripts
├── .env.example      # Template for environment variables
├── .gitignore        # Files excluded from git
└── README.md         # Project documentation
```

---

## Common commands

<!-- FILL IN: Replace with your actual commands -->

```bash
npm install            # Install dependencies
npm run dev            # Start local development server
npm run build          # Build for production
npm run preview        # Preview production build locally
```

---

## What the agent should know

### The operator may ask you to:

- **Fix something broken**: "The site shows a blank page" or "The deploy failed."
  Always check the build logs first (`gh run list`, or ask for the Cloudflare
  Pages deployment log).
- **Change content**: "Update the about page text" or "Add a new blog post."
  Find the relevant source file, make the edit, and confirm the change.
- **Add a feature**: "Add a dark mode toggle" or "Add a contact form." Propose
  the approach before writing code.
- **Debug a deploy**: "Cloudflare says the build failed." Check the build
  command, output directory, Node version, and environment variables.
- **Update dependencies**: "Update everything." Run `npm update`, check for
  breaking changes, test the build.
- **Check security**: "Is this site secure?" Review `.gitignore`, exposed
  secrets, dependency vulnerabilities (`npm audit`).

### Common Cloudflare Pages issues

1. **Build fails**: Check `NODE_VERSION` env var, `build` command in
   `package.json`, and that all dependencies are in `package.json` (not just
   installed locally).
2. **404 after deploy**: Verify the output directory in Cloudflare matches the
   actual build output.
3. **Old version still showing**: Cloudflare caches aggressively. The deploy
   might still be propagating (usually < 2 minutes). Check the deployment status
   in the Cloudflare dashboard.
4. **Environment variables not working**: They must be set in the Cloudflare
   Pages project settings, not just in a local `.env` file. Changes require a
   new deployment to take effect.

### Cloudflare Pages API

If Cloudflare credentials are available in `.env` (`CF_ACCOUNT_ID`, `CF_API_KEY`,
`CF_EMAIL`), the agent can interact with the Cloudflare API:

```bash
# List deployments
curl -s "https://api.cloudflare.com/client/v4/accounts/$CF_ACCOUNT_ID/pages/projects/$PROJECT_NAME/deployments" \
  -H "X-Auth-Email: $CF_EMAIL" \
  -H "X-Auth-Key: $CF_API_KEY"

# Update environment variables
curl -s -X PATCH \
  "https://api.cloudflare.com/client/v4/accounts/$CF_ACCOUNT_ID/pages/projects/$PROJECT_NAME" \
  -H "X-Auth-Email: $CF_EMAIL" \
  -H "X-Auth-Key: $CF_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{"deployment_configs":{"production":{"env_vars":{"VAR_NAME":{"value":"value"}}}}}'
```

---

## Security rules

- **Never** commit `.env` files, API keys, tokens, or passwords.
- `.gitignore` must include: `.env`, `.env.local`, `node_modules/`, `dist/`,
  `.DS_Store`, `*.log`.
- Before every push, verify no sensitive files are staged: `git status`.
- Run `npm audit` periodically to check for vulnerable dependencies.
- If the operator asks to commit something that looks like a secret, **refuse
  and explain why**.

---

## Pre-deploy checklist

Before pushing to `main` (which triggers a live deploy):

1. Does it build? Run `npm run build` locally.
2. Are there any lint/type errors?
3. Is `.gitignore` covering sensitive files?
4. Are commit messages clean and descriptive?
5. Is the change tested (at least manually)?
6. No debug logs, `console.log`, or test data left behind?

---

## How to use this file

This file is read automatically by AI coding agents when they start working on
your project. To set it up:

1. Copy this file to the **root** of your GitHub repository as `AGENTS.md`.
2. Fill in the sections marked with `<!-- FILL IN -->`.
3. Delete the sections that don't apply to your setup.
4. Add any project-specific details (API integrations, special build steps,
   content structure).
5. Commit it: `git add AGENTS.md && git commit -m "docs: add agent guide"`

You can also create a `CLAUDE.md` file for instructions specific to Claude Code
(personal preferences, project philosophy, communication style).
