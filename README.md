# 4413 E-Commerce Project

This project showcases a fully functional e-commerce website developed as part of the EECS 4413 course. The application allows users to browse products, manage their profiles, and complete transactions.

## Accessing the Project

You can view the live version of the website here:

[Live Website](http://akeem.rf.gd/view/index.html)

## Source Code

The source code for this project is available on GitHub:

[GitHub Repository](https://github.com/Akbram98/4413-Ecommerce-Project)

### How to Download the Source Code

Submitted on eClass.

## Local Setup
### To run the project locally:

1. **Install WampServer**: Download and install WampServer from here.  
2. **Access phpMyAdmin**:  Open phpMyAdmin by navigating to http://localhost/phpmyadmin/ in your browser.  
3. **Create Database**: Log in with the username root (no password) and create a new database named eecs4413.  
4. **Execute SQL Scripts**: Select the eecs4413 database, go to the SQL tab, paste the provided SQL scripts, and click "Go" to execute.  
5. **Configure Database Connection**: If you have set a password for the root user, update the dao/Database.php file to include your password.  
6. **Access the Application**: Navigate to http://localhost/view/index.html in your browser to view and interact with the application.  

## Admin Credentials
To access the admin panel, use the following credentials:

**Username**: hiraku  
**Password**: hiraku123  

## Database Setup

To set up the database tables, execute the following SQL scripts:

```sql
CREATE TABLE User (
    userName VARCHAR(50) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    salt VARCHAR(255) NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_logon DATETIME,
    admin BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE Profile (
    userName VARCHAR(50) PRIMARY KEY,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    age INT,
    street VARCHAR(255),
    city VARCHAR(100),
    province VARCHAR(100),
    postal VARCHAR(20),
    card_num VARCHAR(50),
    cvv VARCHAR(3),
    expiry VARCHAR(20),
    CONSTRAINT fk_user FOREIGN KEY (userName) REFERENCES Users(userName)
);

CREATE TABLE Inventory (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    brand VARCHAR(100),
    date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    quantity INT NOT NULL,
    image VARCHAR(255)
);

ALTER TABLE Inventory AUTO_INCREMENT = 10007;

CREATE TABLE Transaction (
    trans_id INT AUTO_INCREMENT NOT NULL,
    item_id INT NOT NULL,
    userName VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    price FLOAT NOT NULL,
    PRIMARY KEY (trans_id, item_id),
    CONSTRAINT fk_item FOREIGN KEY (item_id) REFERENCES Inventory(item_id),
    CONSTRAINT fk_user FOREIGN KEY (userName) REFERENCES Users(userName)
);

ALTER TABLE Transaction AUTO_INCREMENT = 1000003;

CREATE TABLE Payment (
    trans_id INT NOT NULL PRIMARY KEY,
    card_num VARCHAR(50) NOT NULL,
    cvv VARCHAR(3) NOT NULL,
    expiry VARCHAR(20) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    fullName VARCHAR(300) NOT NULL,
    processed BOOLEAN NOT NULL DEFAULT FALSE,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaction FOREIGN KEY (trans_id) REFERENCES Transaction(trans_id)
);
