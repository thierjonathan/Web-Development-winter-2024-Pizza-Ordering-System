"use strict";

function addPizza(pizza) {
    //Adding Pizza to the Warenkorb UI
    var option = document.createElement('option');
    option.value = pizza.dataset.name;
    option.text = pizza.dataset.name;

    option.dataset.price = pizza.dataset.price;
    option.dataset.id = pizza.dataset.article_id;

    console.log("Pizza Dataset:", pizza.dataset);
    console.log("Price in addPizza: ", pizza.dataset.price);
    console.log("Article ID in addPizza: ", pizza.dataset.article_id);

    var warenkorb = document.getElementById('warenkorb');
    warenkorb.appendChild(option);

    //Adjust the Price Ausgabe
    var priceTag = document.getElementById('preisAusgabe');
    var price = parseFloat(priceTag.textContent) + parseFloat(pizza.dataset.price);
    priceTag.innerText = price.toFixed(2) + '€';

    checkCart();
}

function submitOrder(event){
    event.preventDefault(); 

    const ordered_articles = [];
    var address = document.getElementById('address');
    console.log("Address in submitOrder:", address.value);

    var warenkorb = document.getElementById('warenkorb');
    var options = Array.from(warenkorb.options);


    for (let i = 0; i < options.length;i++){
        ordered_articles.push(options[i].dataset.id);
        console.log("Submitted Article ID in submitOrder: ", options[i].dataset.id);
    }
    
    sendOrder(ordered_articles, address);

    setTimeout(() => {
        event.target.submit();
    }, 500);
}

function clearWarenkorb(){
    var warenkorb = document.getElementById('warenkorb');
    var options = warenkorb.options;

    while (options.length > 0){
        options[0].remove();
    }

    var priceTag = document.getElementById('preisAusgabe');
    priceTag.innerText = '0 €'

    checkCart();
}

function clearSelectedItems(){
    var warenkorb = document.getElementById('warenkorb');
    var selectedOptions = Array.from(warenkorb.selectedOptions);

    selectedOptions.forEach(function(option) {
        option.remove();
    });

    updatePrice();

    checkCart();
}

function updatePrice() {
    var warenkorb = document.getElementById('warenkorb');
    var options = Array.from(warenkorb.options);
    var priceTag = document.getElementById('preisAusgabe');
    var currentPrice = 0;

    options.forEach(function(option) {
        var pizzaPrice = parseFloat(option.getAttribute('data-price'));
        currentPrice += pizzaPrice;
    });

    priceTag.innerText = currentPrice.toFixed(2) + '€';
}

function checkCart() {
    var warenkorb = document.getElementById('warenkorb');
    var submitButton = document.getElementById('submitOrder');
    var options = Array.from(warenkorb.options);
    var addressText = document.getElementById('address');
    
    if (options.length == 0 || addressText.value == '') {
        submitButton.disabled = true;
    } else {
        submitButton.disabled = false; 
    }
}

window.onload = function() {
    checkCart();
};

document.getElementById('warenkorb').addEventListener('change', checkCart);
document.getElementById('address').addEventListener('input', checkCart);
document.getElementById("orderForm").addEventListener("submit", submitOrder);
