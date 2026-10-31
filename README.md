# Campus Scheduling System

A Laravel-based application for managing campus schedules, user roles (Students, Teachers, Secretaries, Admin), and registration.

## Features
* Role-based access control
* Scheduling management
* User registration and authentication

## Temporary Render test deployment

1. Push this repository to GitHub.
2. In Render, choose **New + > Blueprint**, connect the repository, and apply the `render.yaml` blueprint. It creates a web service and PostgreSQL database.
3. After the first deploy, open the web service's environment settings and set `APP_URL` to the service's public `https://...onrender.com` URL, then redeploy.
4. Open the public URL and register a test account.

The container builds the Vite assets and runs pending migrations on startup. It does not seed demo users. Do not run `migrate:fresh` against a database you want to keep. This setup is for temporary testing, not production; the free database may be limited or expire, and file-based sessions are lost when the service restarts.