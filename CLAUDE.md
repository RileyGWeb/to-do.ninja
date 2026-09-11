# to-do.ninja

## AWS access
This app is deployed to the `sandbox` AWS account (956315450098), on a single shared EC2 instance (`sandbox-apps-host`) alongside 4 other small apps (ChoreChart, dont-atrophy, PollsAndForms.com, memorize-your-scripture).

- For AWS CLI commands, always use the `sandbox-admin` profile explicitly: `aws <command> --profile sandbox-admin --region us-east-1`.
- This profile uses IAM Identity Center SSO. If a command fails with an expired-token error, run `aws sso login --profile sandbox-admin` (opens a browser to re-approve).
- There is no SSH access to the host — it's reached only via AWS Systems Manager: `aws ssm start-session --target <instance-id> --profile sandbox-admin` (find the instance id via `aws ec2 describe-instances --filters "Name=tag:Name,Values=sandbox-apps-host" --profile sandbox-admin`).
- Secrets (the production `.env`) live in AWS Secrets Manager at `sandbox/apps/todoninja/env`, not in this repo.

## Deployment
- Production domain: **to-do.ninja**
- Push to `main` triggers `.github/workflows/deploy.yml`, which assumes `arn:aws:iam::956315450098:role/github-actions-sandbox-deploy` via GitHub OIDC (no stored AWS credentials in this repo) and runs `/opt/apps/scripts/deploy-todoninja.sh` on the host via SSM `AWS-RunShellScript`.
- Backend runs against a shared MySQL container on the host (database `todo_ninja`, not a dedicated RDS instance).
- No background queue workers or scheduled jobs — this app doesn't use them.
- The frontend build uses Next.js `output: 'standalone'`, so its production image is a lighter multi-stage build than the other apps on this box.
