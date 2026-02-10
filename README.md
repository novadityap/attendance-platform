# Employee Attendance Platform

A full-stack platform for managing employee attendance, featuring authentication, role-based access control, shift management, time tracking (check-in/check-out), and an admin dashboard for real-time monitoring.

---

## 🚀 Tech Stack

### Backend
- **Laravel** — PHP web framework for building RESTful API
- **PostgreSQL** — Relational database with structured schema design
- **Authentication** — JSON Web Token (JWT)

### Frontend
- **Next.js** — Fullstack React framework
- **Redux Toolkit & RTK Query** — State and API management
- **shadcn/ui** — Modern UI components built on top of Tailwind CSS

---

## 🧰 Getting Started (Development)

### Prerequisites
- Docker

### Setup Steps

1. **Clone the repository:**

   ```bash
   git clone https://github.com/novadityap/attendance-platform.git
   cd attendance-platform
   ```

2. **Prepare environment variables:**

   Make sure `.env` files exist in both:

   ```
   ./server/.env
   ./client/.env.development
   ```

   (You can create them manually or copy from `.env.example` if available.)

3. **Start the application:**

   ```bash
   docker compose -f docker-compose.development.yml up -d --build
   ```

4. **Access URLs:**
   - Frontend: [http://localhost:3000](http://localhost:3000)
   - Backend API: [http://localhost:8000/api](http://localhost:8000/api)

---

## 🔐 Default Admin Account

To access the admin dashboard, use the following credentials:

- **Email:** `admin@email.com`
- **Password:** `admin123`

---

## 🧼 Maintenance

- **View container logs:**

  ```bash
  docker compose -f docker-compose.development.yml logs -f
  ```

- **Stop and remove containers, networks, and volumes:**

  ```bash
  docker compose -f docker-compose.development.yml down -v
  ```

---
