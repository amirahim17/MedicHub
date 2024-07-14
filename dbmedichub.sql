-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2024 at 05:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbmedichub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`) VALUES
('ADM0001'),
('ADM0002');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointmentID` varchar(10) NOT NULL,
  `appointmentDate` date NOT NULL,
  `appointmentStatus` varchar(15) NOT NULL,
  `diagnosis` varchar(250) DEFAULT NULL,
  `doctorID` varchar(10) NOT NULL,
  `patientID` varchar(10) NOT NULL,
  `prescriptionID` varchar(10) DEFAULT NULL,
  `timeSlot` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointmentID`, `appointmentDate`, `appointmentStatus`, `diagnosis`, `doctorID`, `patientID`, `prescriptionID`, `timeSlot`) VALUES
('APP0001', '2024-06-26', 'Completed', 'Fever', 'D0001', 'P0001', 'PR0001', '0800-0900'),
('APP0002', '2024-07-04', 'Completed', 'Buasir', 'D0001', 'P0006', 'PR0008', '1100-1200'),
('APP0003', '2024-06-29', 'Completed', 'Diabetes', 'D0002', 'P0007', 'PR0009', '1700-1800'),
('APP0004', '2024-07-03', 'Completed', 'Flu', 'D0001', 'P0007', 'PR0009', '1600-1700'),
('APP0005', '2024-07-06', 'Completed', 'Fever', 'D0004', 'P0007', 'PR0006', '0800-0900'),
('APP0006', '2024-07-01', 'Completed', 'Flu', 'D0003', 'P0008', 'PR0002', '1100-1200'),
('APP0007', '2024-07-09', 'Completed', 'Hypertension', 'D0001', 'P0008', 'PR0007', '0800-0900');

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `doctorID` varchar(10) NOT NULL,
  `doctorName` varchar(40) NOT NULL,
  `doctorNRIC` varchar(14) NOT NULL,
  `doctorSpeciality` varchar(30) NOT NULL,
  `availability` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`doctorID`, `doctorName`, `doctorNRIC`, `doctorSpeciality`, `availability`) VALUES
('D0001', 'Dr. Harith Johari', '760929-14-9231', 'Family Medicine', 'Available'),
('D0002', 'Dr. Rashid', '801120-10-0543', 'Psychiatry', 'Available'),
('D0003', 'Dr. Faqiha', '840519-06-3802', 'Family Medicine', 'Not Available'),
('D0004', 'Dr. Laila', '870212-10-7398', 'Obstetrics and Gynaecology', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `userID` varchar(10) NOT NULL,
  `userPassword` varchar(60) NOT NULL,
  `userTypeID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`userID`, `userPassword`, `userTypeID`) VALUES
('ADM0001', 'adminPass1', 'ADM0001'),
('ADM0002', 'adminPass2', 'ADM0002'),
('D0001', 'doctorPass1', 'D0001'),
('D0002', 'doctorPass2', 'D0002'),
('D0003', 'doctorPass3', 'D0003'),
('D0004', 'doctorPass4', 'D0004'),
('P0001', '$2y$10$ItH8MxH1wsC8cn4obD3R/.GdEFH9LIPPOhT5NSeXCZ6So8qYHs7wa', 'P0001'),
('P0002', '$2y$10$isZZ0Mo.DlF1/nVAdNhhle3dYLkc7p1TAIcockvdP0QsZ/lssbzRe', 'P0002'),
('P0003', '$2y$10$OO5THRdUxGCwY48.zbl8YeaOh1dxLBsMRnt.vjSgknpr/SWdWRipC', 'P0003'),
('P0004', '$2y$10$9FBfcuYLAgXRfFQR8.88q.ONDd0cEgWYeO8nDkHyK88QYjuowQRHq', 'P0004'),
('P0005', '$2y$10$8YFizsYhCAcwbod1zuAnK.lTNzvRbJa6DqwsU0worTC7Z67B8QzOu', 'P0005'),
('P0006', '$2y$10$RB8aSg9rXotkzJAlcDHzrOPlJgdy49cug/2cEIctpRAeF2/j878VC', 'P0006'),
('P0007', '$2y$10$vCJqrvFJZH3ufHQ.1KWfBuzA4TO5p5s1ubDkP6xAw5Cfp4VPdgvEK', 'P0007'),
('P0008', '$2y$10$TPZceEKN/cEoCtFU9NSzIubid1hCuUs8MXNd.q6fvhnbTS.hMoD1a', 'P0008');

