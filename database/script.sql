CREATE DATABASE crud;
USE crud;

CREATE TABLE employees(
    employee_id INT AUTO_INCREMENT NOT NULL,
    job_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(70) UNIQUE NOT NULL,
    phone_number VARCHAR(8) NOT NULL,
    hire_date DATE DEFAULT CURDATE(),
    salary FLOAT NOT NULL,
    active BOOLEAN DEFAULT TRUE,
    profile VARCHAR(150) NULL,
    PRIMARY KEY(employee_id)
)ENGINE=InnoDB;

CREATE TABLE jobs(
    job_id INT AUTO_INCREMENT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    PRIMARY KEY(job_id)
)ENGINE=InnoDB;

ALTER TABLE employees
ADD CONSTRAINT fk_employee_job
FOREIGN KEY (job_id) REFERENCES jobs(job_id);

-- SEEDERS
INSERT INTO jobs(job_title) VALUES('Gerente General');
INSERT INTO jobs(job_title) VALUES('Secretario');
INSERT INTO jobs(job_title) VALUES('Gerente Marketing');