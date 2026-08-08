# Deployment using GitHub

This repository can be deployed using GitHub for source control and GitHub Actions for build/publish automation.

## GitHub deployment flow

1. Push the repository to GitHub.
2. Use the existing GitHub Actions workflow at `.github/workflows/laravel-ci.yml` to run tests and verify the application.
3. Use the Docker publish workflow added below to push a container image to GitHub Container Registry.

## What was added

- `.github/workflows/laravel-ci.yml` — installs dependencies, generates `.env`, creates an SQLite DB, runs migrations, and verifies routes.
- `.github/workflows/docker-publish.yml` — builds and pushes a Docker image to GitHub Container Registry on `main`.
- `Dockerfile` — builds the Laravel application into a PHP-FPM container image.
- `.dockerignore` — keeps build context clean.

## How to publish the Docker image

GitHub Actions will publish the container image automatically on pushes to `main`.

The image name is:

```text
ghcr.io/${{ github.repository_owner }}/citinet-billing:latest
```

For private repositories, GitHub Container Registry can still be used with the built-in `GITHUB_TOKEN`.

## Free hosting options

After the image is published, you can deploy it to any free container-friendly host:

- Fly.io (free app allowance)
- Railway free tier
- Render free tier
- Oracle Cloud Always Free VM

## Production-ready checklist

- Set `APP_ENV=production`
- Set `APP_DEBUG=false`
- Provide `APP_KEY` as a secret, not in Git
- Configure `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Configure `PAYSTACK_PUBLIC_KEY` and `PAYSTACK_SECRET_KEY`
- Configure mail settings for `MAIL_MAILER`
- Ensure `storage/` and `bootstrap/cache/` are writable by PHP
- Point the web server to `public/`

## Local development with GitHub

If you want true GitHub-driven deployment, use GitHub for SCM, GitHub Actions for CI, and a free host for runtime. The app is now ready for that path.
