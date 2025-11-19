The mail_structure.json file is a lightweight JSON representation of an email message. It organizes all the essential components—such as sender, recipients, subject, body content, and optional attachments—into a structured format that can be easily processed by applications. This structure ensures consistency when sending, storing, or parsing emails programmatically.

attachments (optional)
Type: array[object]
A list of file attachments.
Structure:
"to": ["bob@example.com", "carol@example.com"]

Example:
"cc": ["dave@example.com"]

JSON"attachments": [  {    "filename": "report.pdf",    "contentType": "application/pdf",    "data": "JVBERi0xLjQKJ..."  }]


headers (optional)
Type: object
Custom email headers as key-value pairs.
Example:
JSON"headers": {  "X-Priority": "High"}Show more lines

The api.js script fetches email data from php/mails.php and expects the response to be in JSON format. This JSON contains an array of mail objects, each with a structure similar to mail_structure.json.
