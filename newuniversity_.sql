-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2023 at 05:35 PM
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
  `is_present` tinyint(4) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty_assignment`
--

CREATE TABLE `faculty_assignment` (
  `AssignmentID` int(11) NOT NULL,
  `SevaId` int(11) NOT NULL,
  `FacultyId` int(11) NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `faculty_assignment`
--
DELIMITER $$
CREATE TRIGGER `assign_faculty_seva_start_time` BEFORE INSERT ON `faculty_assignment` FOR EACH ROW BEGIN
  DECLARE faculty_seva_start_time TIME;
  DECLARE faculty_seva_end_time TIME;

  SELECT StartTime, EndTime
  INTO faculty_seva_start_time, faculty_seva_end_time
  FROM seva_details
  WHERE `Seva Id` = NEW.`SevaId`;

  SET NEW.StartTime = faculty_seva_start_time;
  SET NEW.EndTime = faculty_seva_end_time;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_seva_assignment` BEFORE INSERT ON `faculty_assignment` FOR EACH ROW BEGIN
    DECLARE seva_count INT;

    -- Check if the StartTime and EndTime exist in seva_details
    SELECT COUNT(*) INTO seva_count
    FROM seva_details
    WHERE StartTime = NEW.StartTime AND EndTime = NEW.EndTime;

    -- If no matching rows are found, raise an error
    IF seva_count = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid StartTime and EndTime. No matching records in seva_details.';
    END IF;
END
$$
DELIMITER ;

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

-- --------------------------------------------------------

--
-- Table structure for table `seva_assignments`
--

CREATE TABLE `seva_assignments` (
  `AssignmentID` int(11) NOT NULL,
  `Seva Id` int(11) DEFAULT NULL,
  `Student ID` int(11) DEFAULT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `faculty_assignment`
--
ALTER TABLE `faculty_assignment`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD UNIQUE KEY `unique_assignment` (`SevaId`,`FacultyId`),
  ADD KEY `fk_faculty` (`FacultyId`);

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
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `faculty_assignment`
--
ALTER TABLE `faculty_assignment`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `EID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  MODIFY `AssignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `seva_details`
--
ALTER TABLE `seva_details`
  MODIFY `Seva Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `SID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
-- Constraints for table `faculty_assignment`
--
ALTER TABLE `faculty_assignment`
  ADD CONSTRAINT `fk_faculty` FOREIGN KEY (`FacultyId`) REFERENCES `login` (`EID`),
  ADD CONSTRAINT `fk_seva` FOREIGN KEY (`SevaId`) REFERENCES `seva_details` (`Seva Id`);

--
-- Constraints for table `seva_assignments`
--
ALTER TABLE `seva_assignments`
  ADD CONSTRAINT `fk_seva_details` FOREIGN KEY (`Seva Id`) REFERENCES `seva_details` (`Seva Id`),
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`Student ID`) REFERENCES `students` (`SID`),
  ADD CONSTRAINT `seva_assignments_ibfk_1` FOREIGN KEY (`Seva Id`) REFERENCES `seva_details` (`Seva Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
