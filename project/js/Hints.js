/*** Function executed when the hint button is clicked.*/

function handleHintClick() {


    const modal = document.getElementById('HintModal');
    const modalContentArea = document.getElementById('HintContent');


    const hintTitle = "💡Phishing Hint";
    const hintMessage = "<p>Always check the sender's full email address and the subject line for suspicious characters!</p>";



    if (modalContentArea) {
        modalContentArea.innerHTML = `
            <h2>${hintTitle}</h2>
            ${hintMessage}
        `;
    }

    // 4. Show the modal
    if (modal) {
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
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
function closeHint() {
  const modal = document.getElementById('HintModal');
  if (!modal) return;
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = ''; // restore scrolling
}


document.addEventListener('DOMContentLoaded', initHint);