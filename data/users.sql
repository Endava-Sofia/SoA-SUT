-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2016 at 03:13 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE DATABASE IF NOT EXISTS db;
USE db;

CREATE TABLE IF NOT EXISTS users (
  id int(11) NOT NULL AUTO_INCREMENT,
  is_admin BOOLEAN,
  first_name varchar(100) NOT NULL,
  sir_name varchar(100) NOT NULL,
  title ENUM('Mr.', 'Mrs.'),
  country varchar(100),
  city varchar(100),
  email varchar(100) NOT NULL,
  password varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (email)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


INSERT IGNORE INTO users (id, is_admin, first_name, sir_name, email, title, country, city, password) 
VALUES 
(1, true, 'Admin', 'Automation', 'admin@automation.com', 'Mr.', 'Bulgaria', 'Sofia', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c');

INSERT IGNORE INTO users (is_admin, first_name, sir_name, email, title, country, city, password) 
VALUES 
(false, 'Ivan', 'Dimotrov', 'idimitrov@automation.com', 'Mr.', 'Bulgaria', 'Sofia', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Yoana', 'Ivanova', 'yivanova@automation.com', 'Mrs.', 'Bulgaria', 'Sopot', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Zdravka', 'Petrova', 'zpetrova@automation.com', 'Mrs.', 'Bulgaria', 'Elin Pelin', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Todor', 'Ivanov', 'tivanov@automation.com', 'Mr.', 'Bulgaria', 'Pravets', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Zahari', 'Avramov', 'zavramov@automation.com', 'Mr.', 'Bulgaria', 'Kardjali', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Maria', 'Georgieva', 'mgeorgieva@automation.com', 'Mrs.', 'Bulgaria', 'Plovdiv', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Stefan', 'Stoyanov', 'sstoyanov@automation.com', 'Mr.', 'Bulgaria', 'Varna', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Elena', 'Dimitrova', 'edimitrova@automation.com', 'Mrs.', 'Bulgaria', 'Burgas', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Nikolay', 'Petrov', 'npetrov@automation.com', 'Mr.', 'Bulgaria', 'Pleven', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Ivan', 'Dimotrov', 'idimitrov@automation.com', 'Mr.', 'Bulgaria', 'Sofia', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c'),
(false, 'Petar', 'Georgiev', 'pgeorgiev@automation.com', 'Mr.', 'USA', 'New York', '1fda9b7dc74fd8e32cfdbb63bde9f29bfbfd796620d650f3d587a788c3899a4b'),
(false, 'Maria', 'Ivanova', 'mivanova@automation.com', 'Mrs.', 'Germany', 'Berlin', 'c214b5bc5cf2e7e42f3c375eb11d484acff6aa0aae6dcb03251d3ab5de0636b8'),
(false, 'Georgi', 'Petrov', 'gpetrov@automation.com', 'Mr.', 'France', 'Paris', '295bb16de493b0dc7a6d48d5f212f1b3d1c02d44b650ee0cf3ab74e2994f5e6e'),
(false, 'Anna', 'Pavlova', 'apavlova@automation.com', 'Mrs.', 'Italy', 'Rome', '2c81f47b6ff7bfe9b2a803ad84e30172759b5185f28b4b02511cfcd9e2d6756b'),
(false, 'Stefan', 'Nikolov', 'snikolov@automation.com', 'Mr.', 'Spain', 'Madrid', '760aebf1d6c6a8b8a97e80187beaee8a42e49f4f3e2c4ad6f1cdac07c20f9f5f'),
(false, 'Elena', 'Dimitrova', 'edimitrova@automation.com', 'Mrs.', 'Portugal', 'Lisbon', 'c2e29519d85b8f2a7ad6d87a02a5c3f5f135df93d2b14e298a79161a9fb1571c'),
(false, 'Nikola', 'Stoyanov', 'nstoyanov@automation.com', 'Mr.', 'Canada', 'Toronto', '0ac5d7723c2d3f25f42875705f2c3447c82d2920b0b6da74eb541e6da44b98e0'),
(false, 'Kristina', 'Vasileva', 'kvasileva@automation.com', 'Mrs.', 'Australia', 'Sydney', 'c6324a4cc2316b5e7e4e68a43154f8c3d0f72e2ac2e7cde7da2fe3c26fe325cb'),
(false, 'Martin', 'Petrov', 'mpetrov@automation.com', 'Mr.', 'Japan', 'Tokyo', '1d3bc8712b8405d0a3ffdc70d89ec3ccfcd19baf53ef2fc26f17a2b30dd216c5'),
(false, 'Iva', 'Todorova', 'itodorova@automation.com', 'Mrs.', 'Brazil', 'Rio de Janeiro', 'b20d049f17a6ee92271d7586c97b14442242820e25dc58446e9cf6c89281c4e5'),
(false, 'Dimitar', 'Nikolov', 'dnikolov@automation.com', 'Mr.', 'Argentina', 'Buenos Aires', 'e3d0c764f6f11c070ee13b82b32d3c8f37647e564b1485a92e5c5f352314e7cb'),
(false, 'Tanya', 'Dimitrova', 'tdimitrova@automation.com', 'Mrs.', 'Russia', 'Moscow', 'e2b7d5a584f30dfae5730919f9b1cf41b35dfad15fd6e890c0948da0d46de14b'),
(false, 'Vladimir', 'Georgiev', 'vgeorgiev@automation.com', 'Mr.', 'India', 'Mumbai', '37413cc9b1145451918b92d80f672f916f17d314b8a92a29e1eae9dc1503bc44'),
(false, 'Eva', 'Pavlova', 'epavlova@automation.com', 'Mrs.', 'China', 'Beijing', '4c3f64c170c09dcfa51224d5b8ff65474e3c20d35a8e5f54399ebeaf91a49bfb');

-- Skills Table
CREATE TABLE IF NOT EXISTS skills (
  id int(11) NOT NULL AUTO_INCREMENT,
  skill_name varchar(24) NOT NULL,
  skill_category varchar(24) NOT NULL,
  skill_description varchar(100) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


INSERT IGNORE INTO skills (id, skill_name, skill_category, skill_description)
VALUES
(1, 'Java', 'Programming', 'Writing programming code using Java language'),
(2, 'Rust', 'Programming', 'Writing programming code using Rust language'),
(3, 'Welding', 'Programming', 'Performing welding tasks with precision and safety measures'),
(4, 'Carpentry', 'Woodworking', 'Crafting wooden structures with attention to detail'),
(5, 'Quality Assurance', 'Quality Assurance', 'Conducting quality checks and ensuring standards compliance'),
(6, 'Machine Learning', 'AI', 'Developing machine learning models for data analysis'),
(7, 'Surveying', 'Geodesy', 'Conducting land surveys and producing accurate measurements'),
(8, 'Biology', 'Biology', 'Analyzing biological samples and conducting experiments'),
(9, 'Construction Management', 'Construction', 'Overseeing construction projects and ensuring smooth operations'),
(10, 'Welding', 'Welding', 'Expertise in various welding techniques and equipment operation'),
(11, 'Electrical Wiring', 'Construction', 'Installing and maintaining electrical systems in buildings'),
(12, 'Plumbing', 'Construction', 'Installation and repair of plumbing fixtures and systems'),
(13, 'Data Analysis', 'AI', 'Analyzing datasets and extracting valuable insights'),
(14, 'Concrete Work', 'Construction', 'Pouring and shaping concrete for various structures'),
(15, 'Project Management', 'Construction', 'Planning and executing construction projects within budget and schedule'),
(16, 'Plant Biology', 'Biology', 'Studying plant species and their ecological impact'),
(17, 'Computer Vision', 'AI', 'Developing algorithms for image recognition and analysis'),
(18, 'Landscaping', 'Biology', 'Designing and maintaining outdoor landscapes'),
(19, 'Structural Engineering', 'Construction', 'Designing and analyzing structures for strength and stability'),
(20, 'Environmental Science', 'Biology', 'Researching environmental issues and proposing solutions'),
(21, 'Software Testing', 'Quality Assurance', 'Executing functional and non-functional tests on software applications, documenting test cases and scenarios, analyzing test results, and reporting defects.'),
(22, 'Automated Testing', 'Quality Assurance', 'Developing and maintaining automated test scripts using tools such as Selenium or Appium, executing routine tests for functionality and robustness, and managing test suites.'),
(23, 'Documentation Management', 'Quality Assurance', 'Creating and updating test plans, test cases, test reports, and other quality assurance documentation to ensure clarity and accuracy.'),
(24, 'Regression Testing', 'Quality Assurance', 'Conducting regression tests to ensure that recent code changes have not adversely affected existing functionality or introduced new bugs.'),
(25, 'User Acceptance Testing', 'Quality Assurance', 'Coordinating and facilitating UAT sessions with stakeholders to validate that the software meets their requirements and expectations.'),
(26, 'Defect Management', 'Quality Assurance', 'Identifying, prioritizing, and tracking defects using issue tracking systems, and working closely with development teams to ensure timely resolution.'),
(27, 'CI/CD Testing', 'Quality Assurance', 'Integrating testing into CI/CD pipelines to ensure that changes are thoroughly tested and validated before deployment.'),
(28, 'Performance Testing', 'Quality Assurance', 'Designing and executing performance tests to assess the responsiveness, scalability, and stability of software applications under various load conditions.'),
(29, 'Security Testing', 'Quality Assurance', 'Conducting security assessments and vulnerability scans to identify and address potential security risks and weaknesses in software systems.');


CREATE TABLE IF NOT EXISTS user_skills (
  user_id int(11) NOT NULL,
  skill_id int(11) NOT NULL,
  competence_level INT CHECK (competence_level >= 1 AND competence_level <= 5),
  PRIMARY KEY (user_id, skill_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT IGNORE INTO user_skills (user_id, skill_id, competence_level)
VALUES
(2, 1, 3), (2, 4, 2), (2, 8, 4), (2, 12, 5), (2, 17, 4),
(3, 2, 3), (3, 6, 2), (3, 10, 5), (3, 15, 5), (3, 20, 5),
(4, 3, 3), (4, 7, 2), (4, 11, 5), (4, 16, 4), (4, 2, 4),
(5, 1, 3), (5, 5, 2), (5, 9, 4), (5, 13, 5), (5, 18, 2),
(6, 2, 3), (6, 6, 2), (6, 10, 5), (6, 14, 1), (6, 19, 3),
(7, 3, 3), (7, 7, 2), (7, 11, 1), (7, 15, 5), (7, 20, 4),
(8, 1, 3), (8, 4, 2), (8, 8, 1), (8, 12, 4), (8, 17, 3),
(9, 2, 3), (9, 5, 2), (9, 9, 1), (9, 13, 3), (9, 18, 5),
(10, 2, 3), (19, 6, 2), (20, 10, 5), (21, 14, 1), (21, 19, 3),
(11, 3, 3), (18, 7, 2), (11, 11, 1), (22, 15, 5), (22, 20, 4),
(13, 1, 3), (16, 4, 2), (14, 8, 1), (10, 12, 4), (18, 17, 3),
(14, 2, 3), (15, 5, 2), (13, 9, 1), (21, 13, 3), (19, 18, 5);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
