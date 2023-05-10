function formXML() {
    if (document.getElementById("xml-pozice").value === "Student") {
        document.getElementById("st").innerHTML = "ST kód";
        document.getElementById("email").innerHTML = "Email";
    } else {
        document.getElementById("st").innerHTML = "Tel. číslo";
        document.getElementById("email").innerHTML = "Funkce";
    }
}

function modalLogin(show) {
    if (show) {
        modalRegister(false);
        document.getElementById("modalLogin").style.display = "flex";
    } else {
        document.getElementById("modalLogin").style.display = "none";
    }
}

function modalRegister(show) {
    if (show) {
        modalLogin(false);
        document.getElementById("modalRegister").style.display = "flex";
    } else {
        modalLogin(false);
        document.getElementById("modalRegister").style.display = "none";
    }
}