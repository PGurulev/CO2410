INSERT INTO `mails` (`subject`, `body`, `sender_name`, `sender_email`, `isFake`) VALUES
('content', 'content', 'NameForExample', 'example@gmail.com', 'true');

-- ecipients for the first email (id = 1), taken from the email_schema_example.txt file
INSERT INTO `mail_recipients` (`mail_id`, `name`, `email`, `isFake`) VALUES
(1, 'NameForExample', 'example@gmail.com', 'true');


