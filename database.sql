-- Create doctors table first
CREATE TABLE IF NOT EXISTS doctors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    phone VARCHAR(10) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    qualification VARCHAR(100) NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    hospital VARCHAR(255),
    experience INT DEFAULT 0,
    password VARCHAR(255) NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create patients table second
CREATE TABLE IF NOT EXISTS patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    aadhaar VARCHAR(12) UNIQUE NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    marital_status ENUM('single', 'married', 'divorced', 'widowed'),
    nationality VARCHAR(50) DEFAULT 'Indian',
    address TEXT NOT NULL,
    phone VARCHAR(10) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'),
    allergies TEXT,
    current_medications TEXT,
    has_insurance ENUM('yes', 'no') DEFAULT 'no',
    insurance_provider VARCHAR(255),
    policy_number VARCHAR(100),
    group_number VARCHAR(100),
    insurance_phone VARCHAR(10),
    emergency_name1 VARCHAR(255) NOT NULL,
    emergency_relation1 ENUM('spouse', 'parent', 'child', 'sibling', 'friend', 'other') NOT NULL,
    emergency_phone1 VARCHAR(10) NOT NULL,
    emergency_email1 VARCHAR(191),
    emergency_name2 VARCHAR(255),
    emergency_relation2 ENUM('spouse', 'parent', 'child', 'sibling', 'friend', 'other'),
    emergency_phone2 VARCHAR(10),
    emergency_email2 VARCHAR(191),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create appointments table (depends on both doctors and patients)
CREATE TABLE IF NOT EXISTS appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    reason TEXT,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    patient_phone VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    medicines TEXT NOT NULL,
    dosage VARCHAR(255) NOT NULL,
    instructions TEXT,
    duration VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Doctor availability/schedule table
CREATE TABLE IF NOT EXISTS doctor_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    doctor_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Patient documents table
CREATE TABLE IF NOT EXISTS patient_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    document_type ENUM('xray', 'ct', 'mri', 'prescription', 'lab_report', 'other') NOT NULL,
    document_date DATE NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Medical history table
CREATE TABLE IF NOT EXISTS medical_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    condition_name VARCHAR(255) NOT NULL,
    diagnosis_date DATE NOT NULL,
    treatment TEXT,
    status ENUM('active', 'resolved', 'chronic') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Patient medications table
CREATE TABLE IF NOT EXISTS patient_medications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    prescribed_date DATE NOT NULL,
    end_date DATE,
    prescribed_by VARCHAR(255),
    purpose TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_appointments_doctor_date ON appointments(doctor_id, appointment_date);
CREATE INDEX idx_appointments_patient_date ON appointments(patient_id, appointment_date);
CREATE INDEX idx_appointments_status ON appointments(status);
CREATE INDEX idx_prescriptions_doctor ON prescriptions(doctor_id);
CREATE INDEX idx_prescriptions_patient ON prescriptions(patient_id);
CREATE INDEX idx_doctor_schedule_doctor ON doctor_schedule(doctor_id);
CREATE INDEX idx_patient_documents_patient ON patient_documents(patient_id);
CREATE INDEX idx_medical_history_patient ON medical_history(patient_id);
CREATE INDEX idx_patient_medications_patient ON patient_medications(patient_id);