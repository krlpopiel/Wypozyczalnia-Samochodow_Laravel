# Car Rental System (Wypożyczalnia Samochodów)

A web-based application for managing a car rental business. The system facilitates the process of renting vehicles, managing the car fleet, and handling customer reservations. It is built using the **Laravel** framework, ensuring a robust backend and an MVC architecture.

## 📖 About the Project

This project is a web application designed to handle the core operations of a car rental company. It provides distinct interfaces for customers (to browse and rent cars) and administrators/employees (to manage the fleet and rentals).

## ✨ Key Features

### For Clients

* **Car Browsing:** View available cars with details (brand, model, price, description).
* **Rent a Car:** Simple booking process for selected vehicles.
* **User Account:** Registration and login functionality.
* **Rental History:** View past and current rentals.

### For Administrators / Employees

* **Fleet Management (CRUD):** Add, edit, and delete vehicles from the database.
* **Rental Management:** Approve reservations, track car status (available/rented).
* **Client Management:** View registered users and their details.
* **Dashboard:** Overview of current system status.

## 🛠️ Tech Stack

* **Backend:** PHP, Laravel Framework
* **Frontend:** Blade Templates, Bootstrap (HTML/CSS/JS)
* **Database:** MySQL
* **Environment:** Composer, Apache/Nginx

## 🚀 Installation & Setup

To run this project locally, follow these steps:

### Prerequisites

* PHP (v8.x recommended)
* Composer
* MySQL Database
* Node.js & NPM (optional, for compiling assets)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/krlpopiel/Wypozyczalnia-Samochodow_Laravel.git
cd Wypozyczalnia-Samochodow_Laravel

```


2. **Install PHP dependencies**
```bash
composer install

```


3. **Configure the environment**
Duplicate the `.env.example` file and rename it to `.env`:
```bash
cp .env.example .env

```


Open the `.env` file and configure your database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

```


4. **Generate Application Key**
```bash
php artisan key:generate

```


5. **Run Migrations (and Seeds)**
Create the database tables:
```bash
php artisan migrate
# If the project has seeders for default data/admin:
# php artisan migrate --seed

```


6. **Install Frontend Dependencies (Optional)**
```bash
npm install
npm run dev

```


7. **Run the Server**
Start the local development server:
```bash
php artisan serve

```


The application will be available at `http://127.0.0.1:8000`.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome! Feel free to check the [issues page](https://www.google.com/search?q=https://github.com/krlpopiel/Wypozyczalnia-Samochodow_Laravel/issues).

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👤 Author

**Karol Popiel**

* GitHub: [@krlpopiel](https://github.com/krlpopiel)

