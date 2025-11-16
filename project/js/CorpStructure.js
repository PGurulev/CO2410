let CorpStructPath = "./AssetsAndExamples/JsonFiles/corporate_structure.json";
let DeptPlace = document.getElementById("DepartmentDetails");
let RightPanel = document.getElementById("EmployeeDetails");
//map info from https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Map
let EmpList = new Map();
async function GenerateDepartmentOBJ() {
    let FullData = await JSONTransmitter(CorpStructPath);
    console.log(FullData);
    DeptData = FullData.company.departments;
    console.log(DeptData);
    EmpData = FullData.company.employees;
    console.log(EmpData);
    DeptData.forEach(dept => {
        deptDiv = document.createElement("div");
        deptDiv.class = "DeptContainer";
        deptDiv.textContent = `${dept.name}`;
        let empContainer = document.createElement("div");
        empContainer.class = "EmpContainer";
        empContainer.style.display = "none";
        deptDiv.onclick = () => {
            empContainer.style.display = CheckForVisibility(empContainer);
        }
        for(let i = 0; i < EmpData.length; i++){
            if(EmpData[i].department != dept.name){
                continue;
            }
            else{
                let EmpDiv = document.createElement("div");
                EmpDiv.textContent = `${EmpData[i].name}`;
                EmpDiv.onclick = () => {
                    hideAllEmpData();
                    EmpList.get(EmpData[i].name).style.display = "flex";
                }
                empContainer.appendChild(EmpDiv);
            }

            

            EmpPanel = document.createElement("div");
            EmpPanel.style.display = "none";

            EmpPanel.innerHTML = `<p>${EmpData[i].name}</p>
            <p>${EmpData[i].password}</p>
            <p>${EmpData[i].email}</p>
            <p>${EmpData[i].position}</p>
            <p>${EmpData[i].department}</p>
            <p>${EmpData[i].manager}</p>`;

            RightPanel.appendChild(EmpPanel);
            EmpList.set(EmpData[i].name, EmpPanel);
        }
        DeptPlace.appendChild(deptDiv);
        DeptPlace.appendChild(empContainer);
    })
}
async function JSONTransmitter(filename){
    let FetchedData = await (await fetch(filename)).json();
    return FetchedData;
}

function CheckForVisibility(element){
    if(element.style == "none")
        return "flex";
    else
        return "none";
}
function hideAllEmpData()
{
    EmpData.forEach(emp => {
        emp.style.display = "none";
    });
}
GenerateDepartmentOBJ();
//TO DO implement the changes in visibility, so it works correctly and create basic css
function CheckCorparativeStructureToMakeSure()
{

}