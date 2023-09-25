-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2023 at 09:54 AM
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
(11, 'F1', NULL, 'D1', 'ak@am.students.amrita.edu', '001', 'faculty', 0, 0);

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

-- --------------------------------------------------------

--
-- Table structure for table `seva_details`
--

CREATE TABLE `seva_details` (
  `Seva Id` int(11) NOT NULL,
  `Seva Name` varchar(255) NOT NULL,
  `Seva Coordinator` varchar(255) NOT NULL,
  `EID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seva_details`
--

INSERT INTO `seva_details` (`Seva Id`, `Seva Name`, `Seva Coordinator`, `EID`) VALUES
(14, 'Seva 2', 'F1', 11);

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
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`SID`, `Name`, `RollNumber`, `Email`, `Contact`, `Semester`, `Batch`, `user_type`, `Password`) VALUES
(5, 'Mohan', '43', 'e', '5', 5, 'BCA', 'student', ''),
(6, 'Rahul', '21', 'a', '2', 3, 'CSE', 'student', ''),
(7, 'Rizwan', '45', 'sports', '3', 6, 'BCA', 'student', ''),
(8, 'saddsdsd', '3', 'its in the game', '1', 2, 'MBA', 'student', ''),
(13, 'Mohan', '63', 'e', '5', 5, 'BCA', 'student', ''),
(14, 'Rahul', '12', 'a', '2', 3, 'CSE', 'student', ''),
(15, 'Rizwan', '54', 'sports', '3', 6, 'BCA', 'student', ''),
(16, 'saddsdsd', '30', 'its in the game', '1', 2, 'MBA', 'student', ''),
(17, 'Zil', '15', 'ga', '14', 3, 'BBA', 'student', ''),
(18, 'kel', '18', 'fa', '23', 4, 'ECE', 'student', ''),
(19, 'dm', '91', 'iew', '1313', 1, 'LOC', 'student', ''),
(20, 'new', '141', 'rw', '45', 5, 'REC', 'student', '');

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
  MODIFY `EID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `seva_details`
--
ALTER TABLE `seva_details`
  MODIFY `Seva Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `SID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`Student ID`) REFERENCES `students` (`SID`),
  ADD CONSTRAINT `seva_assignments_ibfk_1` FOREIGN KEY (`Seva Id`) REFERENCES `seva_details` (`Seva Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
