# 🌊 MyTask: ADHD-Focused Task Manager

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4e56a6?style=for-the-badge)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**MyTask** is a high-performance, modular task management web application meticulously designed for users with ADHD. It prioritizes low cognitive load, immediate task capture, and "Energy-Based" productivity to help you swim with the current, not against it.

---

## 🧠 ADHD-Centric Features

-   **⚡ The Brain Dump:** A zero-friction "Quick Add" modal. Capture your thoughts before they drift away, without losing your current flow.
-   **🔋 Energy Level Mapping:** Categorize tasks by "Brain Power" (1-5). Pick tasks that match your current mental battery, whether you're hyper-focused or winding down.
-   **🎯 Focus Mode:** A distraction-free UI that strips away navigation and sidebars, showing only your priority task and an integrated Pomodoro timer.
-   **💎 Dopamine Hits:** Instant visual feedback and Redis-powered streak tracking to provide the immediate gratification that ADHD brains crave.

---

## 🛠 Tech Stack

-   **Framework:** [Laravel 11](https://laravel.com)
-   **Architecture:** [Laravel Modules](https://github.com/nwidart/laravel-modules) (Domain-Driven Design approach)
-   **Frontend:** [TALL Stack](https://tallstack.dev/) (Tailwind CSS, Alpine.js, Laravel Livewire 3)
-   **Database:** PostgreSQL (Primary) & Redis (Real-time timers & Caching)
-   **Build Tool:** Vite

---

## 🚀 Getting Started

### Prerequisites

-   PHP 8.2+
-   [Laravel Herd](https://herd.laravel.com) (Recommended) or Docker
-   Node.js & NPM
-   Composer

### Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/faiqsyhmi/my-task.git
    cd my-task
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database & Migration:**
    Configure your database in `.env` and run:
    ```bash
    php artisan migrate
    ```

5.  **Run Development Server:**
    ```bash
    npm run dev
    ```

---

## 📂 Modular Structure

The project follows a modular structure to ensure high maintainability and isolation:

-   `Modules/Tasks`: Core CRUD, Category logic, and Energy Level scopes.
-   `Modules/Focus`: Alpine.js driven Focus Mode and Pomodoro logic.
-   `Modules/Analytics`: Redis-backed productivity stats and the "Dopamine Meter."

---

## � License

This project is open-source software licensed under the [MIT license](LICENSE).
