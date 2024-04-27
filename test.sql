-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2024 at 10:41 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `ledgers`
--

CREATE TABLE `ledgers` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Series` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ledgers`
--

INSERT INTO `ledgers` (`Id`, `Name`, `Series`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Aditya', 100, '2024-04-26 10:27:19', '2024-04-26 10:28:36');

-- --------------------------------------------------------

--
-- Table structure for table `lossofpointers`
--

CREATE TABLE `lossofpointers` (
  `Id` int(11) NOT NULL,
  `DV_No` varchar(50) NOT NULL,
  `DV_Date` date NOT NULL,
  `Bill_Description` varchar(255) NOT NULL,
  `Amount` varchar(255) NOT NULL,
  `Beneficiary_Name` varchar(100) NOT NULL,
  `IFSC_Code` varchar(20) NOT NULL,
  `Account_No` varchar(20) NOT NULL,
  `Remark` varchar(255) NOT NULL,
  `Cheque_No` varchar(20) NOT NULL,
  `Value_Date` date NOT NULL,
  `Release_Date` date NOT NULL,
  `UTR_No` varchar(50) NOT NULL,
  `Rejected_Reason` varchar(255) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lossofpointers`
--

INSERT INTO `lossofpointers` (`Id`, `DV_No`, `DV_Date`, `Bill_Description`, `Amount`, `Beneficiary_Name`, `IFSC_Code`, `Account_No`, `Remark`, `Cheque_No`, `Value_Date`, `Release_Date`, `UTR_No`, `Rejected_Reason`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, '1001', '2024-04-26', 'Bill ABC', '10000', 'Rohit', 'IBIB000176', '45785421356', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:48:13', '2024-04-26 13:50:49'),
(2, '1001', '2024-04-26', 'Bill ABC', '50000', 'Raj', 'IBIB000175', '5487548754', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:51:25', '2024-04-26 13:51:25'),
(3, '1002', '2024-04-26', 'Bill UUU', '250000', 'Sunita', 'KTKB000175', '8754986598', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:52:55', '2024-04-26 13:52:55'),
(4, '1004', '2024-04-26', 'Bill 1000', '100000', 'Sunita', 'SBIB000175', '2154215421', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:54:28', '2024-04-26 13:54:28'),
(5, '1004', '2024-04-26', 'Bill 50000', '600000', 'Pranita', 'SBIB000175', '9865986587', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:55:00', '2024-04-26 13:55:00'),
(6, '1003', '2024-04-26', 'Bill 50000', '600000', 'Anjali', 'SBIB000175', '5454545454', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:55:31', '2024-04-26 13:55:31'),
(7, '1003', '2024-04-26', 'Bill 50000', '600000', 'Anjali', 'KTAB000175', '5465655465', 'True', '', '0000-00-00', '0000-00-00', '', '', 'PENDING', '2024-04-26 13:55:45', '2024-04-26 13:55:45'),
(8, '1003', '2024-04-27', 'Bill 50000', '300000', 'Anju', 'KKKB000175', '5487542154', 'True', '', '0000-00-00', '0000-00-00', '', '', 'SUCCESS', '2024-04-27 07:31:50', '2024-04-27 08:13:30');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `Id` int(11) NOT NULL,
  `Receipt_No` int(11) NOT NULL,
  `Receipt_Date` date NOT NULL,
  `Amount` int(11) NOT NULL,
  `Amount_In_Words` varchar(255) NOT NULL,
  `Section` int(11) NOT NULL,
  `Remark` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`Id`, `Receipt_No`, `Receipt_Date`, `Amount`, `Amount_In_Words`, `Section`, `Remark`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1000, '2024-04-11', 100, 'one hundred', 1, 'DD', '2024-04-26 11:26:42', '2024-04-26 11:26:42');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `Id` int(11) NOT NULL,
  `Section_Code` int(11) NOT NULL,
  `Section_Name` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`Id`, `Section_Code`, `Section_Name`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 200, 'Comp', '2024-04-26 10:43:02', '2024-04-26 10:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `Id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Mobile` varchar(15) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`Id`, `Name`, `Mobile`, `Email`, `Password`, `Role`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Admin', '8754875487', 'govt@mailinator.com', '$2y$10$Bdky2t9hMBXdvlEoHVJVz.Dju1i0UXFZiUhrgSlh3QDgU19E5sMcS', 'ADMIN', '1', '2024-04-26 16:25:41', '2024-04-26 16:25:41'),
(2, 'Rohit', '5555668845', 'rohit@gmail.com', '$2y$10$LKf8IDZ2JUNplLgPaOPKAOo1RNlLoWe8vT1y1N0oPtUvnEmRqvKa.', 'USER', '1', '2024-04-26 17:05:58', '2024-04-27 06:48:33'),
(7, 'Aditya', '8754875484', 'adi@gmail.com', '$2y$10$Zsz26QT7M46WbAQJsj8L0eBROO/AC02VNWOUcZyaA39/73eAXRR2a', 'USER', '1', '2024-04-27 05:14:42', '2024-04-27 06:48:23'),
(8, 'Tejas', '8798659865', 'teja@gmail.com', '$2y$10$5.x8uA0TIS55wNEqvXYeoufmUUfaa.MIBInj6aGH9mKqyadsmQDb2', 'USER', '1', '2024-04-27 06:25:55', '2024-04-27 06:43:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ledgers`
--
ALTER TABLE `ledgers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `lossofpointers`
--
ALTER TABLE `lossofpointers`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Receipt_No` (`Receipt_No`),
  ADD KEY `Section` (`Section`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `Mobile` (`Mobile`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `UC_Email` (`Email`),
  ADD UNIQUE KEY `UC_Mobile` (`Mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ledgers`
--
ALTER TABLE `ledgers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lossofpointers`
--
ALTER TABLE `lossofpointers`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`Section`) REFERENCES `sections` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
