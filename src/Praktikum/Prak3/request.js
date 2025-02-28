var request = new XMLHttpRequest();

function requestData(){
    request.open("GET", "kundenStatus.php");

    request.onreadystatechange = processData;

    request.send(null);
}

function processData(){
    if (request.readyState == 4){
        if (request.status == 200){
            if (request.responseText != null){
                process(request.responseText);
            } else {console.error("Dokument ist leer");}
        } else { console.error("Übertragung fehlgeschlagen");}
    } else ;
}

function process($data){
    console.log('--------');
    console.log('Response String:');
    console.log($data);
    var obj = JSON.parse($data);

    console.log('JSON.parse Objekte: ');
    console.log(obj);

    var output = document.getElementById('output');

    while(output.firstChild) {
        output.removeChild(output.lastChild);
    }

    var list = document.createElement('ol');
    output.appendChild(list);

    for (item of obj){
        var listItem = document.createElement('li');
        listItem.innerText = item.ordering_id;
    }
    console.log('HTML Liste:');
    console.log(list);
}

window.setInterval(requestData, 3000)