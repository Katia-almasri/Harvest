# 🏠 Harvest – Fractional Real Estate Management System

**Harvest** is a full-stack backend platform built with **Laravel** to manage fractional ownership in real estate assets.  
The system enables property owners and investors to share ownership of a property and receive rental income based on their share percentage.

It also features a secure internal transaction mechanism to handle rent distributions and share transfers.

---

## 📦 Features

- 🧾 **Ownership Ledger** to track fractional shares per user
- 💸 **Automated Rent Distribution** based on ownership percentage
- 🔄 **Internal Share Transfer System**
- 🗂️ **Asset Management**: add/update/delete properties and ownership info
- 🔐 **User Roles**: Admin, Owner, Investor
- 📊 **Rent Logs & History** with transaction records
- 🧩 Modular and extendable structure

---

## 🛠️ Tech Stack

- **Framework**: Laravel (PHP)
- **Database**: MySQL
- **Queue System**: Laravel Jobs / Events / Event-driven architecture
- **Authentication**: Laravel Sanctum (or custom session-based)
- **Additional Tools**: Artisan Console Commands, Eloquent Models, Laravel Seeders

---

## 📂 Project Structure (Simplified)
```bash
harvest/
│
├── app/
│ ├── Models/
│ ├── Http/Controllers/
│ ├── Jobs/
│ ├── Events/
│ ├── Listeners/
│ └── Services/
│
├── routes/
│ └── web.php
│
├── database/
│ ├── migrations/
│ ├── seeders/
│ └── factories/
│
└── config/

---
```
---

## 🧠 Key Logic – Ownership Ledger

The system keeps a live ledger that maps each user's ownership percentage of a property.

When rent income is received:
1. A `RentDistributionJob` is triggered.
2. The job calculates each owner's share.
3. Internal `Transaction` records are created.
4. Balances are updated accordingly.

---

## 🧪 Example Use Cases

- Add a new property and assign owners with different share percentages.
- Receive rent income and distribute it automatically.
- Transfer ownership from one investor to another.
- Audit rental history per user.

---

## 🚀 Demo / Deployment

This project is not publicly deployed for security reasons.  
A code walkthrough or demo access can be provided upon request.

---

## 📫 Author

**Katia Almasri**  
Backend Developer – Laravel | Payments | SaaS Platforms  
📧 katiaalmasri2@gmail.com  
🌐 [GitHub](https://github.com/Katia-almasri) | [LinkedIn](https://linkedin.com/in/katia-al-masri)

---

## 📝 License

MIT – Open-source for learning and personal use.


