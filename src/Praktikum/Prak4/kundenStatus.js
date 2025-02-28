var request = new XMLHttpRequest();

function requestData(){
    console.log("requestData called");
    request.open("GET", "kundenStatus.php");
    request.onreadystatechange = processData;
    request.send(null);
}

function processData(){
    if(request.readyState == 4){
        if (request.status == 200){
            if(request.responseText != null){
                console.log("Data fetched:", request.responseText); //debugging
                process(request.responseText);
            }else{
                console.error ("Document is empty");
            }
        }else{
            console.error("Ubertragung fehlgeschlagen");
        }
    }else{
        //ubertragung lauft noch
    }
}

function process($data){
    try{
        const obj = JSON.parse($data);

        console.log('JSON parsed Objekte: ');
        console.log(obj);

        const ordersArray = Object.values(obj);

        ordersArray.forEach(order =>{
            console.log("Processing order:", order);

            //iterate each artidcle:
            order.ordered_articles.forEach(article =>{
                console.log("Processing article:", article);

                const statusMap = {
                    0: 'bestellt',
                    1: 'im Ofen',
                    2: 'fertig',
                    3: 'unterwegs',
                    4: 'geliefert'
                };
                
                const statusValue = statusMap[article.status];
                if(statusValue){
                    const radioName = `status_${article['ordered_article_id']}`;
                    const radioButtons = document.getElementsByName(radioName);

                    radioButtons.forEach(radio =>{
                        radio.checked = radio.value === statusValue;
                    });

                    const statusTextWritten = document.getElementById(`status_${article['ordered_article_id']}`);
                    if(statusTextWritten){
                        statusTextWritten.textContent = `Status: ${statusValue}`;
                    }
                }
            });
        });
    }catch(e){
        console.error("Error processing data", e);
    } 
}

window.setInterval(requestData, 2000);
