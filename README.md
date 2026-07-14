# Laravel Project

This README provides instructions for setting up and running the Laravel project.

## Prerequisites

Make sure you have the following installed on your system:
- PHP
- Composer
- Node.js and npm
- MySQL or another compatible database

## Installation Steps

1. Clone the repository:
   ```
   git clone [your-repository-url]
   cd [your-project-name]
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install Vite:
   ```
   npm install vite
   ```

4. Install Puppeteer:
   ```
   npm install puppeteer
   ```

5. Set up environment file:
   ```
   cp .env.example .env
   ```

6. Generate application key:
   ```
   php artisan key:generate
   ```

7. Configure your database in the `.env` file.

8. Run database migrations:
   ```
   php artisan migrate
   ```

9. Seed the database:
   ```
   php artisan db:seed
   ```

## Running the Application

1. Start the Laravel development server:
   ```
   php artisan serve
   ```

2. In a separate terminal, compile and watch for asset changes:
   ```
   npm run dev
   ```

Visit `http://localhost:8000` in your browser to see the application.

Username and password

Admin :
admin@gmail.com
admin123

Karyawan :
karyawan@gmail.com
karyawan
