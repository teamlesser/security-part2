/**
 * File: main.js
 * Desc: File for handling page events on main.php.
 * Date: 2018-05-18
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

    //Adds EventListeners to post message button.
    byId("button-post-message").addEventListener('click', doPostMessage, false);
    byId("logout-button").addEventListener('click', doLogout, false);

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

/**
 * Sends a POST to processLogin.php that will attempt to login an user.
 * Gets values from email and password fields and passes it on to server.
 */
function doPostMessage() {

    // Checks that text field isn't empty
    if(byId("textarea-message").value !== ""){

        // Gets values of the message and keywords
        var message = byId("textarea-message").value;
        var keywords = byId("input-keywords").value;

        // Check that message doesn't contain other than legal characters
        var messageRegex = /^[\w \-+.,!()';:?]+$/;

        if (message.match(messageRegex)){

            // Check the length of the message and keywords, is it beyond 2500 characters?
            if (message.length > 2500){
                byId("return-message").innerHTML = "Message is too long. Limit is 2500 characters.";
            }
            else if (keywords.length > 256){
                byId("return-message").innerHTML = "Keywords are too long. Limit is 256 characters.";
            }

            else {
                // EventListener for server state change
                xhr.addEventListener('readystatechange', processPostMessage, false);

                // Message is composed and sent as JSON
                var data = JSON.stringify({"message": message, "keywords": keywords});

                xhr.open("POST", "../user/processPostMessage.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.send(data);
            }

        } else {
            byId("return-message").innerHTML = "Message contains illegal characters.";
        }
    }
    else {
        byId("return-message").innerHTML = "Empty message!";
    }
}


/**
 * Processes the received response from the
 * server when attempting to login.
 */
function processPostMessage(){

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){

        // Removes the EventListener since the xhr-object is reused
        xhr.removeEventListener("readystatechange", processPostMessage, false);

        // Handles response message
        var response = JSON.parse(this.responseText);

        // Displays the message on page
        byId("return-message").innerHTML = response.message;

        if (response.status === "success"){
            // on success the processPostMessage should
            // send back an array with posts.
            setTimeout(function(){
                window.location.reload(1);
            }, 0);

            // Empties post fields
            byId("textarea-message").value = "";
            byId("input-keywords").value = "";

        } else if (response.status === "authfail"){
            // User was not authenticated, move them to index
            window.location.replace("/securitylab/index.php");
        }
    }
}

/*******************************************************************************
 * doLogout()
 * sends a GET to logout.php
 ******************************************************************************/
function doLogout() {
    // EventListener for server state change
    xhr.addEventListener('readystatechange', processLogout, false);
    xhr.open('GET', '../user/logout.php', true);
    xhr.send(null);
}

/*******************************************************************************
 * processLogout()
 * recieces response from server after trying to log out
 ******************************************************************************/
function processLogout() {

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        // Removes the EventListener since the xhr-object is reused
        xhr.removeEventListener('readystatechange', processLogout, false);
        var response = JSON.parse(this.responseText);

        byId('logout-message').style.display = "block";
        byId('logout-message').innerHTML = response.message;

        //redirect if return message doesn't imply that something went wrong
        if(response.message.indexOf("wrong") === -1){
            window.location.replace("/securitylab/index.php");
        }
    }
}


//Main is run once the page has finished loading.
window.addEventListener("load", main, false);