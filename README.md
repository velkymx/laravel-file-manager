Here’s the updated **README.md** with the **CC BY-NC-SA 4.0** license details:

---

## **File Manager Application**

This is a Laravel-based file management system that supports user authentication, folder navigation, file uploads, previews, and admin-specific functionality for managing users and groups.

---

### **Requirements**

- PHP 8.1 or higher
- Composer 2.x
- Node.js 16.x or higher
- NPM 7.x or higher
- MariaDB or MySQL database

---

### **Setup Instructions**

#### **1. Clone the Repository**

```bash
git clone <repository-url>
cd file-manager
```

Replace `<repository-url>` with your repository URL.

---

#### **2. Install PHP Dependencies**

Ensure Composer is installed on your system, then run:

```bash
composer install
```

---

#### **3. Install JavaScript Dependencies**

Ensure Node.js and NPM are installed on your system, then run:

```bash
npm install
```

---

#### **4. Configure Environment**

1. Copy the `.env.example` file to create a `.env` file:

   ```bash
   cp .env.example .env
   ```

2. Update the `.env` file with your database credentials and other environment settings. For example:

   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=file_manager
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. Set the application key:

   ```bash
   php artisan key:generate
   ```

---

#### **5. Run Migrations and Seeders**

Set up the database schema and seed initial data:

```bash
php artisan migrate --seed
```

---

#### **6. Compile Frontend Assets**

Build CSS and JavaScript assets:

```bash
npm run dev
```

For production builds, use:

```bash
npm run build
```

---

#### **7. Start the Development Server**

Run the Laravel development server:

```bash
php artisan serve
```

By default, the application will be available at [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

### **Additional Notes**

#### **Admin Access**
To access admin features, create an admin user or update an existing user in the database:

1. Log in to your database management tool (e.g., phpMyAdmin, MySQL CLI).
2. Update the `is_admin` field of a user in the `users` table:

   ```sql
   UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
   ```

#### **Run Tests**
To ensure everything is working, you can run the test suite:

```bash
php artisan test
```

#### **Clear Caches**
If you encounter any issues, clear the application cache:

```bash
php artisan optimize:clear
```

---

### **Common Commands**

- **Run migrations**: `php artisan migrate`
- **Rollback migrations**: `php artisan migrate:rollback`
- **Seed the database**: `php artisan db:seed`
- **Run the server**: `php artisan serve`
- **Compile assets**: `npm run dev` or `npm run build`

---

### **License**

This project is licensed under the **Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)**.
