function showHintModal() {
    const modal = document.getElementById("DetailsModal");
    modal.style.display = "block";

    // Hide corporative structure panels
    document.getElementById("DepartmentDetails").style.display = "none";
    document.getElementById("EmployeeDetails").style.display = "none";

    // Show hints panel (empty for now)
    const hintPanel = document.getElementById("HintPanel");
    hintPanel.style.display = "block";
    hintPanel.innerHTML = "<p style='text-align:center; color:#333;'>Hint content will appear here...</p>";
}

function closeModal() {
    const modal = document.getElementById("DetailsModal");
    modal.style.display = "none";

    // Reset panels
    document.getElementById("DepartmentDetails").style.display = "flex";
    document.getElementById("EmployeeDetails").style.display = "block";
    document.getElementById("HintPanel").style.display = "none";
}

// Attach hint button
document.querySelectorAll('.btn-hint').forEach(btn => {
    btn.addEventListener('click', showHintModal);
});
