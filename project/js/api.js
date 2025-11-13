'use strict';

// Получение списка писем и вывод в консоль/DOM
fetch('php/mails.php')
	.then(r => {
		if (!r.ok) throw new Error('Network response was not ok');
		return r.json();
	})
	.then(({ mails }) => {
		console.log('Mails:', mails);
		const container = document.getElementById('mail-list');
		if (!container) return;
		container.innerHTML = '';
		mails.forEach(m => {
			const div = document.createElement('div');
			div.className = 'mail-item';
			div.innerHTML = `
				<h3>${m.content.subject}</h3>
				<p><strong>ID:</strong> ${m.id}</p>
				<p><strong>Sender:</strong> ${m.content.sender.name} &lt;${m.content.sender.email}&gt;</p>
				<p><strong>Receivers:</strong> ${m.content.recievers.map(r => `${r.name} &lt;${r.email}&gt;`).join(', ')}</p> 
				<pre class="mail-body">${m.body}</pre> // m.body stands for the body of the email
			`;
			container.appendChild(div);
		});
	})
	.catch(err => {
		console.error('Failed to load mails:', err);
	});


