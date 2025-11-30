/*** Function executed when the hint button is clicked.*/

function handleHintClick() {


    const modal = document.getElementById('HintModal');
    const modalContentArea = document.getElementById('HintContent');


    const hintTitle = "💡 Phishing Hint";
    const hintMessage = "<p>Always check the sender's full email address and the subject line for suspicious characters!</p>";



    if (modalContentArea) {
        modalContentArea.innerHTML = `
            <h3 style="margin-top:0; color : black;">${hintTitle}</h3>
            ${hintMessage}
        `;
    }

    // 4. Show the modal
    if (modal) {
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
    }
}

/**
 * Initializes the hint button by making it clickable.
 */
function initHint() {
    const button = document.querySelector('.btn-hint');

    if (button) {
        button.addEventListener('click', handleHintClick);
    } else {
        console.error("Error: Button with class 'btn-hint' not found.");
    }
}

document.addEventListener('DOMContentLoaded', initHint);