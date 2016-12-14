window.onload = initAll;
var xhr = false;

function initAll(){
    document.getElementById("company_name").selectedIndex = 0;    
    document.getElementById("company_name").onchange = initXML;
    document.getElementById("dept_name").onchange = deptselected;
}

function initXML(){
    var newCompany = document.getElementById("company_name");
    var companyId = newCompany.options[newCompany.selectedIndex].value;
    document.getElementById("company").value = companyId;
    
    if(window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    }
    else{
        if(window.ActiveXObject){
            try{
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e){}
        }
    }
    
    if(xhr){
        var company_id = encodeURIComponent(companyId);
        xhr.open("GET", "popmenu.php?company_id="+company_id ,true);
        xhr.onreadystatechange = popdept;
        xhr.send(null);
    }
    else
        alert("Error creating the XMLHttpRequest object!");
}

function popdept()
{
    if(xhr.readyState === 4)
    {
        if(xhr.status === 200)
        {
           var xmlResponse = xhr.responseXML;
           if(xmlResponse && xmlResponse.childNodes.length >0)
               initdeptoptions(xmlResponse.documentElement);
           else
               document.getElementById("dept_error").innerHTML = "no proper department";
        }
        else
        {
           alert("There was a problem accessing the server:" + xhr.statusText);     
        }
    }
    //else
        //setTimeout('popdept()',1000);
}

function initdeptoptions(xmlDept)
{
    var node = xmlDept;
    while(null!==node)
         {
             var child = node.firstChild;
             var newoption = document.createElement("option");
             newoption.value = child.textContent;
             child = child.nextSibling;
             newoption.innerHTML = child.textContent;
             
             node = node.nextSibling;
             document.getElementById("dept_name").appendChild(newoption);
         }
}

function deptselected()
{
    document.getElementById("department").value = this.options[this.selectedIndex].value;
}

