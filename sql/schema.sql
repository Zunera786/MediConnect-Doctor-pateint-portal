-- MediConnect Database Setup Script
-- Database: mediconnect_db

-- Admins Table
CREATE TABLE admins (
admin_id INT(11) AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors Table (Registration Fields Included)
CREATE TABLE doctors (
doctor_id INT(11) AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL,
aadhaar_no VARCHAR(12) UNIQUE NOT NULL,
phone VARCHAR(15) NOT NULL,
specialization VARCHAR(100) NOT NULL,
experience_years INT(3) NOT NULL,
qualifications TEXT NOT NULL,
hospital_clinic VARCHAR(150) NOT NULL,
license_no VARCHAR(100) UNIQUE NOT NULL,
surgeries_performed INT(5) DEFAULT 0,
consultation_fee DECIMAL(6, 2) NOT NULL,
is_verified BOOLEAN DEFAULT FALSE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table (Registration Fields Included)
CREATE TABLE patients (
patient_id INT(11) AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(100) NOT NULL,
last_name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password_hash VARCHAR(255) NOT NULL,
aadhaar_no VARCHAR(12) UNIQUE NOT NULL,
phone VARCHAR(15) NOT NULL,
dob DATE NOT NULL,
gender ENUM('Male', 'Female', 'Other') NOT NULL,
blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
health_issues TEXT,
current_medications TEXT,
address TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments Table (Links Patients and Doctors)
CREATE TABLE appointments (
appointment_id INT(11) AUTO_INCREMENT PRIMARY KEY,
patient_id INT(11) NOT NULL,
doctor_id INT(11) NOT NULL,
appointment_time DATETIME NOT NULL,
reason TEXT NOT NULL,
status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE


);

-- Prescriptions Table (Links to Patient, Doctor, and Appointment)
CREATE TABLE prescriptions (
prescription_id INT(11) AUTO_INCREMENT PRIMARY KEY,
patient_id INT(11) NOT NULL,
doctor_id INT(11) NOT NULL,
appointment_id INT(11) DEFAULT NULL,
medication_details TEXT NOT NULL,
dosage TEXT NOT NULL,
instructions TEXT,
prescription_date DATE NOT NULL,

FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE,
FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE SET NULL


);