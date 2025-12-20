Capstone Project Report
Software Requirements Specification

Project Title:
Development of an Online Shipping Management Website

2.1. Overview 
This chapter outlines the requirements for the "Development of an Online Shipping Management Website" project. It includes a feasibility study, elicitation techniques, use case analysis, and a detailed Software Requirements Specifications (SRS) document. The goal is to define the functional and non-functional requirements that will guide the design and implementation phases.

2.2. Feasibility study
A feasibility study evaluates the practicality of the proposed system by analyzing technical, economic, and operational aspects.
2.2.1 Operational Feasibility Study
The system will improve shipping operations by automating order management, tracking, and container handling. It’s easy to use and aligns with business goals, helping reduce errors and improve customer satisfaction.
2.2.2 Technical Feasibility Study
Laravel and HTML/CSS provide a strong, scalable foundation. The system can securely handle online payments and support future features like API integration. Hosting and maintenance requirements are manageable. Database Management Systems: e.g. SQL Server
2.2.3 Legal Feasibility
The system must comply with financial regulations, data protection laws, and anti-fraud measures. Secure payment gateways, user data encryption, and clear privacy and terms policies are essential.

2.3. List of the stakeholders
End-Users: 
Libyan Merchants: Create orders, track shipments, and make payments via the platform.

Admin Staff: Manage orders, pricing, trips, and system monitoring (backend operations).

Service Providers:
Logistics Teams (Internal): Handle manual shipment updates (e.g., status changes like "Shipped" or "Delivered").
Payment Gateway Providers:  Enable online transactions (limited to available Libyan services).

Technical Dependencies:
Web Hosting/Cloud Service Providers: Ensure platform accessibility and scalability.

Elicitation techniques
Interviews: Direct feedback from merchants.
Observation: Study of current manual logistics processes.



System scenario
user registration and login:
a merchant begins by filling out a registration form with their details. After email verification, they can log in and access their dashboard. When creating a shipping order, the merchant enters shipment details, including recipient information, package dimensions, and preferred container type. The system calculates the cost, generates a tracking number, and submits the order for admin approval.
Administrators review and approve orders:
 based on available capacity, Approved orders move to the shipping queue, while rejected ones notify the merchant with a reason. Merchants can track shipments in real-time, viewing status updates such as "Loaded," "Shipped," or "Delivered." Payments are processed through integrated local gateways, and upon successful payment, the system generates a shipping label.
Admins manage shipping routes:
 adding new schedules and updating transit details. They also manually update shipment statuses (e.g., marking a package as "Loaded"), which triggers notifications to merchants. Finally, merchants can access shipping history, filtering past orders and reviewing tracking details for record-keeping.





Use case tables
Table 2.6.1 User Account Management
Use Case ID:	UC-01
Actor:	Merchant, Admin
Description:	Allows users to create accounts and log in with role-based access.
Preconditions:		None for registration; valid credentials required for login.
"
Main Scenario:"	"User selects "Register" or "Login."
System displays the respective form.
User enters details (email, password, role).
System validates input.
If valid, account is created/login is granted.
System redirects to the dashboard."
Alternative Scenarios:	Invalid credentials → System displays error.
Postconditions:	"New account created (if registration).
User authenticated (if login)."



Table 2.6.2 Create Shipping Order
Use Case ID:	UC-02
Actor:	Merchant
Description:	Merchant creates a shipping order with item details, container size, and destination.
Preconditions:		Merchant is logged in.""
Main Scenario:"	"Merchant selects "Create Order."
System displays an order form.
Merchant enters details (item, container size, destination).
System calculates estimated cost.
Merchant confirms.
System generates an order ID."
Alternative Scenarios:	Missing required fields → System highlights errors.
Postconditions:	"Order is saved in the system.
Order status set to "Pending.""







Table 2.6.3 Track Shipment
Use Case ID:	UC-03
Actor:	Merchant
Description:	Merchant views real-time status of orders via dashboard.
Preconditions:		 Merchant is logged in; orders exist.
Main Scenario:	"Merchant selects "Track Shipment."
System displays active orders.
Merchant selects an order.
System shows current status (e.g., "Shipped")."
Alternative Scenarios:	 No orders exist → System displays empty state.
Postconditions:	Merchant views updated shipment status.









