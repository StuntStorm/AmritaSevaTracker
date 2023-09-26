-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2023 at 08:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newuniversity`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_students`
--

CREATE TABLE `attendance_students` (
  `attendance_id` int(11) NOT NULL,
  `seva_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `attendance` enum('yes','no') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `EID` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `user_type` enum('main_coordinator','seva_coordinator','faculty','students') NOT NULL DEFAULT 'faculty',
  `status` int(11) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`EID`, `name`, `password`, `department`, `email`, `contact`, `user_type`, `status`, `available`) VALUES
(20, 'F1', NULL, 'D1', NULL, '001', 'seva_coordinator', 0, 0),
(21, 'F2', NULL, 'D2', NULL, '002', 'faculty', 0, 0),
(22, 'F3', NULL, 'D3', NULL, '003', 'faculty', 0, 0),
(26, 'ETHANAEL KHARKONGOR', '$2y$10$8JmfIdmouFOAqGHd1JtrcuXdrA7gaq5NVsUvyUVZzIuP1X/OjsM5u', 'Computer Science', 'amscu3csc21024@am.students.amrita.edu', '9863872923', 'main_coordinator', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `seva_assignments`
--

CREATE TABLE `seva_assignments` (
  `AssignmentID` int(11) NOT NULL,
  `Seva Id` int(11) DEFAULT NULL,
  `Faculty ID` int(11) DEFAULT NULL,
  `Student ID` int(11) DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seva_assignments`
--

INSERT INTO `seva_assignments` (`AssignmentID`, `Seva Id`, `Faculty ID`, `Student ID`, `StartTime`, `EndTime`) VALUES
(62, 21, 20, 21, '12:00:00', '14:00:00'),
(63, 21, 20, 22, '12:00:00', '14:00:00'),
(64, 22, 20, 21, '12:15:00', '07:00:00'),
(65, 23, NULL, 24, '18:30:00', '22:10:00');

--
-- Triggers `seva_assignments`
--
DELIMITER $$
CREATE TRIGGER `assign_seva_start_time` BEFORE INSERT ON `seva_assignments` FOR EACH ROW BEGIN
  DECLARE seva_start_time TIME;
  DECLARE seva_end_time TIME;

  SELECT StartTime, EndTime
  INTO seva_start_time, seva_end_time
  FROM seva_details
  WHERE `Seva Id` = NEW.`Seva Id`;

  SET NEW.StartTime = seva_start_time;
  SET NEW.EndTime = seva_end_time;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `seva_details`
--

CREATE TABLE `seva_details` (
  `Seva Id` int(11) NOT NULL,
  `Seva Name` varchar(255) NOT NULL,
  `Seva Coordinator` varchar(255) NOT NULL,
  `EID` int(11) DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seva_details`
--

INSERT INTO `seva_details` (`Seva Id`, `Seva Name`, `Seva Coordinator`, `EID`, `StartTime`, `EndTime`) VALUES
(21, 'Seva 1', 'ETHANAEL KHARKONGOR', 26, '12:00:00', '14:00:00'),
(22, 'Seva 2', 'F1', 20, '12:15:00', '07:00:00'),
(23, 'Seva 3', '', NULL, '18:30:00', '22:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `SID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `RollNumber` varchar(20) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Contact` varchar(15) NOT NULL,
  `Semester` int(11) NOT NULL,
  `Batch` varchar(10) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'student',
  `Password` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`SID`, `Name`, `RollNumber`, `Email`, `Contact`, `Semester`, `Batch`, `user_type`, `Password`, `status`) VALUES
(21, 'S1', '01', 'ak@am.students.amrita.edu', '001', 5, 'CSE', 'student', '$2y$10$8JmfIdmouFOAqGHd1JtrcuXdrA7gaq5NVsUvyUVZzIuP1X/OjsM5u', NULL),
(22, 'S2', '02', '', '002', 4, 'BCA', 'student', NULL, NULL),
(24, 'S3', '03', '', '1234', 4, 'BCA', 'student', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_students`
--
ALTER TABLE `attendance_students`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `seva_id` (`seva_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`EID`);

--
-- Indexes for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `Seva Id` (`Seva Id`),
  ADD KEY `Faculty ID` (`Faculty ID`),
  ADD KEY `fk_student` (`Student ID`);

--
-- Indexes for table `seva_details`
--
ALTER TABLE `seva_details`
  ADD PRIMARY KEY (`Seva Id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`SID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_students`
--
ALTER TABLE `attendance_students`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `EID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `seva_details`
--
ALTER TABLE `seva_details`
  MODIFY `Seva Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `SID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance_students`
--
ALTER TABLE `attendance_students`
  ADD CONSTRAINT `attendance_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`SID`),
  ADD CONSTRAINT `attendance_students_ibfk_2` FOREIGN KEY (`seva_id`) REFERENCES `seva_details` (`Seva Id`);

--
-- Constraints for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  ADD CONSTRAINT `fk_seva_assignments_login` FOREIGN KEY (`Faculty ID`) REFERENCES `login` (`EID`),
  ADD CONSTRAINT `fk_seva_details` FOREIGN KEY (`Seva Id`) REFERENCES `seva_details` (`Seva Id`),
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`Student ID`) REFERENCES `students` (`SID`),
  ADD CONSTRAINT `seva_assignments_ibfk_1` FOREIGN KEY (`Seva Id`) REFERENCES `seva_details` (`Seva Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