-- --------------------------------------------------------

--
-- Table structure for table `medicalcertificate`
--

CREATE TABLE `medicalcertificate` (
  `mcSerialNumber` varchar(15) NOT NULL,
  `mcDate` date NOT NULL,
  `duration` int(11) NOT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `appointmentID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicalcertificate`
--

INSERT INTO `medicalcertificate` (`mcSerialNumber`, `mcDate`, `duration`, `diagnosis`, `appointmentID`) VALUES
('MC0001', '2024-06-26', 2, 'Fever', 'APP0001'),
('MC0002', '2024-07-04', 3, 'Buasir', 'APP0002'),
('MC0003', '2024-06-29', 1, 'Diabetes', 'APP0003'),
('MC0004', '2024-07-03', 2, 'Flu', 'APP0004');

-- --------------------------------------------------------

--
-- Table structure for table `medication`
--

CREATE TABLE `medication` (
  `medSerialNumber` varchar(15) NOT NULL,
  `medName` varchar(40) NOT NULL,
  `mfgDate` date NOT NULL,
  `expDate` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `medFactory` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medication`
--

INSERT INTO `medication` (`medSerialNumber`, `medName`, `mfgDate`, `expDate`, `quantity`, `medFactory`) VALUES
('MED0001', 'Paracetamol', '2025-02-01', '2027-02-01', 180, 'ABC Pharma'),
('MED0002', 'Ibuprofen', '2025-03-10', '2027-03-10', 200, 'XYZ Pharma'),
('MED0003', 'Amoxicillin', '2025-03-17', '2027-03-17', 151, 'Medic Pharma'),
('MED0004', 'Methotrexate', '2025-03-21', '2027-03-21', 50, 'ABC Pharma'),
('MED0005', 'Diuretics', '2024-04-29', '2027-04-29', 400, 'Jay Pharma'),
('MED0006', 'Tramadol', '2024-04-30', '2027-04-30', 70, 'ABC Pharma'),
('MED0007', 'Fentanyl', '2024-05-10', '2027-05-10', 80, 'HealthCare'),
('MED0008', 'Oxycodone', '2024-05-15', '2027-05-15', 150, 'DrugStore Pharma'),
('MED0009', 'Trimethoprim', '2024-05-20', '2027-05-20', 100, 'Lola Pharma'),
('MED0010', 'Funderparinex', '2024-05-23', '2027-05-23', 170, 'Medic Pharma');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patientID` varchar(10) NOT NULL,
  `patientNRIC` varchar(14) NOT NULL,
  `patientName` varchar(40) NOT NULL,
  `patientPhoneNo` varchar(12) NOT NULL,
  `patientAddress` varchar(250) NOT NULL,
  `registerDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patientID`, `patientNRIC`, `patientName`, `patientPhoneNo`, `patientAddress`, `registerDate`) VALUES
('P0001', '021102140937', 'Ahmad Mustafa bin Maznan', '0196600304', '33, Jalan Mentari', '2024-06-14'),
('P0002', '040313102711', 'Wan Hafiz', '0155500304', '10, Desa Sari', '2024-06-14'),
('P0003', '670616344804', 'Nur Zulaikha ', '0184423426', '6, Taman Gembala', '2024-06-14'),
('P0004', '090303105645', 'Hazeem Hazri', '0173456023', '45, Bertam Indah', '2024-06-14'),
('P0005', '991023140988', 'Kasih Laila', '0192379234', '21 , Jalan Kasih', '2024-06-26'),
('P0006', '961230140277', 'Kim Taehyung', '0194563223', '12, Daegu Road , Seoul', '2024-06-28'),
('P0007', '911116078992', 'Amira Natasha binti Radzi', '0182345267', '20 , Jalan Mentakab', '2024-06-28'),
('P0008', '920406119062', 'Maya Karin', '0185469219', '3 , Apartment Royal , Height Street ', '2024-06-30');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` varchar(10) NOT NULL,
  `riskPanelAvailability` varchar(15) NOT NULL,
  `panelName` varchar(40) DEFAULT NULL,
  `billCharges` decimal(6,2) NOT NULL,
  `appointmentID` varchar(10) NOT NULL,
  `adminID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `riskPanelAvailability`, `panelName`, `billCharges`, `appointmentID`, `adminID`) VALUES
