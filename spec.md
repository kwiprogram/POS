# ☕ Coffee POS System
## Software Requirement Specification (SPEC)

---

# Project Information

## Project Name
Coffee POS System

## Version
1.0.0

## Author
Kwi

## Technology Stack

### Backend
- Laravel 12+
- PHP 8.3+
- PostgreSQL

### Frontend
- Blade Template
- Bootstrap 5 (or Tailwind CSS)
- JavaScript
- AJAX

### Database
- PostgreSQL

### Authentication
- Laravel Authentication

---

# Project Overview

Coffee POS System is a Point of Sale application for coffee shops.

The system allows employees to manage:

- Coffee menu
- Categories
- Customer orders
- Payments
- Receipts
- Daily sales
- Users

The objective is to make ordering faster, reduce human error, and generate sales reports automatically.

---

# User Roles

## Admin

Can:

- Login
- Dashboard
- Manage Users
- Manage Coffee Menu
- Manage Categories
- Manage Tables
- View Orders
- Update Orders
- Delete Orders
- View Reports
- View Revenue
- System Settings

---

## Cashier

Can:

- Login
- Dashboard
- Create Order
- Update Order
- Cancel Order
- Process Payment
- Print Receipt
- View Today's Orders

Cannot:

- Delete Users
- Access System Settings

---

# Main Modules

---

## 1. Authentication

Features

- Login
- Logout
- Remember Me
- Password Validation
- Role Permission

---

## 2. Dashboard

Display

- Today's Revenue
- Total Orders
- Best Selling Coffee
- Total Customers
- Pending Orders
- Completed Orders

Charts

- Daily Sales
- Weekly Sales
- Monthly Sales

---

## 3. Category Management

Fields

- ID
- Name
- Description
- Status
- Created At

Functions

- Create
- Update
- Delete
- Search

---

## 4. Coffee Menu

Fields

- ID
- Category
- Coffee Name
- Image
- Price
- Description
- Status
- Stock
- Created At

Functions

- Add Coffee
- Edit Coffee
- Delete Coffee
- Search
- Filter Category

---

## 5. Table Management

Fields

- Table Number
- Capacity
- Status

Status

- Available
- Occupied
- Reserved

Functions

- Add Table
- Edit Table
- Delete Table

---

## 6. Customer

Fields

- ID
- Name
- Phone
- Member Status

Functions

- Register
- Search
- Update

---

## 7. Order

Fields

- Order Number
- Table
- Customer
- Cashier
- Date
- Status

Status

- Pending
- Preparing
- Ready
- Completed
- Cancelled

Functions

- New Order
- Add Item
- Remove Item
- Update Quantity
- Cancel Order
- Complete Order

---

## 8. Order Detail

Fields

- Coffee
- Quantity
- Price
- Discount
- Subtotal

Automatically Calculate

Subtotal

```
Price × Quantity
```

---

## 9. Payment

Fields

- Order
- Total
- Discount
- Tax
- Grand Total
- Payment Method
- Amount Paid
- Change

Payment Method

- Cash
- Credit Card
- ABA KHQR
- ACLEDA
- Wing

Automatically Calculate

```
Grand Total

=

Subtotal

-

Discount

+

Tax
```

```
Change

=

Amount Paid

-

Grand Total
```

---

## 10. Receipt

Contains

- Coffee Shop Name
- Address
- Phone
- Order Number
- Cashier
- Date
- Ordered Items
- Total
- Payment Method
- Amount Paid
- Change

Can

- Print
- Download PDF

---

## 11. Sales Report

Reports

Daily

Weekly

Monthly

Yearly

Show

- Revenue
- Orders
- Best Seller
- Total Customers

Export

- PDF
- Excel

---

## 12. User Management

Fields

- Name
- Email
- Password
- Role
- Status

Roles

- Admin
- Cashier

Functions

- Create
- Update
- Delete
- Reset Password

---

# Database Design

## users

- id
- name
- email
- password
- role
- created_at
- updated_at

---

## categories

- id
- name
- description
- status
- created_at
- updated_at

---

## coffees

- id
- category_id
- name
- image
- description
- price
- stock
- status
- created_at
- updated_at

---

## customers

- id
- name
- phone
- member_status
- created_at
- updated_at

---

## tables

- id
- table_number
- capacity
- status
- created_at
- updated_at

---

## orders

- id
- order_number
- customer_id
- table_id
- user_id
- total
- payment_status
- order_status
- created_at
- updated_at

---

## order_items

- id
- order_id
- coffee_id
- quantity
- price
- subtotal
- created_at
- updated_at

---

## payments

- id
- order_id
- payment_method
- amount_paid
- total
- change
- paid_at

---

# Relationships

Category

```
1 ---- * Coffee
```

Customer

```
1 ---- * Orders
```

Table

```
1 ---- * Orders
```

User

```
1 ---- * Orders
```

Order

```
1 ---- * Order Items
```

Coffee

```
1 ---- * Order Items
```

Order

```
1 ---- 1 Payment
```

---

# Validation Rules

Category Name

Required

Maximum 100 characters

---

Coffee Name

Required

Maximum 150 characters

---

Price

Required

Numeric

Greater than 0

---

Stock

Required

Integer

Minimum 0

---

Customer Phone

Optional

Must be unique

---

Payment

Amount Paid

Cannot be less than Grand Total

---

# Search

Search Coffee

- Name
- Category

Search Orders

- Order Number
- Customer
- Date

Search Customer

- Name
- Phone

---

# Filter

Coffee

- Category
- Available
- Out of Stock

Orders

- Pending
- Completed
- Cancelled

Reports

- Daily
- Weekly
- Monthly
- Yearly

---

# Non-functional Requirements

- Responsive Design
- Fast Loading
- Secure Authentication
- CSRF Protection
- SQL Injection Protection
- Input Validation
- Error Handling
- PostgreSQL Support
- Clean MVC Architecture

---

# Future Features

- Barcode Scanner
- Kitchen Display System (KDS)
- Inventory Management
- Supplier Management
- Expense Tracking
- Employee Attendance
- Loyalty Points
- Coupon System
- Online Ordering
- QR Table Ordering
- Telegram Notifications
- Email Receipt
- Dark Mode
- Multi-Branch Support

---

# Development Roadmap

Phase 1
- Authentication
- Dashboard
- Category
- Coffee Menu

Phase 2
- Customer
- Table
- Order
- Payment

Phase 3
- Receipt
- Reports
- User Management

Phase 4
- Inventory
- Analytics
- QR Ordering
- Multi-Branch

---

# End of Specification