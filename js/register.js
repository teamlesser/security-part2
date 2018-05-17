/**
 * File: register.js
 * Desc: File for handling registering information through Ajax. Sends and receives data from register.php.
 * Date: 2018-05-16
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
 * The main function. Adds listener to login button and
 * creates XMLHttpRequest-object.
 */
function main(){

    //Adds EventListeners to buttons.
    byId("register-button").addEventListener('click', doRegister, false);

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
 * Sends a POST to processRegister.php that will attempt to create and register a user in the database.
 * Gets values from username, password, password-again and e-mail-fields and passes it on to server.
 */
function doRegister() {

    // Checks that fields aren't empty
    if(byId("username-field").value !== "" &&
        byId("password-field").value !== "" &&
        byId("password-again-field").value !== "" &&
        byId("email-field").value !== ""){

        // Gets values from registration fields
        var username = byId("username-field").value;
        var password = byId("password-field").value;
        var passwordAgain = byId("password-again-field").value;
        var email = byId("email-field").value;

        //Check so that password and password confirmation doesn't differ
        if(password === passwordAgain) {

            // Check that e-mail has correct format
            var emailRegex = /[\w]+@[\w]+\.[a-zA-Z]+/;

            if (email.match(emailRegex)) {

                // EventListener for server state change
                xhr.addEventListener('readystatechange', processRegister, false);

                username = encodeURIComponent(username);
                password = encodeURIComponent(password);
                passwordAgain = encodeURIComponent(passwordAgain);
                email = encodeURIComponent(email);

                // Message is composed and sent as JSON
                var data = JSON.stringify({
                    "username": username,
                    "password": password,
                    "passwordAgain": passwordAgain,
                    "email": email
                });

                xhr.open("POST", "processRegister.php", true);
                xhr.setRequestHeader("Content-Type", "application/json");
                xhr.send(data);

            }
            else {
                byId("return-message").innerHTML = "E-mail has incorrect format.";
            }
        }
        else{
            byId("return-message").innerHTML = "'Confirmation password' does not match 'password'";
        }
    }

    else {
        byId("return-message").innerHTML = "At least one of the fields are empty.";
    }
}

/**
 * Processes the received response from the
 * server when attempting to register.
 */
function processRegister(){

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200){
        // Removes the EventListener since the xhr-object is reused
        xhr.removeEventListener("readystatechange", processRegister, false);

        // Handles response message
        var response = JSON.parse(this.responseText);
        byId("return-message").innerHTML = response.message;
    }

}

//Main is run once the page has finished loading.
window.addEventListener("load", main, false);