('PAY0001', 'no', '', 100.00, 'APP0001', 'ADM0001'),
('PAY0002', 'yes', 'Prudential Sdn. Bhd.', 0.00, 'APP0002', 'ADM0001'),
('PAY0003', 'yes', 'AIA Berhad', 0.00, 'APP0003', 'ADM0001'),
('PAY0004', 'yes', 'Berkshire Hathaway Inc.', 0.00, 'APP0004', 'ADM0001'),
('PAY0005', 'yes', 'AIA Berhad', 0.00, 'APP0006', 'ADM0001');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescriptionID` varchar(10) NOT NULL,
  `medSerialNumber` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescriptionID`, `medSerialNumber`) VALUES
('PR0001', 'MED0001'),
('PR0002', 'MED0002'),
('PR0003', 'MED0003'),
('PR0004', 'MED0004'),
('PR0005', 'MED0005'),
('PR0006', 'MED0006'),
('PR0007', 'MED0007'),
('PR0008', 'MED0008'),
('PR0009', 'MED0009'),
('PR0010', 'MED0010');

-- --------------------------------------------------------

--
-- Table structure for table `usertype`
--

CREATE TABLE `usertype` (
  `userTypeID` varchar(10) NOT NULL,
  `userType` enum('admin','doctor','patient') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertype`
--

INSERT INTO `usertype` (`userTypeID`, `userType`) VALUES
('ADM0001', 'admin'),
('ADM0002', 'admin'),
('D0001', 'doctor'),
('D0002', 'doctor'),
('D0003', 'doctor'),
('D0004', 'doctor'),
('P0001', 'patient'),
('P0002', 'patient'),
('P0003', 'patient'),
('P0004', 'patient'),
('P0005', 'patient'),
('P0006', 'patient'),
('P0007', 'patient'),
('P0008', 'patient');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointmentID`),
  ADD KEY `doctorID` (`doctorID`),
  ADD KEY `patientID` (`patientID`),
  ADD KEY `prescriptionID` (`prescriptionID`);

--
-- Indexes for table `doctor`
--
ALTER TABLE `doctor`
  ADD PRIMARY KEY (`doctorID`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `userTypeID` (`userTypeID`);

--
-- Indexes for table `medicalcertificate`
--
ALTER TABLE `medicalcertificate`
  ADD PRIMARY KEY (`mcSerialNumber`),
  ADD KEY `appointmentID` (`appointmentID`);

--
-- Indexes for table `medication`
--
ALTER TABLE `medication`
  ADD PRIMARY KEY (`medSerialNumber`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patientID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `appointmentID` (`appointmentID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescriptionID`),
  ADD KEY `medSerialNumber` (`medSerialNumber`);

--
-- Indexes for table `usertype`
--
ALTER TABLE `usertype`
  ADD PRIMARY KEY (`userTypeID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`doctorID`) REFERENCES `doctor` (`doctorID`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`patientID`) REFERENCES `patient` (`patientID`),
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`prescriptionID`) REFERENCES `prescription` (`prescriptionID`);

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`userTypeID`) REFERENCES `usertype` (`userTypeID`);

--
-- Constraints for table `medicalcertificate`
--
ALTER TABLE `medicalcertificate`
  ADD CONSTRAINT `medicalcertificate_ibfk_1` FOREIGN KEY (`appointmentID`) REFERENCES `appointment` (`appointmentID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`appointmentID`) REFERENCES `appointment` (`appointmentID`),
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`);

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`medSerialNumber`) REFERENCES `medication` (`medSerialNumber`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
