# Nanaska Present

A Laravel 10 application for uploading and evaluating presentations with Vue.js frontend and FilamentPHP admin panel.

## Features

- **User Registration & Authentication**: Secure user registration and login using Laravel Breeze with Vue.js/Inertia.js
- **Presentation Upload**: Users can upload presentation files (PDF, PPT, PPTX)
- **Video/Audio Capture**: Record video and audio directly from the browser or upload media files
- **FilamentPHP Admin Panel**: Admin-only interface for managing users, presentations, and evaluations
- **Presentation Evaluation**: Admins can create evaluation prompts and score presentations with detailed feedback
- **Evaluation Results**: Users can view their presentation evaluation results and scores

## Requirements

- PHP 8.1+
- Composer
- Node.js 18+
- SQLite/MySQL/PostgreSQL

## Installation

1. Clone the repository:
```bash
git clone https://github.com/voxsar/nanaska-present.git
cd nanaska-present
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Copy the environment file and generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env` file. For SQLite:
```
DB_CONNECTION=sqlite
```

Create the SQLite database file:
```bash
touch database/database.sqlite
```

6. Run migrations and seed the database:
```bash
php artisan migrate
php artisan db:seed
```

7. Create the storage symbolic link:
```bash
php artisan storage:link
```

8. Build frontend assets:
```bash
npm run build
```

9. Start the development server:
```bash
php artisan serve
```

## Default Admin Credentials

- Email: `admin@example.com`
- Password: `password`

Access the admin panel at: `/admin`

## Usage

### For Users

1. Register a new account or login
2. Navigate to "Presentations" in the navigation menu
3. Click "Upload New Presentation"
4. Fill in the title and description
5. Upload a presentation file (PDF, PPT, PPTX)
6. Record or upload video/audio of your presentation
7. Submit and wait for admin evaluation

### For Administrators

1. Login with admin credentials
2. Access the admin panel at `/admin`
3. Manage users, presentations, and evaluations
4. Create evaluation prompts for presentations
5. Provide scores and feedback for each presentation

## Development

Start the Vite development server for hot module replacement:
```bash
npm run dev
```

Run tests:
```bash
php artisan test
```

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3 with Inertia.js
- **Admin Panel**: FilamentPHP 3
- **Styling**: Tailwind CSS
- **Build Tool**: Vite

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
