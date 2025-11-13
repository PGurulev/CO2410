INSERT INTO `mails` (`subject`, `body`, `sender_name`, `sender_email`) VALUES
('content', 'content', 'NameForExample', 'example@gmail.com');

// recipients for the first email (id = 1), taken from the example.txt file
INSERT INTO `mail_recipients` (`mail_id`, `name`, `email`) VALUES
(1, 'NameForExample', 'example@gmail.com');


