let CorpStructPath = "project/php/get_departments.php";
let DeptPlace = document.getElementById("DepartmentDetails");
let RightPanel = document.getElementById("EmployeeDetails");

let EmpList = new Map();
let EmpData = [];

async function GenerateDepartmentOBJ() {
    let FullData = await JSONTransmitter(CorpStructPath);
    DeptData = FullData.departments;
    //EmpData = FullData.company.employees;

    DeptData.forEach(dept => {
        let deptDiv = document.createElement("div");
        deptDiv.className = "DeptContainer";
        deptDiv.textContent = dept.name;


        let empContainer = document.createElement("div");
        empContainer.className = "EmpContainer";
        empContainer.style.display = "none";

        deptDiv.onclick = () => {
            empContainer.style.display =
                empContainer.style.display === "none" ? "block" : "none";
        };
        EmpData = dept.employees;

        //EMPLOYEES
        EmpData.forEach(emp => {
            if (emp.department !== dept.name) return;

            let EmpDiv = document.createElement("div");
            EmpDiv.className = "EmpName";
            EmpDiv.textContent = emp.name;

            EmpDiv.onclick = (event) => {
                event.stopPropagation();
                hideAllEmpData();
                EmpList.get(emp.name).style.display = "block";
            };

            empContainer.appendChild(EmpDiv);

            //right side panel
            let EmpPanel = document.createElement("div");
            EmpPanel.className = "EmpDetailsPanel";
            EmpPanel.style.display = "none";

            EmpPanel.innerHTML = `
                <h3>${emp.name}</h3>
                <p><strong>Employee Password</strong>${emp.password}</p>
                <p><strong>Email:</strong> ${emp.email}</p>    
                <p><strong>Position:</strong> ${emp.position}</p>
                <p><strong>Department:</strong> ${emp.department}</p>
                <p><strong>Manager:</strong> ${emp.manager ?? "—"}</p>
            `;

            RightPanel.appendChild(EmpPanel);
            EmpList.set(emp.name, EmpPanel);
        });

        DeptPlace.appendChild(deptDiv);
        DeptPlace.appendChild(empContainer);
    });
}

async function JSONTransmitter(filename) {
    let data = await (await fetch(filename)).json();
    return data;
}

function hideAllEmpData() {
    EmpList.forEach(panel => {
        panel.style.display = "none";
    });
}

// OPEN MODAL WHEN BUTTON PRESSED
function CheckCorparativeStructureToMakeSure() {
    document.getElementById("DetailsModal").style.display = "block";
    PauseTimer();
}

function CloseStructure(){
    ContinueTimer();
    document.querySelector('.modal').style.display='none';
}

GenerateDepartmentOBJ();
