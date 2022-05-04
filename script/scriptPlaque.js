function testCheckPlaque(){
    let caseCocher = document.getElementById("typePlaque").checked;
    let champPlaque = document.getElementById("plaqueImma");
    if(caseCocher){
        champPlaque.setAttribute("placeholder", "XX-123-XX");
        newPlaqueBool = true;
    }else{
        champPlaque.setAttribute("placeholder", "123 XXX 12");
        newPlaqueBool = false;
    }
}
function testPlaque(){
    let plaque = document.getElementById("plaqueImma");

    if(valid){
        applyPlaque(plaque);
    }
}

let valid = true;
let newPlaqueBool = true;
function applyPlaque(plaque){
    let plaque_num = plaque.value;
    let length_num = plaque_num.length;

    if(newPlaqueBool){
        if(length_num === 2 || length_num === 6){
            plaque_num += '-';
            plaque.value = plaque_num;
        }
        if(length_num >9){
            plaque.value = plaque_num.slice(0, 9);
        }
    }else{
        if(length_num === 3 || length_num === 7){
            plaque_num += ' ';
            plaque.value = plaque_num;
        }
        if(length_num >10){
            plaque.value = plaque_num.slice(0, 10);

        }
    }
}

function main(){
    let plaque = document.getElementById("plaqueImma");

    plaque.addEventListener("keydown", (e)=>{
        if(e.key === "Backspace"){
            console.log("appui sur la suppression");
            valid = false;
        }
    });
    plaque.addEventListener("keyup", (e)=>{
        if(e.key === "Backspace"){
            console.log("relachement sur la suppression");
            valid = true;
            testPlaque();
        }
    });

}

main();