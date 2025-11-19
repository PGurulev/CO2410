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


Do you want me to also include a diagram (like a JSON schema tree) or generate a Mermaid diagram to visualize the structure?
