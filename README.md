# CEMS - Construction ERP Management System

![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Laravel](https://img.shields.io/badge/Laravel-12.0-red)
![License](https://img.shields.io/badge/license-MIT-green)

## 📖 Overview

CEMS (Construction ERP Management System) is a comprehensive enterprise resource planning system designed specifically for construction and contracting companies. It manages projects, contracts, tenders, financials, procurement, HR, and more.

## ✨ Features

### Core Modules
- 🏗️ **Project Management**: Projects, WBS, BOQ, Progress tracking
- 📄 **Contract Management**: Contract lifecycle, amendments, claims
- 📋 **Tender Management**: Bid preparation, submission, evaluation
- 💰 **Financial Management**: AR, AP, GL, Cost control
- 🛒 **Procurement**: Purchase requisitions, orders, receipts
- 👥 **HR & Payroll**: Employees, attendance, leave, payroll
- 📊 **Reporting**: 50+ built-in reports and dashboards
- 🔐 **Multi-tenancy**: Support for multiple companies

### Advanced Features
- Change Order Management (FIDIC compliant)
- IPC (Interim Payment Certificate) processing
- Risk management and mitigation
- Document management with versioning
- Site diary and daily reports
- Quality inspections and punch lists
- Asset management and depreciation
- Bank reconciliation
- Multi-currency support

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Node.js 18+
- Git

### Installation

1. Clone the repository:
```bash
git clone https://github.com/ALNSOUR0790820045/CEMS.git
cd CEMS
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cems_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. Run migrations and seeders:
```bash
php artisan migrate --seed
```

6. Build assets:
```bash
npm run build
```

7. Start the server:
```bash
php artisan serve
```

Visit: http://localhost:8000

### Default Login
```
Email: admin@example.com
Password: password
```

## 📖 Documentation

- [API Documentation](docs/API.md)
- [User Guide](docs/USER_GUIDE.md)
- [Developer Guide](docs/DEVELOPER_GUIDE.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Module Documentation](docs/MODULES.md)

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📊 Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Database**: MySQL 8.0
- **Frontend**: Blade, Alpine.js, Tailwind CSS
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Multi-tenancy**: Stancl Tenancy
- **PDF Generation**: DomPDF
- **Excel**: PhpSpreadsheet
- **Testing**: PHPUnit

## 🤝 Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel community
- All contributors
- Open source libraries used

## 📞 Support

For support, email support@example.com or open an issue on GitHub.

## 🗺️ Roadmap

- [ ] Mobile app (iOS/Android)
- [ ] Advanced BI dashboards
- [ ] Real-time collaboration
- [ ] WhatsApp/SMS notifications
- [ ] Integration with accounting software
- [ ] IoT device integration

---

Made with ❤️ for construction industry
