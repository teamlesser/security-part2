/**
 * File: deleteMessage.js
 * Desc: File for user confirmation of message deletion.
 * Date: 2018-05-20
 */

/**
 * Variable for XMLHttpRequest-object
 */
var xhr;

/**
 * Gets a HTML-element by id-name.
 * @param id The id of the page element to return.
 * @returns {HTMLElement | null}
 */
function byId(id) {
    return document.getElementById(id);
}

/**
 * The main function. Creates XMLHttpRequest-object.
 * Adds listeners to necessary buttons.
 */
function main(){

    //Adds EventListeners to buttons.
    byId("button-yes").addEventListener('click', doDelete, false);
    byId("button-no").addEventListener('click', returnToMain, false);

    // Creates matching XMLHttpRequest-object
    try {
        if (window.XMLHttpRequest) {
            // Supported by newer browsers
            xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            // Supported by IE6, IE5
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            throw new Error('Cannot create XMLHttpRequest object');
        }

    } catch (e) {
        alert('"XMLHttpRequest failed!' + e.message);
    }
}

function returnToMain(){
    window.location.replace("/securitylab/user/main.php");
}

/**
 * Sends a message to server that user really wants to delete the shown post.
 */
function doDelete() {
    // EventListener for server state change
    xhr.addEventListener('readystatechange', processDelete, false);

    // Message is composed and sent as JSON
    var data = JSON.stringify({"deleteValue":byId("button-yes").value});

    xhr.open("POST", "../user/processDeleteMessage.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(data);
}

/**
 * Processes the received response for deletion.
 */
function processDelete(){
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){

        // Removes the EventListener since the xhr-object is reused
        xhr.removeEventListener("readystatechange", processDelete, false);

        // Handles response message
        var response = JSON.parse(this.responseText);

        // Displays the message on page
        byId("return-message").innerHTML = response.message;

        if (response.status === "success"){
            returnToMain();
        }
    }
}

//Main is run once the page has finished loading.
window.addEventListener("load", main, false);