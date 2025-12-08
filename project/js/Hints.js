/*** Hints.js***/


const HintsArray = [
    {
        message: "Always verify the sender’s full email address and subject line, watching carefully for odd or suspicious details.",
        image: "project/assets/images/hint01.png"
    },
    {
        message: "Be aware that phishers often disguise dangerous links with harmless-looking text meant to mislead or confuse you.",
        image: "project/assets/images/hint02.png"
    },
    {
        message: "Stay alert for urgent or threatening wording in emails, especially when you are pressured to respond quickly.",
        image: "project/assets/images/hint03.png"
    },
    {
        message: "Check for unexpected attachments or demands for private data, since trustworthy companies rarely request it",
        image: "project/assets/images/hint04.png"
    }
];

let currentHintIndex = 0;

function showHint(index) {
    const modal = document.getElementById('HintModal');
    const modalContentArea = document.getElementById('HintContent');

    const hint = HintsArray[index];

    if (modalContentArea) {
        modalContentArea.innerHTML = `
            <h2>💡 Phishing Hint</h2>
            <p>${hint.message}</p>
            ${hint.image ? `<img src="${hint.image}" alt="Hint Image" class="hint-image">` : ""}
            <div style="text-align:center; margin-top:10px;">
                <button class="btn btn-next-hint">Next Hint ➡️</button>
            </div>
        `;


        const nextBtn = modalContentArea.querySelector('.btn-next-hint');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentHintIndex = (currentHintIndex + 1) % HintsArray.length;
                showHint(currentHintIndex);
            });
        }
    }

    if (modal) {
        modal.style.display = 'block';
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Handles the hint button click
 */
function handleHintClick() {
    currentHintIndex = Math.floor(Math.random() * HintsArray.length); // pick a random start
    showHint(currentHintIndex);
}

/**
 * Closes the hint modal
 */
function closeHint() {
    const modal = document.getElementById('HintModal');
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = ''; // restore scrolling
}

/**
 * Initializes the hint button
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
