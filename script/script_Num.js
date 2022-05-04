let valid = true;

function testNum(){
    let tel = document.getElementById("num_tel");

    if(valid){
        applayNewNum(tel);
    }
}

function applayNewNum(tel){
    let num_tel = tel.value;
    let length_num = num_tel.length;

    if(length_num%3 === 2 && length_num <14){
        num_tel += "-";
        tel.value = num_tel;
    }else if(length_num >= 15){
        tel.value = num_tel.slice(0, 14);
    }
}

function main(){
    let tel = document.getElementById("num_tel");

    tel.addEventListener("keydown", (e)=>{
        if(e.key === "Backspace"){
            console.log("appui sur la suppression");
            valid = false;
        }
    });
    tel.addEventListener("keyup", (e)=>{
        if(e.key === "Backspace"){
            console.log("relachement sur la suppression");
            valid = true;
            testNum();
        }
    });

}

main();