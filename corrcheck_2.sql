-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2015 at 06:06 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `corrcheck`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_companies`
--

DROP TABLE IF EXISTS `tbl_companies`;
CREATE TABLE IF NOT EXISTS `tbl_companies` (
  `company_ID` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `addr_1` varchar(255) DEFAULT NULL,
  `addr_2` varchar(255) DEFAULT NULL,
  `addr_3` varchar(255) DEFAULT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telno` varchar(255) DEFAULT NULL,
  `faxno` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`company_ID`),
  KEY `company_ID` (`company_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_companies`
--

INSERT INTO `tbl_companies` (`company_ID`, `company_name`, `addr_1`, `addr_2`, `addr_3`, `postcode`, `email`, `telno`, `faxno`) VALUES
(1, 'Website NI', '14 Main Street', 'Benburb', 'Dungannon', 'BT71 7LA', 'info@Websiteni.com', '028 3754 9025', ''),
(2, 'Corr Brothers Ltd', '101 Ballyards Road', 'Armagh', '', 'BT60 3NS', 'enquiries@corrbrothers.com', '028 3752 5245', ''),
(3, 'Website Dublin', '2 LAWRENCEVALE', '', '2 LAWRENCEVALE', 'BT636EN', 'gareth@websiteni.com', '07725319947', ''),
(4, 'Watson Inc', '21 Quaymount', '', 'Priory House West', 'BT71 7LA', 'info@watsoninc.com', '+442837549025', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_questions`
--

DROP TABLE IF EXISTS `tbl_questions`;
CREATE TABLE IF NOT EXISTS `tbl_questions` (
  `question_ID` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` varchar(255) NOT NULL,
  `type_ID` int(3) NOT NULL,
  `section_ID` int(3) NOT NULL,
  `ind_trailers` varchar(1) NOT NULL,
  `question_seqno` int(2) NOT NULL,
  `ind_required` varchar(1) NOT NULL,
  PRIMARY KEY (`question_ID`),
  KEY `question_ID` (`question_ID`),
  KEY `section_ID` (`section_ID`),
  KEY `section_ID_2` (`section_ID`),
  KEY `type_ID` (`type_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=171 ;

--
-- Dumping data for table `tbl_questions`
--

INSERT INTO `tbl_questions` (`question_ID`, `question_text`, `type_ID`, `section_ID`, `ind_trailers`, `question_seqno`, `ind_required`) VALUES
(11, 'Vehicle Type', 13, 1, 'Y', 1, 'Y'),
(12, 'Vehicle Registration', 11, 1, 'Y', 1, 'Y'),
(13, 'Company', 13, 1, 'Y', 2, 'Y'),
(14, 'Make/Model', 11, 1, 'Y', 3, 'Y'),
(15, 'Date of inspection', 14, 1, 'Y', 4, 'Y'),
(16, 'Odometer Reading', 16, 1, 'Y', 5, 'Y'),
(17, 'Odometer Type', 13, 1, 'Y', 6, 'Y'),
(18, 'Pre-service Remarks', 12, 1, 'Y', 7, 'N'),
(31, 'Grease All Points', 13, 4, 'Y', 1, 'N'),
(33, 'Check/Top Up All Units', 13, 4, 'N', 3, 'N'),
(35, 'Check/Top Up Batteries', 13, 4, 'N', 5, 'N'),
(37, 'Check/Top Up Brake Fluid', 13, 4, 'N', 7, 'N'),
(39, 'Check Coolant Antifreeze', 13, 4, 'N', 9, 'N'),
(41, 'Tail Lights', 13, 5, 'Y', 1, 'N'),
(44, 'Side Lights', 13, 5, 'Y', 2, 'N'),
(45, 'Stop Lights', 13, 5, 'Y', 3, 'N'),
(46, 'Indicator Lights', 13, 5, 'Y', 4, 'N'),
(47, 'Marker Lights', 13, 5, 'Y', 5, 'N'),
(48, 'No Plate Lights', 13, 5, 'Y', 6, 'N'),
(49, 'Fog Lights', 13, 5, 'Y', 7, 'N'),
(50, 'Headlight Aim', 13, 5, 'N', 8, 'N'),
(51, 'Headlight Pitch', 13, 5, 'N', 9, 'N'),
(52, 'Work Lamps', 13, 5, 'Y', 10, 'N'),
(53, 'Beacons', 13, 5, 'Y', 11, 'N'),
(54, 'Full Beam Lights', 13, 5, 'N', 13, 'N'),
(55, 'Dip Beam Lights', 13, 5, 'N', 14, 'N'),
(56, 'Interior Cabin Lights ', 13, 5, 'N', 12, 'N'),
(57, 'Reverse Light', 13, 5, 'N', 15, 'N'),
(58, '6 Year Tachograph Calibration', 13, 6, 'N', 1, 'N'),
(59, '2 Year Tachograph Calibration', 13, 6, 'N', 2, 'N'),
(60, 'Digital Tachograph Calibration', 13, 6, 'N', 3, 'N'),
(61, 'Speed Limiter', 13, 6, 'N', 4, 'N'),
(62, 'Gear Box Seal', 13, 6, 'N', 5, 'N'),
(63, 'Driver’s seat', 13, 7, 'N', 1, 'N'),
(64, 'Seat belts', 13, 7, 'N', 2, 'N'),
(65, 'Mirrors', 13, 7, 'N', 3, 'N'),
(66, 'Glass and view of the road', 13, 7, 'N', 4, 'N'),
(67, 'Accessibility features E.G. Handles', 13, 7, 'N', 5, 'N'),
(68, 'Windscreen wipers and washers', 13, 7, 'N', 6, 'N'),
(69, 'Speedometer', 13, 7, 'N', 7, 'N'),
(70, 'Horn', 13, 7, 'N', 8, 'N'),
(71, 'Driving controls', 13, 7, 'N', 9, 'N'),
(72, 'Steering control', 13, 7, 'N', 10, 'N'),
(73, 'Service brake pedal', 13, 7, 'N', 11, 'N'),
(74, 'Driver’s accommodation', 13, 7, 'N', 12, 'N'),
(75, 'Interior of body, passenger entrance, exit steps and platforms', 13, 7, 'N', 13, 'N'),
(76, 'Oil Pressure Gauge', 13, 7, 'N', 14, 'N'),
(77, 'Air Pressure Gauge', 13, 7, 'N', 15, 'N'),
(78, 'Interior Vehicle Warning Lights including ABS / EBS', 13, 7, 'N', 16, 'N'),
(79, 'Passenger doors, driver’s doors and emergency exits', 13, 8, 'Y', 1, 'N'),
(80, 'Security of body  including body bolts, cross members and chimes', 13, 8, 'Y', 2, 'N'),
(81, 'Exterior of body including luggage compartments', 13, 8, 'Y', 3, 'N'),
(82, 'Road wheels and hubs', 13, 8, 'Y', 4, 'N'),
(83, 'Condition of tyres, thread pattern and damage', 13, 8, 'Y', 5, 'N'),
(84, 'Bumper bars', 13, 8, 'Y', 6, 'N'),
(85, 'Condition of body/tail doors', 13, 8, 'Y', 7, 'N'),
(86, 'Wings and wheel arches', 13, 8, 'Y', 8, 'N'),
(87, 'Vehicle to trailer coupling and lock', 13, 8, 'Y', 9, 'N'),
(88, 'Number Plates', 13, 8, 'Y', 10, 'N'),
(89, 'Electrical equipment and wiring', 13, 8, 'Y', 11, 'N'),
(90, 'Engine and transmission mountings', 13, 8, 'Y', 12, 'N'),
(91, 'Oil and waste leaks', 13, 8, 'Y', 13, 'N'),
(92, 'Fuel tanks and system', 13, 8, 'Y', 14, 'N'),
(93, 'Exhaust and waste systems', 13, 8, 'Y', 15, 'N'),
(94, 'Steering mechanism', 13, 8, 'Y', 16, 'N'),
(95, 'Suspension', 13, 8, 'Y', 17, 'N'),
(96, 'Axles, stub axles and wheel bearings', 13, 8, 'Y', 18, 'N'),
(97, 'Brake Disc', 13, 8, 'Y', 19, 'N'),
(98, 'Brake Pad', 13, 8, 'Y', 20, 'N'),
(99, 'Brake Chambers', 13, 8, 'Y', 21, 'N'),
(100, 'Brake Return Springs', 13, 8, 'Y', 22, 'N'),
(101, 'Brake Dust Covers', 13, 8, 'Y', 23, 'N'),
(102, 'Brake Slack Adjusters', 13, 8, 'Y', 24, 'N'),
(103, 'Brake Cam shaft and Bushings', 13, 8, 'Y', 25, 'N'),
(104, 'Additional braking devices', 13, 8, 'Y', 26, 'N'),
(105, 'Reflectors and rear markings', 13, 8, 'Y', 27, 'N'),
(106, 'Side Rails/Crash Barriers', 13, 8, 'Y', 28, 'N'),
(107, 'Air Leaks with Brake Not Applied', 13, 8, 'Y', 29, 'N'),
(108, 'Air Leaks with Service Brake Applied', 13, 8, 'Y', 30, 'N'),
(109, 'Air Leaks with Brake Park Applied', 13, 8, 'Y', 31, 'N'),
(110, 'Brake test Points', 13, 8, 'Y', 32, 'N'),
(111, 'Chassis Condition', 13, 8, 'Y', 33, 'N'),
(112, 'Anti Roll Bush Front', 13, 8, 'N', 34, 'N'),
(113, 'Anti Roll Bush Rear', 13, 8, 'N', 35, 'N'),
(114, 'King Pins', 13, 8, 'N', 36, 'N'),
(115, 'Track Rod Ends', 13, 8, 'N', 37, 'N'),
(116, 'Drag Link Ends', 13, 8, 'N', 38, 'N'),
(117, 'Split Pins', 13, 8, 'N', 39, 'N'),
(118, 'Spring Bush', 13, 8, 'Y', 40, 'N'),
(119, 'Spring Pins', 13, 8, 'Y', 41, 'N'),
(120, 'Springs', 13, 8, 'Y', 42, 'N'),
(121, 'Cab Bush', 13, 8, 'N', 43, 'N'),
(122, 'Belts', 13, 8, 'N', 44, 'N'),
(123, 'Radiator Bush', 13, 8, 'N', 45, 'N'),
(124, 'Drive Shaft', 13, 8, 'N', 46, 'N'),
(125, 'Hardy Spicer', 13, 8, 'N', 47, 'N'),
(126, 'Carrier Bearing', 13, 8, 'N', 48, 'N'),
(127, 'Spring Hanger', 13, 8, 'Y', 49, 'N'),
(128, 'Brake Fluid', 13, 8, 'Y', 50, 'N'),
(129, 'Power Steering Fluid', 13, 8, 'N', 51, 'N'),
(130, 'Water Leak', 13, 8, 'N', 52, 'N'),
(131, 'Road Shocks', 13, 8, 'Y', 53, 'N'),
(132, 'Load Sensing Valve', 13, 8, 'Y', 54, 'N'),
(133, 'Service brake pedal', 13, 8, 'Y', 55, 'N'),
(134, 'Service brake operation', 13, 8, 'Y', 56, 'N'),
(135, 'Pressure/vacuum warning and build-up', 13, 8, 'Y', 57, 'N'),
(136, 'Hand levers operating mechanical brakes', 13, 8, 'Y', 58, 'N'),
(137, 'Hand-operated brake control valves', 13, 8, 'Y', 59, 'N'),
(138, 'Dump Valve operation - open yellow line and brake twice.', 13, 8, 'Y', 60, 'N'),
(139, 'Steering Knuckle Joint', 13, 8, 'Y', 61, 'N'),
(140, 'Oil Change', 13, 9, 'Y', 1, 'N'),
(141, 'Oil Filter', 13, 9, 'Y', 2, 'N'),
(142, 'Fuel Filter 1', 13, 9, 'Y', 3, 'N'),
(143, 'Fuel Filter 2', 13, 9, 'Y', 4, 'N'),
(144, 'Air Filter', 13, 9, 'Y', 5, 'N'),
(145, 'Oil Level', 13, 9, 'Y', 6, 'N'),
(146, 'Water Level', 13, 9, 'Y', 7, 'N'),
(147, 'Windscreen fluid', 13, 9, 'Y', 8, 'N'),
(148, 'Grease', 13, 9, 'Y', 9, 'N'),
(149, 'Gear Box Oil', 13, 9, 'Y', 10, 'N'),
(150, 'Diff Oil', 13, 9, 'Y', 11, 'N'),
(151, 'Clutch Fluid', 13, 9, 'Y', 12, 'N'),
(152, 'ABS / EBS Functionality', 13, 10, 'Y', 1, 'N'),
(153, 'Excessive Smoke', 13, 10, 'Y', 2, 'N'),
(154, 'Tipping Bar at Rear', 13, 10, 'Y', 3, 'N'),
(155, 'Tipping Ram', 13, 10, 'Y', 4, 'N'),
(156, 'Side Securing pins for tipping Bodies', 13, 10, 'Y', 5, 'N'),
(157, 'Exterior paint condition', 13, 10, 'Y', 6, 'N'),
(158, 'Serial/Chassis Number Condition', 13, 10, 'Y', 7, 'N'),
(159, 'Trailer Floor Condition', 13, 10, 'Y', 8, 'N'),
(160, 'Trailer Ramp Condition Including Hinges and Locks', 13, 10, 'Y', 9, 'N'),
(161, 'Trailer Coupling', 13, 10, 'Y', 10, 'N'),
(162, 'Trailer Draw Bar Condition', 13, 10, 'Y', 11, 'N'),
(163, 'Condition of CV Boot', 13, 10, 'Y', 12, 'N'),
(164, 'Condition of Brake Pipes', 13, 10, 'Y', 13, 'N'),
(165, 'Condition of Front U Bolts (inc tightness)', 13, 10, 'Y', 14, 'N'),
(166, 'Condition of Rear U Bolts (inc tightness)', 13, 10, 'Y', 15, 'N'),
(167, 'Grease Points Lubrication', 13, 10, 'Y', 16, 'N'),
(168, 'Notes /  Parts List', 12, 11, 'Y', 1, 'N'),
(169, 'Inspection completed by', 11, 11, 'Y', 2, 'N'),
(170, 'Inspection supervised', 13, 11, 'Y', 3, 'N');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_question_types`
--

DROP TABLE IF EXISTS `tbl_question_types`;
CREATE TABLE IF NOT EXISTS `tbl_question_types` (
  `type_ID` int(3) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  PRIMARY KEY (`type_ID`),
  KEY `type_ID` (`type_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `tbl_question_types`
--

INSERT INTO `tbl_question_types` (`type_ID`, `type_name`) VALUES
(11, 'Text'),
(12, 'Textarea'),
(13, 'Dropdown'),
(14, 'Date'),
(15, 'Checkbox'),
(16, 'Number');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sections`
--

DROP TABLE IF EXISTS `tbl_sections`;
CREATE TABLE IF NOT EXISTS `tbl_sections` (
  `section_ID` int(50) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `section_seqno` int(3) NOT NULL,
  `ind_trailers` varchar(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`section_ID`),
  KEY `section_ID` (`section_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `tbl_sections`
--

INSERT INTO `tbl_sections` (`section_ID`, `section_name`, `section_seqno`, `ind_trailers`) VALUES
(1, 'Vehicle Details', 1, 'Y'),
(2, 'Brake Performance', 2, 'Y'),
(3, 'Tyre Thread Remaining', 3, 'Y'),
(4, 'Lubrication', 4, 'Y'),
(5, 'Lights', 5, 'Y'),
(6, 'Tachograph/Speed Limiter', 6, 'N'),
(7, 'Inside Cab', 7, 'N'),
(8, 'Ground Level And Under Vehicle', 8, 'Y'),
(9, 'Small Service', 9, 'N'),
(10, 'Additional (Road Test)', 10, 'Y'),
(11, 'Inspection Report Details', 11, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_surveys`
--

DROP TABLE IF EXISTS `tbl_surveys`;
CREATE TABLE IF NOT EXISTS `tbl_surveys` (
  `survey_ID` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_type` varchar(250) NOT NULL,
  `vehicle_reg` varchar(50) NOT NULL,
  `company_ID` int(11) NOT NULL,
  `make_model` varchar(100) NOT NULL,
  `odo_reading` int(11) NOT NULL,
  `odo_type` varchar(50) NOT NULL,
  `pre_service_remarks` text NOT NULL,
  `notes_parts_list` text NOT NULL,
  `completed_by_user_ID` int(11) NOT NULL,
  `supervised_by_user_ID` int(11) DEFAULT NULL,
  `survey_date` date NOT NULL,
  `status_id` int(11) NOT NULL,
  `date_last_update` datetime NOT NULL,
  `user_last_update` int(11) NOT NULL,
  PRIMARY KEY (`survey_ID`),
  KEY `company_ID` (`company_ID`),
  KEY `completed_by_user_ID` (`completed_by_user_ID`),
  KEY `user_last_update` (`user_last_update`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Dumping data for table `tbl_surveys`
--

INSERT INTO `tbl_surveys` (`survey_ID`, `vehicle_type`, `vehicle_reg`, `company_ID`, `make_model`, `odo_reading`, `odo_type`, `pre_service_remarks`, `notes_parts_list`, `completed_by_user_ID`, `supervised_by_user_ID`, `survey_date`, `status_id`, `date_last_update`, `user_last_update`) VALUES
(58, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(59, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(60, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(61, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(62, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(63, 'lorry', 'DEX8998', 1, 'FORD BOYO', 987654, 'km', 'Some remarks', 'Need some bits and bobs for this report', 2, 1, '0000-00-00', 3, '0000-00-00 00:00:00', 2),
(64, 'lorry', 'Test1234', 1, 'lorry car thing', 12455, 'km', '', '', 2, NULL, '0000-00-00', 2, '0000-00-00 00:00:00', 2),
(65, 'lorry', 'kmlkj', 1, ';/lk;lo', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(66, 'lorry', 'ABC1234', 1, 'Ford Focus', 986542, 'km', '', 'This is just a test.', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(67, 'lorry', 'DEX4321', 1, 'Renault Megane', 90000, 'km', '', 'This is another test', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(68, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(69, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(70, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(71, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(72, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 2, NULL, '2014-12-18', 2, '0000-00-00 00:00:00', 5),
(73, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 5, NULL, '2014-12-18', 2, '2014-12-18 00:00:00', 5),
(74, 'lorry', 'shshh', 1, 'shs', 0, 'km', '', '', 5, NULL, '2014-12-18', 2, '2014-12-18 16:31:04', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_axle_responses`
--

DROP TABLE IF EXISTS `tbl_survey_axle_responses`;
CREATE TABLE IF NOT EXISTS `tbl_survey_axle_responses` (
  `response_ID` int(11) NOT NULL AUTO_INCREMENT,
  `survey_ID` int(11) NOT NULL,
  `question_ID` varchar(50) NOT NULL,
  `question_response` float NOT NULL,
  PRIMARY KEY (`response_ID`),
  UNIQUE KEY `response_ID` (`response_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=373 ;

--
-- Dumping data for table `tbl_survey_axle_responses`
--

INSERT INTO `tbl_survey_axle_responses` (`response_ID`, `survey_ID`, `question_ID`, `question_response`) VALUES
(219, 58, 'axle_1_service_bk_dec', 75),
(220, 58, 'axle_1_service_bk_imb', 85),
(221, 58, 'axle_1_parking_bk_dec', 95),
(222, 58, 'axle_1_parking_bk_imb', 65),
(223, 58, 'axle_2_service_bk_dec', 62),
(224, 58, 'axle_2_service_bk_imb', 52),
(225, 58, 'axle_2_parking_bk_dec', 84),
(226, 58, 'axle_2_parking_bk_imb', 84),
(227, 58, 'axle_1_inner_near', 25),
(228, 58, 'axle_1_inner_off', 25),
(229, 58, 'axle_1_outer_near', 25),
(230, 58, 'axle_1_outer_off', 25),
(231, 58, 'axle_2_inner_near', 10),
(232, 58, 'axle_2_inner_off', 8),
(233, 58, 'axle_2_outer_near', 9),
(234, 58, 'axle_2_outer_off', 7),
(235, 59, 'axle_1_service_bk_dec', 75),
(236, 59, 'axle_1_service_bk_imb', 85),
(237, 59, 'axle_1_parking_bk_dec', 95),
(238, 59, 'axle_1_parking_bk_imb', 65),
(239, 59, 'axle_2_service_bk_dec', 62),
(240, 59, 'axle_2_service_bk_imb', 52),
(241, 59, 'axle_2_parking_bk_dec', 84),
(242, 59, 'axle_2_parking_bk_imb', 84),
(243, 59, 'axle_1_inner_near', 25),
(244, 59, 'axle_1_inner_off', 25),
(245, 59, 'axle_1_outer_near', 25),
(246, 59, 'axle_1_outer_off', 25),
(247, 59, 'axle_2_inner_near', 10),
(248, 59, 'axle_2_inner_off', 8),
(249, 59, 'axle_2_outer_near', 9),
(250, 59, 'axle_2_outer_off', 7),
(251, 60, 'axle_1_service_bk_dec', 75),
(252, 60, 'axle_1_service_bk_imb', 85),
(253, 60, 'axle_1_parking_bk_dec', 95),
(254, 60, 'axle_1_parking_bk_imb', 65),
(255, 60, 'axle_2_service_bk_dec', 62),
(256, 60, 'axle_2_service_bk_imb', 52),
(257, 60, 'axle_2_parking_bk_dec', 84),
(258, 60, 'axle_2_parking_bk_imb', 84),
(259, 60, 'axle_1_inner_near', 25),
(260, 60, 'axle_1_inner_off', 25),
(261, 60, 'axle_1_outer_near', 25),
(262, 60, 'axle_1_outer_off', 25),
(263, 60, 'axle_2_inner_near', 10),
(264, 60, 'axle_2_inner_off', 8),
(265, 60, 'axle_2_outer_near', 9),
(266, 60, 'axle_2_outer_off', 7),
(267, 61, 'axle_1_service_bk_dec', 75),
(268, 61, 'axle_1_service_bk_imb', 85),
(269, 61, 'axle_1_parking_bk_dec', 95),
(270, 61, 'axle_1_parking_bk_imb', 65),
(271, 61, 'axle_2_service_bk_dec', 62),
(272, 61, 'axle_2_service_bk_imb', 52),
(273, 61, 'axle_2_parking_bk_dec', 84),
(274, 61, 'axle_2_parking_bk_imb', 84),
(275, 61, 'axle_1_inner_near', 25),
(276, 61, 'axle_1_inner_off', 25),
(277, 61, 'axle_1_outer_near', 25),
(278, 61, 'axle_1_outer_off', 25),
(279, 61, 'axle_2_inner_near', 10),
(280, 61, 'axle_2_inner_off', 8),
(281, 61, 'axle_2_outer_near', 9),
(282, 61, 'axle_2_outer_off', 7),
(283, 62, 'axle_1_service_bk_dec', 75),
(284, 62, 'axle_1_service_bk_imb', 85),
(285, 62, 'axle_1_parking_bk_dec', 95),
(286, 62, 'axle_1_parking_bk_imb', 65),
(287, 62, 'axle_2_service_bk_dec', 62),
(288, 62, 'axle_2_service_bk_imb', 52),
(289, 62, 'axle_2_parking_bk_dec', 84),
(290, 62, 'axle_2_parking_bk_imb', 84),
(291, 62, 'axle_1_inner_near', 25),
(292, 62, 'axle_1_inner_off', 25),
(293, 62, 'axle_1_outer_near', 25),
(294, 62, 'axle_1_outer_off', 25),
(295, 62, 'axle_2_inner_near', 10),
(296, 62, 'axle_2_inner_off', 8),
(297, 62, 'axle_2_outer_near', 9),
(298, 62, 'axle_2_outer_off', 7),
(299, 63, 'axle_1_service_bk_dec', 75),
(300, 63, 'axle_1_service_bk_imb', 85),
(301, 63, 'axle_1_parking_bk_dec', 95),
(302, 63, 'axle_1_parking_bk_imb', 65),
(303, 63, 'axle_2_service_bk_dec', 62),
(304, 63, 'axle_2_service_bk_imb', 52),
(305, 63, 'axle_2_parking_bk_dec', 84),
(306, 63, 'axle_2_parking_bk_imb', 84),
(307, 63, 'axle_1_inner_near', 25),
(308, 63, 'axle_1_inner_off', 25),
(309, 63, 'axle_1_outer_near', 25),
(310, 63, 'axle_1_outer_off', 25),
(311, 63, 'axle_2_inner_near', 10),
(312, 63, 'axle_2_inner_off', 8),
(313, 63, 'axle_2_outer_near', 9),
(314, 63, 'axle_2_outer_off', 7),
(315, 64, 'axle_1_service_bk_dec', 12),
(316, 64, 'axle_1_inner_off', 2),
(317, 66, 'axle_1_service_bk_dec', 80),
(318, 66, 'axle_1_service_bk_imb', 80),
(319, 66, 'axle_1_parking_bk_dec', 80),
(320, 66, 'axle_1_parking_bk_imb', 80),
(321, 66, 'axle_2_service_bk_dec', 80),
(322, 66, 'axle_2_service_bk_imb', 80),
(323, 66, 'axle_2_parking_bk_dec', 80),
(324, 66, 'axle_2_parking_bk_imb', 80),
(325, 66, 'axle_3_service_bk_dec', 50),
(326, 66, 'axle_3_service_bk_imb', 50),
(327, 66, 'axle_3_parking_bk_dec', 50),
(328, 66, 'axle_3_parking_bk_imb', 50),
(329, 66, 'axle_4_service_bk_dec', 70),
(330, 66, 'axle_4_service_bk_imb', 70),
(331, 66, 'axle_4_parking_bk_dec', 70),
(332, 66, 'axle_4_parking_bk_imb', 70),
(333, 66, 'axle_1_inner_near', 8),
(334, 66, 'axle_1_inner_off', 8),
(335, 66, 'axle_1_outer_near', 8),
(336, 66, 'axle_1_outer_off', 8),
(337, 67, 'axle_1_service_bk_dec', 90),
(338, 67, 'axle_1_service_bk_imb', 80),
(339, 67, 'axle_1_parking_bk_dec', 70),
(340, 67, 'axle_1_parking_bk_imb', 60),
(341, 67, 'axle_2_service_bk_dec', 90),
(342, 67, 'axle_2_service_bk_imb', 50),
(343, 67, 'axle_2_parking_bk_dec', 30),
(344, 67, 'axle_2_parking_bk_imb', 20),
(345, 67, 'axle_1_inner_near', 8),
(346, 67, 'axle_1_inner_off', 8),
(347, 67, 'axle_1_outer_near', 8),
(348, 67, 'axle_1_outer_off', 8),
(349, 68, 'axle_1_service_bk_dec', 50),
(350, 68, 'axle_1_service_bk_imb', 50),
(351, 68, 'axle_1_parking_bk_dec', 50),
(352, 68, 'axle_1_parking_bk_imb', 50),
(353, 68, 'axle_2_service_bk_dec', 90),
(354, 68, 'axle_2_service_bk_imb', 60),
(355, 68, 'axle_2_parking_bk_dec', 80),
(356, 68, 'axle_2_parking_bk_imb', 88),
(357, 69, 'axle_1_service_bk_dec', 50),
(358, 69, 'axle_1_service_bk_imb', 50),
(359, 69, 'axle_1_parking_bk_dec', 50),
(360, 69, 'axle_1_parking_bk_imb', 50),
(361, 69, 'axle_2_service_bk_dec', 90),
(362, 69, 'axle_2_service_bk_imb', 60),
(363, 69, 'axle_2_parking_bk_dec', 80),
(364, 69, 'axle_2_parking_bk_imb', 88),
(365, 70, 'axle_1_service_bk_dec', 50),
(366, 70, 'axle_1_service_bk_imb', 50),
(367, 70, 'axle_1_parking_bk_dec', 50),
(368, 70, 'axle_1_parking_bk_imb', 50),
(369, 70, 'axle_2_service_bk_dec', 90),
(370, 70, 'axle_2_service_bk_imb', 60),
(371, 70, 'axle_2_parking_bk_dec', 80),
(372, 70, 'axle_2_parking_bk_imb', 88);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_responses`
--

DROP TABLE IF EXISTS `tbl_survey_responses`;
CREATE TABLE IF NOT EXISTS `tbl_survey_responses` (
  `response_ID` int(11) NOT NULL AUTO_INCREMENT,
  `survey_ID` int(11) NOT NULL,
  `question_ID` varchar(255) NOT NULL,
  `question_response` varchar(255) NOT NULL,
  PRIMARY KEY (`response_ID`),
  UNIQUE KEY `response_ID` (`response_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=840 ;

--
-- Dumping data for table `tbl_survey_responses`
--

INSERT INTO `tbl_survey_responses` (`response_ID`, `survey_ID`, `question_ID`, `question_response`) VALUES
(591, 58, 'veh_lub_31', 'not-ok'),
(592, 58, 'veh_lub_31_details', 'Something needs fixed'),
(593, 58, 'veh_lub_33', 'ok'),
(594, 58, 'veh_lub_35', 'ok'),
(595, 58, 'veh_lub_37', 'ok'),
(596, 58, 'veh_lub_39', 'ok'),
(597, 58, 'veh_lights_41', 'significant_defect'),
(598, 58, 'veh_lights_rectified_by_41', '1'),
(599, 58, 'veh_lights_details_41', 'Something is really broken here'),
(600, 58, 'veh_lights_44', 'satisfactory'),
(601, 58, 'veh_lights_45', 'satisfactory'),
(602, 58, 'veh_lights_46', 'significant_defect'),
(603, 58, 'veh_lights_details_46', 'Something badly needs fixed here'),
(604, 58, 'veh_lights_47', 'satisfactory'),
(605, 58, 'veh_lights_48', 'satisfactory'),
(606, 58, 'veh_lights_49', 'satisfactory'),
(607, 58, 'veh_lights_50', 'satisfactory'),
(608, 58, 'veh_lights_51', 'slight_defect'),
(609, 58, 'veh_lights_details_51', 'This is kind of ok but could do with a looking at'),
(610, 58, 'veh_lights_52', 'satisfactory'),
(611, 58, 'veh_lights_53', 'satisfactory'),
(612, 58, 'veh_lights_56', 'satisfactory'),
(613, 58, 'veh_lights_54', 'satisfactory'),
(614, 58, 'veh_lights_55', 'satisfactory'),
(615, 58, 'veh_lights_57', 'satisfactory'),
(616, 58, 'veh_tacho_58', 'significant_defect'),
(617, 58, 'veh_tacho_details_58', 'Busted up'),
(618, 58, 'veh_tacho_59', 'satisfactory'),
(619, 58, 'veh_tacho_60', 'satisfactory'),
(620, 58, 'veh_tacho_61', 'satisfactory'),
(621, 58, 'veh_tacho_62', 'satisfactory'),
(622, 58, 'veh_insidecab_63', 'significant_defect'),
(623, 58, 'veh_insidecab_details_63', 'Major issue here'),
(624, 58, 'veh_insidecab_64', 'slight_defect'),
(625, 58, 'veh_insidecab_details_64', 'This could do with a looking at'),
(626, 58, 'veh_insidecab_65', 'satisfactory'),
(627, 58, 'veh_insidecab_66', 'satisfactory'),
(628, 58, 'veh_insidecab_67', 'satisfactory'),
(629, 58, 'veh_insidecab_68', 'satisfactory'),
(630, 58, 'veh_insidecab_69', 'satisfactory'),
(631, 58, 'veh_insidecab_70', 'satisfactory'),
(632, 58, 'veh_insidecab_71', 'satisfactory'),
(633, 58, 'veh_insidecab_72', 'satisfactory'),
(634, 58, 'veh_insidecab_73', 'satisfactory'),
(635, 58, 'veh_insidecab_74', 'satisfactory'),
(636, 58, 'veh_insidecab_75', 'satisfactory'),
(637, 58, 'veh_insidecab_76', 'satisfactory'),
(638, 58, 'veh_insidecab_77', 'satisfactory'),
(639, 58, 'veh_insidecab_78', 'satisfactory'),
(640, 58, 'veh_glevel_79', 'satisfactory'),
(641, 58, 'veh_glevel_80', 'satisfactory'),
(642, 58, 'veh_glevel_81', 'satisfactory'),
(643, 58, 'veh_glevel_82', 'satisfactory'),
(644, 58, 'veh_glevel_83', 'satisfactory'),
(645, 58, 'veh_glevel_84', 'satisfactory'),
(646, 58, 'veh_glevel_85', 'satisfactory'),
(647, 58, 'veh_glevel_86', 'satisfactory'),
(648, 58, 'veh_glevel_87', 'satisfactory'),
(649, 58, 'veh_glevel_88', 'satisfactory'),
(650, 58, 'veh_glevel_89', 'satisfactory'),
(651, 58, 'veh_glevel_90', 'satisfactory'),
(652, 58, 'veh_glevel_91', 'satisfactory'),
(653, 58, 'veh_glevel_92', 'satisfactory'),
(654, 58, 'veh_glevel_93', 'satisfactory'),
(655, 58, 'veh_glevel_94', 'satisfactory'),
(656, 58, 'veh_glevel_95', 'satisfactory'),
(657, 58, 'veh_glevel_96', 'satisfactory'),
(658, 58, 'veh_glevel_97', 'satisfactory'),
(659, 58, 'veh_glevel_98', 'satisfactory'),
(660, 58, 'veh_glevel_99', 'satisfactory'),
(661, 58, 'veh_glevel_100', 'satisfactory'),
(662, 58, 'veh_glevel_101', 'satisfactory'),
(663, 58, 'veh_glevel_102', 'satisfactory'),
(664, 58, 'veh_glevel_103', 'satisfactory'),
(665, 58, 'veh_glevel_104', 'satisfactory'),
(666, 58, 'veh_glevel_105', 'satisfactory'),
(667, 58, 'veh_glevel_106', 'satisfactory'),
(668, 58, 'veh_glevel_107', 'satisfactory'),
(669, 58, 'veh_glevel_108', 'satisfactory'),
(670, 58, 'veh_glevel_109', 'satisfactory'),
(671, 58, 'veh_glevel_110', 'satisfactory'),
(672, 58, 'veh_glevel_111', 'satisfactory'),
(673, 58, 'veh_glevel_112', 'satisfactory'),
(674, 58, 'veh_glevel_113', 'satisfactory'),
(675, 58, 'veh_glevel_114', 'satisfactory'),
(676, 58, 'veh_glevel_115', 'satisfactory'),
(677, 58, 'veh_glevel_116', 'satisfactory'),
(678, 58, 'veh_glevel_117', 'satisfactory'),
(679, 58, 'veh_glevel_118', 'satisfactory'),
(680, 58, 'veh_glevel_119', 'satisfactory'),
(681, 58, 'veh_glevel_120', 'satisfactory'),
(682, 58, 'veh_glevel_121', 'satisfactory'),
(683, 58, 'veh_glevel_122', 'satisfactory'),
(684, 58, 'veh_glevel_123', 'satisfactory'),
(685, 58, 'veh_glevel_124', 'satisfactory'),
(686, 58, 'veh_glevel_125', 'satisfactory'),
(687, 58, 'veh_glevel_126', 'satisfactory'),
(688, 58, 'veh_glevel_127', 'satisfactory'),
(689, 58, 'veh_glevel_128', 'satisfactory'),
(690, 58, 'veh_glevel_129', 'satisfactory'),
(691, 58, 'veh_glevel_130', 'satisfactory'),
(692, 58, 'veh_glevel_131', 'satisfactory'),
(693, 58, 'veh_glevel_132', 'satisfactory'),
(694, 58, 'veh_glevel_133', 'satisfactory'),
(695, 58, 'veh_glevel_134', 'satisfactory'),
(696, 58, 'veh_glevel_135', 'satisfactory'),
(697, 58, 'veh_glevel_136', 'satisfactory'),
(698, 58, 'veh_glevel_137', 'satisfactory'),
(699, 58, 'veh_glevel_138', 'satisfactory'),
(700, 58, 'veh_glevel_139', 'satisfactory'),
(701, 58, 'veh_smallservice_140', 'satisfactory'),
(702, 58, 'veh_smallservice_141', 'satisfactory'),
(703, 58, 'veh_smallservice_142', 'satisfactory'),
(704, 58, 'veh_smallservice_143', 'satisfactory'),
(705, 58, 'veh_smallservice_144', 'satisfactory'),
(706, 58, 'veh_smallservice_145', 'satisfactory'),
(707, 58, 'veh_smallservice_146', 'satisfactory'),
(708, 58, 'veh_smallservice_147', 'satisfactory'),
(709, 58, 'veh_smallservice_148', 'satisfactory'),
(710, 58, 'veh_smallservice_149', 'satisfactory'),
(711, 58, 'veh_smallservice_150', 'satisfactory'),
(712, 58, 'veh_smallservice_151', 'satisfactory'),
(713, 61, 'veh_lub_31', 'not-ok'),
(714, 61, 'veh_lub_31_details', 'Something needs fixed'),
(715, 61, 'veh_lub_33', 'ok'),
(716, 61, 'veh_lub_35', 'ok'),
(717, 61, 'veh_lub_37', 'ok'),
(718, 61, 'veh_lub_39', 'ok'),
(719, 61, 'veh_lights_41', 'significant_defect'),
(720, 61, 'veh_lights_rectified_by_41', '1'),
(721, 61, 'veh_lights_details_41', 'Something is really broken here'),
(722, 61, 'veh_lights_46', 'significant_defect'),
(723, 61, 'veh_lights_details_46', 'Something badly needs fixed here'),
(724, 61, 'veh_lights_51', 'slight_defect'),
(725, 61, 'veh_lights_details_51', 'This is kind of ok but could do with a looking at'),
(726, 61, 'veh_tacho_58', 'significant_defect'),
(727, 61, 'veh_tacho_details_58', 'Busted up'),
(728, 61, 'veh_insidecab_63', 'significant_defect'),
(729, 61, 'veh_insidecab_details_63', 'Major issue here'),
(730, 61, 'veh_insidecab_64', 'slight_defect'),
(731, 61, 'veh_insidecab_details_64', 'This could do with a looking at'),
(732, 62, 'veh_lub_31', 'not-ok'),
(733, 62, 'veh_lub_31_details', 'Something needs fixed'),
(734, 62, 'veh_lub_33', 'ok'),
(735, 62, 'veh_lub_35', 'ok'),
(736, 62, 'veh_lub_37', 'ok'),
(737, 62, 'veh_lub_39', 'ok'),
(738, 62, 'veh_lights_41', 'significant_defect'),
(739, 62, 'veh_lights_rectified_by_41', '1'),
(740, 62, 'veh_lights_details_41', 'Something is really broken here'),
(741, 62, 'veh_lights_46', 'significant_defect'),
(742, 62, 'veh_lights_details_46', 'Something badly needs fixed here'),
(743, 62, 'veh_lights_51', 'slight_defect'),
(744, 62, 'veh_lights_details_51', 'This is kind of ok but could do with a looking at'),
(745, 62, 'veh_tacho_58', 'significant_defect'),
(746, 62, 'veh_tacho_details_58', 'Busted up'),
(747, 62, 'veh_insidecab_63', 'significant_defect'),
(748, 62, 'veh_insidecab_details_63', 'Major issue here'),
(749, 62, 'veh_insidecab_64', 'slight_defect'),
(750, 62, 'veh_insidecab_details_64', 'This could do with a looking at'),
(751, 63, 'veh_lub_31', 'not-ok'),
(752, 63, 'veh_lub_31_details', 'Something needs fixed'),
(753, 63, 'veh_lub_33', 'ok'),
(754, 63, 'veh_lub_35', 'ok'),
(755, 63, 'veh_lub_37', 'ok'),
(756, 63, 'veh_lub_39', 'ok'),
(757, 63, 'veh_lights_41', 'significant_defect'),
(758, 63, 'veh_lights_rectified_by_41', '1'),
(759, 63, 'veh_lights_details_41', 'Something is really broken here'),
(760, 63, 'veh_lights_46', 'significant_defect'),
(761, 63, 'veh_lights_details_46', 'Something badly needs fixed here'),
(762, 63, 'veh_lights_51', 'slight_defect'),
(763, 63, 'veh_lights_details_51', 'This is kind of ok but could do with a looking at'),
(764, 63, 'veh_tacho_58', 'significant_defect'),
(765, 63, 'veh_tacho_details_58', 'Busted up'),
(766, 63, 'veh_insidecab_63', 'significant_defect'),
(767, 63, 'veh_insidecab_details_63', 'Major issue here'),
(768, 63, 'veh_insidecab_64', 'slight_defect'),
(769, 63, 'veh_insidecab_details_64', 'This could do with a looking at'),
(770, 64, 'veh_lub_31', 'not-ok'),
(771, 64, 'veh_lub_31_details', 'bad'),
(772, 64, 'veh_lub_33', 'ok'),
(773, 64, 'veh_lub_35', 'ok'),
(774, 64, 'veh_lub_37', 'ok'),
(775, 64, 'veh_lub_39', 'ok'),
(776, 65, 'veh_lub_31', 'not-ok'),
(777, 65, 'veh_lub_33', 'ok'),
(778, 65, 'veh_lub_35', 'ok'),
(779, 65, 'veh_lub_37', 'ok'),
(780, 65, 'veh_lub_39', 'ok'),
(781, 65, 'veh_lights_44', 'significant_defect'),
(782, 65, 'veh_lights_45', 'slight_defect'),
(783, 66, 'veh_lub_31', 'not-ok'),
(784, 66, 'veh_lub_31_details', 'Issue fault'),
(785, 66, 'veh_lub_33', 'not-ok'),
(786, 66, 'veh_lub_33_details', 'Another fault'),
(787, 66, 'veh_lub_35', 'ok'),
(788, 66, 'veh_lub_37', 'ok'),
(789, 66, 'veh_lub_39', 'ok'),
(790, 66, 'veh_lights_55', 'significant_defect'),
(791, 66, 'veh_lights_details_55', 'Major issue here'),
(792, 66, 'veh_lights_57', 'slight_defect'),
(793, 66, 'veh_lights_details_57', 'Slight issue here'),
(794, 66, 'veh_tacho_58', 'slight_defect'),
(795, 66, 'veh_tacho_details_58', 'Slight issue here'),
(796, 67, 'veh_lub_31', 'not-ok'),
(797, 67, 'veh_lub_31_details', 'Issue found with these lubs'),
(798, 67, 'veh_lub_33', 'ok'),
(799, 67, 'veh_lub_35', 'ok'),
(800, 67, 'veh_lub_37', 'ok'),
(801, 67, 'veh_lub_39', 'ok'),
(802, 67, 'veh_lights_41', 'significant_defect'),
(803, 67, 'veh_lights_details_41', 'Major issue with the tail lgihts'),
(804, 67, 'veh_lights_44', 'slight_defect'),
(805, 67, 'veh_lights_details_44', 'Slight issue with the side lights'),
(806, 67, 'veh_lights_53', 'significant_defect'),
(807, 67, 'veh_lights_details_53', 'Beacons not working at all'),
(808, 67, 'veh_smallservice_140', 'slight_defect'),
(809, 67, 'veh_smallservice_details_140', 'Oil change'),
(810, 68, 'veh_lub_31', 'not-ok'),
(811, 68, 'veh_lub_31_details', 'Issue'),
(812, 68, 'veh_lub_33', 'ok'),
(813, 68, 'veh_lub_35', 'ok'),
(814, 68, 'veh_lub_37', 'ok'),
(815, 68, 'veh_lub_39', 'ok'),
(816, 68, 'veh_tacho_58', 'slight_defect'),
(817, 68, 'veh_tacho_details_58', 'Slight issue here'),
(818, 68, 'veh_glevel_81', 'significant_defect'),
(819, 68, 'veh_glevel_details_81', 'Major issue here'),
(820, 69, 'veh_lub_31', 'not-ok'),
(821, 69, 'veh_lub_31_details', 'Issue'),
(822, 69, 'veh_lub_33', 'ok'),
(823, 69, 'veh_lub_35', 'ok'),
(824, 69, 'veh_lub_37', 'ok'),
(825, 69, 'veh_lub_39', 'ok'),
(826, 69, 'veh_tacho_58', 'slight_defect'),
(827, 69, 'veh_tacho_details_58', 'Slight issue here'),
(828, 69, 'veh_glevel_81', 'significant_defect'),
(829, 69, 'veh_glevel_details_81', 'Major issue here'),
(830, 70, 'veh_lub_31', 'not-ok'),
(831, 70, 'veh_lub_31_details', 'Issue'),
(832, 70, 'veh_lub_33', 'ok'),
(833, 70, 'veh_lub_35', 'ok'),
(834, 70, 'veh_lub_37', 'ok'),
(835, 70, 'veh_lub_39', 'ok'),
(836, 70, 'veh_tacho_58', 'slight_defect'),
(837, 70, 'veh_tacho_details_58', 'Slight issue here'),
(838, 70, 'veh_glevel_81', 'significant_defect'),
(839, 70, 'veh_glevel_details_81', 'Major issue here');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_survey_statuses`
--

DROP TABLE IF EXISTS `tbl_survey_statuses`;
CREATE TABLE IF NOT EXISTS `tbl_survey_statuses` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(20) NOT NULL,
  PRIMARY KEY (`status_id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_survey_statuses`
--

INSERT INTO `tbl_survey_statuses` (`status_id`, `status_name`) VALUES
(1, 'draft'),
(2, 'pending'),
(3, 'final'),
(4, 'archive');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int(50) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `tel_no` varchar(50) NOT NULL,
  `user_role_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `salt` char(16) NOT NULL,
  `password` char(64) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_role_id` (`user_role_id`),
  KEY `user_role_id_2` (`user_role_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `username`, `first_name`, `last_name`, `title`, `email`, `tel_no`, `user_role_id`, `company_id`, `salt`, `password`) VALUES
(1, 'mick', 'Mick', 'Corr', 'Manager', 'accounts@corrbrothers.co.uk', '+44 (0) 28 3752 5245', 1, 2, '5782846d22bde9ec', 'c642c8fc27fbc4838080564e810eb9b52fe0d44245d2f6a4030805cf1eb5ca43'),
(2, 'gareth', 'Gareth', 'Watson', 'Developer', 'gareth@websiteni.com', '(028) 3754 9025', 3, 1, '', ''),
(5, 'gdpwatson', 'Gareth', 'Watson', 'Mr', 'gdpwatson@gmail.com', '07725319947', 1, 1, 'a9048675744c521', '0ba4fb46a885fc700642aa4f7b7716d4ee8adf5bbacdd4311f1cde41a5df9865'),
(13, 'jimmarley', 'Jim', 'Marley', 'Production Manager', 'jim@websiteni.com', '123456', 1, 1, '619b3310498f5e35', 'd3278d3bfd89723f0bc21b020980040ec9e998d19f74e903324ac49c03b13ea3'),
(14, 'keithman', 'Keith', 'Man', 'Senior Guy', 'tester@email.com', '1234', 2, 2, 'eeb0f1e44922297', 'a614988f67f5a8647349446131db879a60c2a4b7fd2de3772b6b24f34a4a36e4'),
(15, 'dconlon', 'Daithi', 'Conlon', 'DM', 'dconlon@websiteni.com', '12345', 3, 1, '6254e8c662c13809', 'd4c875be557b92ace6c3c65033e1aab154d6b789e58c7e21007ae760dc6ae426'),
(16, 'lboylan', 'Linda', 'Boylan', 'Sales and Marketing', 'linda@websiteni.com', '1234', 2, 1, 'b58edc86864e6c2', '73db3a9479d8162238914697a86fc985c0b3da01bbead63efccf7b0c192a11e9');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_roles`
--

DROP TABLE IF EXISTS `tbl_user_roles`;
CREATE TABLE IF NOT EXISTS `tbl_user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`user_role_id`),
  KEY `user_role_id` (`user_role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tbl_user_roles`
--

INSERT INTO `tbl_user_roles` (`user_role_id`, `role`) VALUES
(1, 'Manager'),
(2, 'Garage'),
(3, 'Customer');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_questions`
--
ALTER TABLE `tbl_questions`
  ADD CONSTRAINT `tbl_questions_ibfk_1` FOREIGN KEY (`section_ID`) REFERENCES `tbl_sections` (`section_ID`),
  ADD CONSTRAINT `tbl_questions_ibfk_2` FOREIGN KEY (`type_ID`) REFERENCES `tbl_question_types` (`type_ID`);

--
-- Constraints for table `tbl_surveys`
--
ALTER TABLE `tbl_surveys`
  ADD CONSTRAINT `tbl_surveys_ibfk_1` FOREIGN KEY (`company_ID`) REFERENCES `tbl_companies` (`company_ID`),
  ADD CONSTRAINT `tbl_surveys_ibfk_2` FOREIGN KEY (`completed_by_user_ID`) REFERENCES `tbl_users` (`user_id`),
  ADD CONSTRAINT `tbl_surveys_ibfk_4` FOREIGN KEY (`user_last_update`) REFERENCES `tbl_users` (`user_id`),
  ADD CONSTRAINT `tbl_surveys_ibfk_5` FOREIGN KEY (`status_id`) REFERENCES `tbl_survey_statuses` (`status_id`);

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `tbl_user_roles` (`user_role_id`),
  ADD CONSTRAINT `tbl_users_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `tbl_companies` (`company_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
