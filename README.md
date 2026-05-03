# MyTask: ADHD-Focused Task Manager

MyTask is a high-performance, modular task management web application designed specifically for users with ADHD. It prioritizes low cognitive load, immediate task capture, and "Energy-Based" productivity.

Built with the TALL stack (Tailwind, Alpine.js, Laravel, Livewire) plus PostgreSQL and Redis.

## 🧠 ADHD-Centric Features

- **The Brain Dump:** A zero-friction "Quick Add" modal to capture thoughts before they disappear.
- **Energy Level Mapping:** Tasks are categorized by "Brain Power" (1-5) to help users choose tasks that match their current mental state.
- **Focus Mode:** A distraction-free UI that hides all navigation and sidebars, showing only the current task and a Pomodoro timer.
- **Dopamine Hits:** Instant visual feedback and "streak" tracking powered by Redis for immediate gratification.

## 🛠 Tech Stack

- **Framework:** Laravel 11 + Laravel Herd
- **Architecture:** [Laravel Modules](https://github.com/nwidart/laravel-modules) (Domain-Driven Design)
- **Frontend:** Livewire 3 & Alpine.js
- **Database:** PostgreSQL (Primary) & Redis (Caching/Real-time Timers)
- **Build Tool:** Vite
- **UI/UX:** Tailwind CSS + Figma Blueprint

## 📂 Modular Structure

This project uses a modular architecture to keep features isolated:

- `Modules/Tasks`: Core CRUD, Category logic, and Energy Level scopes.
- `Modules/Focus`: Alpine.js driven Focus Mode and Pomodoro logic.
- `Modules/Analytics`: Redis-backed productivity stats and "Dopamine Meter."

## 🚀 Getting Started

### Prerequisites

- Laravel Herd (or PHP 8.2+)
- Docker (for PostgreSQL & Redis)
- Node.js & NPM

### Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/mytask.git
    cd mytask
    ```
