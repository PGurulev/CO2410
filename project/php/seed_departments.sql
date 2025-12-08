-- Seed data for departments and employees
-- Insert departments first
INSERT INTO `departments` (`name`) VALUES
('Executive Office'),
('Engineering & R&D'),
('IT & Security'),
('Finance'),
('Human Resources'),
('Operations (Marina Campus)'),
('Student Affairs'),
('Admissions & Outreach')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Insert employees using INSERT ... SELECT to get department IDs dynamically
-- Note: manager_email references employee emails, so some employees may need to be inserted after their managers
INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Dr. Eleni Hadjis', 'AOU!Pres2025', 'ehadjis@aou.example.com', 'President & Vice-Chancellor', d.id, NULL
FROM departments d WHERE d.name = 'Executive Office'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Mateo Grivas', 'Exec#Strat_8842', 'mgrivas@aou.example.com', 'Chief of Staff', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Executive Office'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Prof. Nia Kouris', 'R&D_Lead^2025', 'nkouris@aou.example.com', 'Director of Engineering & R&D', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Engineering & R&D'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Armin Petrescu', 'AuvLab$412', 'apetrescu@aou.example.com', 'Senior Robotics Engineer', d.id, 'nkouris@aou.example.com'
FROM departments d WHERE d.name = 'Engineering & R&D'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Lia Demetriou', 'OceanData_73!', 'ldemetriou@aou.example.com', 'Data Scientist (Ocean Sensing)', d.id, 'nkouris@aou.example.com'
FROM departments d WHERE d.name = 'Engineering & R&D'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Hassan Al-Masri', 'CISO@AOU_2025', 'halmasri@aou.example.com', 'Head of IT & Security (CISO)', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'IT & Security'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Sofia Marin', 'IdP-Gateway*902', 'smarin@aou.example.com', 'Systems Administrator', d.id, 'halmasri@aou.example.com'
FROM departments d WHERE d.name = 'IT & Security'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Jonas Mikkelsen', 'NetOps^Switch31', 'jmikkelsen@aou.example.com', 'Network Engineer', d.id, 'halmasri@aou.example.com'
FROM departments d WHERE d.name = 'IT & Security'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Maria Kyriakou', 'FinCtrl_77$', 'mkyriakou@aou.example.com', 'Finance Controller', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Finance'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Owen Dlamini', 'P2P_Ledger009!', 'odlamini@aou.example.com', 'Accounts Payable Specialist', d.id, 'mkyriakou@aou.example.com'
FROM departments d WHERE d.name = 'Finance'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Agata Nowak', 'HR_Policy*2025', 'anowak@aou.example.com', 'HR Manager', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Human Resources'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Theo Christoforou', 'Talent!Board_55', 'tchristoforou@aou.example.com', 'Talent Acquisition Specialist', d.id, 'anowak@aou.example.com'
FROM departments d WHERE d.name = 'Human Resources'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Irina Kovacs', 'Ops_Marina^441', 'ikovacs@aou.example.com', 'Operations Lead (Marina Campus)', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Operations (Marina Campus)'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Babatunde Ajayi', 'GateCtrl_NFC!28', 'bajayi@aou.example.com', 'Access Control Technician', d.id, 'ikovacs@aou.example.com'
FROM departments d WHERE d.name = 'Operations (Marina Campus)'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Elif Yildiz', 'StudentCare_960', 'eyildiz@aou.example.com', 'Director of Student Affairs', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Student Affairs'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'George Papas', 'Advisor*2025', 'gpapas@aou.example.com', 'Student Advisor', d.id, 'eyildiz@aou.example.com'
FROM departments d WHERE d.name = 'Student Affairs'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Rita Okoye', 'Outreach+Enroll77', 'rokoye@aou.example.com', 'Head of Admissions & Outreach', d.id, 'ehadjis@aou.example.com'
FROM departments d WHERE d.name = 'Admissions & Outreach'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `employees` (`name`, `password`, `email`, `position`, `department_id`, `manager_email`)
SELECT 'Samuel Petrou', 'Fairs&Schools_33', 'spetrou@aou.example.com', 'Admissions Officer', d.id, 'rokoye@aou.example.com'
FROM departments d WHERE d.name = 'Admissions & Outreach'
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