Table 2.6.4 Make Payment
Use Case ID:	UC-04
Actor:	Merchant
Description:	Merchant pays for shipping via integrated gateways.
Preconditions:		 Merchant has pending orders; payment gateway is available.
Main Scenario:	"Merchant selects "Pay Now" on an order.
System redirects to payment gateway.
Merchant completes payment.
System confirms success."
Alternative Scenarios:	 Payment fails → System retries or alerts support.
Postconditions:	Order status updated to "Paid."


Table 2.6.5 Manage Container Types (Admin)
Use Case ID:	UC-05
Actor:	Admin
Description:	 Admin adds/updates container types and sizes.
Preconditions:		 Admin is logged in.
Main Scenario:	"Admin navigates to "Container Management."
System displays existing containers.
Admin edits/adds new types.
System saves changes."
Alternative Scenarios:	 Invalid input → System rejects changes.
Postconditions:	Container options updated.









Table 2.6.6 Update Shipment Status (Admin)
Use Case ID:	UC-06
Actor:	Admin
Description:	Admin manually updates order status (e.g., "Loaded").
Preconditions:		 Admin is logged in; orders exist.
Main Scenario:	"Admin selects an order.
System shows current status.
Admin selects new status (e.g., "Shipped").
System updates order."
Alternative Scenarios:	 Invalid status transition → System blocks update.
Postconditions:	Order status updated.



Non-Functional Requirements (NFR):
Performance
NFR-01: The system shall load dashboard pages within 2 seconds under normal load.
NFR-02: The system shall support at least 500 concurrent users without degradtion in performance.
Security
NFR-04: The system shall comply with Libyan data protection laws and financial regulations.
NFR-05: The system shall enforce HTTPS for all communications.
Usability
NFR-06: The system shall provide an intuitive UI with clear navigation for merchants and admins
NFR-07: The system shall be available in Arabic and English.
Reliability & Availability
NFR-08: The system shall achieve 99.5% uptime (excluding scheduled maintenance).
NFR-09: The system shall log errors and recover gracefully from crashes.
Scalability
NFR-10: The system shall be deployable on cloud infrastructure to handle future growth.
Compatibility
NFR-11: The system shall support Chrome, Firefox, and Edge (latest versions).

Overall Description:

Product Perspective
The Online Shipping Management system is a standalone web application with the future potential to integrate with larger logistics platforms and ERP systems.

Product Functions:
Order Management: Merchants can create and manage shipping orders.
Payment Processing: Secure online payment integration.
Admin Dashboard: Admins can manage orders, containers, routes, and users.
Container Management: Admins can define and price different container types.
Reporting & Analytics: Admins can view reports on shipping volumes and revenue.

User Characteristics:
Merchants (Users): Libyan business owners with basic computer skills. Prefer a simple, Arabic-first interface.
Administrators: Logistics company staff with higher technical proficiency. Use the system for daily operations and management.
Constraints:
The system relies on a stable internet connection.
Payment functionality is dependent on the availability and API stability of local Libyan payment gateways.
Assumptions and Dependencies:
Merchants have access to the internet and an email address.
Admins will manually input shipping status updates.
The initial version will focus on major Libyan cities.
Apportioning of Requirements:
Future versions may include:
Automated SMS notifications.
Mobile application.
Advanced analytics and predictive features.
Integration with international shipping carriers.

 Specific Requirements:

External Interface Requirements:
User Interfaces: Responsive web design accessible on desktop and mobile devices.
Software Interfaces: Integration with payment gateway APIs (e.g., local Libyan banks).
 Performance Requirements:
The system should support 50+ concurrent users.
Page load times should be under 3 seconds.
The payment process should be completed within 30 seconds.
 Software System Attributes:
Reliability: Core features like order creation and payment must be highly reliable.
Availability: The system should aim for 99% uptime.
Security: User data and payment information must be encrypted. Role-based access control is mandatory.
Maintainability: The system will be built using the Laravel MVC framework for clean, modular, and maintainable code.






Wireframes:

