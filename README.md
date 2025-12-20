<div align="center">
  <img src="public/assets/logos/TaskForgeGreen.svg" alt="TaskForge Logo" width="160"/>

  <h1>TaskForge</h1>

  <p><strong>Forge clarity. Build momentum.</strong></p>
</div>

---

## 🚀 About TaskForge

**TaskForge** is a full-featured project management web application designed to help individuals and teams **plan, organize, and collaborate** efficiently.

It brings together task tracking, milestones, real-time communication, file sharing, and role-based collaboration into a **single, clean workspace**.

TaskForge focuses on **clarity**, **accountability**, and **momentum** — so everyone always knows:
- what needs to be done
- who is responsible
- and what comes next

---

## ✨ Core Features

### 🗂 Project Management
- Create and manage multiple projects
- Assign project owners and members
- Role-based access (Owners vs Members)

### ✅ Task Management
- Create, edit, and assign tasks
- Track task status and progress
- Set deadlines and priorities
- Visual task boards (Kanban-style)

### 🧩 Milestones & Progress
- Break projects into milestones
- Track long-term goals visually
- Monitor overall project progress

### 💬 Real-Time Project Chat
- Project-specific chat rooms
- Real-time message updates
- File and image attachments
- Clear message ownership and timestamps

### 📁 File Management
- Upload and share files within chats
- Image previews and downloadable attachments
- Secure file storage per project

### 👥 Team Collaboration
- Invite members to projects
- Manage members from the project settings
- Owner-only management controls

### 🌗 Dark Mode
- System-wide dark mode support
- Persistent theme using local storage
- Seamless UI transitions

---

## 🧭 User Guide

### 1️⃣ Getting Started
1. Visit the TaskForge homepage
2. Register a new account
3. Log in to access your dashboard
4. Create your first project

### 2️⃣ Creating a Project
- Click **Create Project**
- Set a project name
- You become the project owner automatically

### 3️⃣ Managing Tasks
- Add tasks inside a project
- Assign tasks to members
- Update task status as work progresses

### 4️⃣ Using Project Chat
- Open the **Chat** tab inside a project
- Send messages in real time
- Attach files or images
- Messages update automatically without refreshing

### 5️⃣ Managing Team Members
- Owners can add or remove members
- Members can collaborate but cannot manage settings
- Owners have exclusive access to management tabs

### 6️⃣ Profile & Account
- Update personal details
- Change password
- Delete account if needed

---

## 🔍 Feature Details

### 🔐 Role-Based Access
| Role   | Permissions |
|------|-------------|
| Owner | Full control (settings, members, delete project) |
| Member | Tasks, chat, files |

### ⚡ Real-Time Updates
- Chat messages load automatically via polling
- No page refresh required
- Smooth and responsive UX

### 🎨 Modern UI
- Glassmorphism cards
- Subtle gradients and animations
- Fully responsive layout

---

## ❓ Frequently Asked Questions (FAQs)

### ❔ Who can manage a project?
Only the **project owner** can manage settings, members, and delete the project.

### ❔ Can members chat and upload files?
Yes. All project members can chat and share files.

### ❔ Is dark mode supported?
Yes. Dark mode can be toggled and is saved across sessions.

### ❔ Are files stored securely?
Yes. Files are stored per project and only accessible to members.

### ❔ Can I use TaskForge alone?
Absolutely. TaskForge works great for both solo and team projects.

---

## 🛠 Installation Guide

## ✅ Prerequisites
Make sure you have the following installed:
- **PHP 8.1+**
- **Composer**
- **Node.js & npm**
- **MySQL / SQLite**
- **Laravel CLI (optional)**

## 📦 Clone the Repository
```bash
git clone https://github.com/your-username/taskforge.git
cd taskforge
```

## 📥 Install PHP Dependencies
```bash
composer install
```

## 📥 Install Frontend Dependencies
```bash
nmp install
```

## ⚙️ Environment Setup
Copy the example environment file:
```bash
cp .env.example .env
```

Generate the application key:
```bash
php artisan key:generate
```

## 🗄 Configure Database
Edit .env and set your database credentials:
```bash
DB_DATABASE=taskforge
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:
```bash
php artisan migrate
```

(Optional) Seed data:
```bash
php artisan db:seed
```

## 🎨 Build Frontend Assets:
```bash
npm run build
```

For development:
```bash
npm run dev
```

## ▶️ Run the Application:
```bash
php artisan serve
```

Open your browser:
```bash
http://127.0.0.1:8000
```

## 👩‍💻 Developer:
**Shakif Niaz**
ID: 232-134-040
Batch: 5th
Department: Software Engineering
Project: TaskForge

---

<div align="center"> <strong>TaskForge — Forge clarity. Build momentum.</strong> </div>