https://www.figma.com/design/76NshPLZG5hXbELOPkJOFm/project-wireframe?node-id=0-1&t=iClfslZRcNgIoAPM-1



## Database Schema:

### Users Table
- id (Primary Key)
- name
- email (unique)
- email_verified_at
- password
- role (enum: 'merchant', 'admin')
- phone
- address
- company_name
- remember_token
- created_at
- updated_at

### Routes Table
- id (Primary Key)
- origin (varchar)
- destination (varchar)
- schedule (enum: 'weekly', 'biweekly', 'monthly')
- price (decimal - base route price per shipment)
- duration_days (integer - estimated transit time)
- is_active (boolean - default: true)
- created_at
- updated_at

### Containers Table
- id (Primary Key)
- name (varchar - e.g., "20ft Standard", "40ft High Cube")
- size (decimal - in m³)
- price (decimal - container price)
- description (text)
- is_available (boolean - default: true)
- created_at
- updated_at

### Orders Table
- id (Primary Key)
- merchant_id (Foreign Key → users.id)
- route_id (Foreign Key → routes.id)
- container_id (Foreign Key → containers.id)
- tracking_number (unique varchar)
- item_description (text - what's being shipped)
- recipient_name (varchar)
- recipient_phone (varchar)
- recipient_address (text)
- origin (varchar - pickup location)
- destination (varchar - delivery location)
- route_price (decimal - snapshot of route price)
- container_price (decimal - snapshot of container price)
- customs_fee (decimal - manually added by admin)
- total_cost (decimal - route_price + container_price + customs_fee)
- status (enum: 'pending_approval', 'approved', 'rejected', 'awaiting_payment', 'paid', 'processing', 'completed', 'cancelled')
- rejection_reason (text - nullable)
- created_at
- updated_at

### Shipments Table
- id (Primary Key)
- order_id (Foreign Key → orders.id)
- route_id (Foreign Key → routes.id)
- container_id (Foreign Key → containers.id)
- current_status (enum: 'pending', 'loaded', 'in_transit', 'arrived', 'delivered')
- last_updated (timestamp)
- created_at
- updated_at

### Shipment_Status_History Table
- id (Primary Key)
- shipment_id (Foreign Key → shipments.id)
- status (varchar)
- notes (text - nullable, admin can add notes)
- updated_by (Foreign Key → users.id - admin who made the update)
- created_at

### Payments Table
- id (Primary Key)
- order_id (Foreign Key → orders.id)
- amount (decimal)
- payment_method (varchar - e.g., 'plutu', 'cash')
- transaction_ref (varchar - unique, nullable)
- status (enum: 'pending', 'completed', 'failed', 'refunded')
- paid_at (timestamp - nullable)
- created_at
- updated_at

### Notifications Table
- id (Primary Key)
- user_id (Foreign Key → users.id)
- message (text)
- type (enum: 'order_status', 'payment', 'system', 'shipment_update')
- read_at (timestamp - nullable)
- created_at
- updated_at

## Database Relationships:

- Users (1) → Orders (Many) - A merchant can have multiple orders
- Routes (1) → Orders (Many) - A route can be used for multiple orders
- Containers (1) → Orders (Many) - A container type can be used for multiple orders
- Orders (1) → Shipments (1) - Each order has one shipment
- Orders (1) → Payments (1) - Each order has one payment
- Shipments (1) → Shipment_Status_History (Many) - Track all status changes
- Users (1) → Notifications (Many) - Users receive multiple notifications

## Pricing Calculation Logic:
1. Merchant selects: Route + Container Type
2. System fetches: route.price + container.price
3. Admin adds: customs_fee (manually during order approval)
4. Final calculation: total_cost = route_price + container_price + customs_fee
5. Store all price components in Orders table for transparency and historical record

## PDF Receipt Generation:
- Generated on-demand when merchant requests download
- Contains: Order details, shipment info, payment status, tracking number
- Generated after payment completion
- Not stored in database - created dynamically each time

## Admin Dashboard Statistics (Filament):
- Total Orders Count
- Pending Approval Count
- Active Shipments Count
- Total Revenue (sum of completed payments)
- Recent Orders List
- Payment Status Overview