var xhr; 

/*******************************************************************************
 * Util functions
 ******************************************************************************/
function byId(id) {
    return document.getElementById(id);
}
/******************************************************************************/


/*******************************************************************************
 * Main function
 ******************************************************************************/
function main() {

	byId("reset-password-button").addEventListener('click', doResetPassword, false);

    try {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            // code for IE6, IE5
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            throw new Error('Cannot create XMLHttpRequest object');
        }

    } catch (e) {
        alert('"XMLHttpRequest failed!' + e.message);
    }

}

/*******************************************************************************
 * Function doResetPassword
 ******************************************************************************/
function doResetPassword() {
    if (byId('email-field').value !== "" && byId('newpass1-field').value !== "" &&
        byId('newpass2-field').value !=="" && byId('resettoken-field').value !== "") {

        // Checks that passwords are the same
        if (byId('newpass1-field').value === byId('newpass2-field').value){
            var data = JSON.stringify(
                {
                    "email": byId('email-field').value,
                    "newpass1": byId('newpass1-field').value,
                    "newpass2": byId('newpass2-field').value,
                    "resettoken": byId('resettoken-field').value
                });

            xhr.addEventListener('readystatechange', processResetPassword, false);

            xhr.open('POST', '../user/processResetPassword.php', true);
            xhr.setRequestHeader("Content-type", "application/json");
            xhr.send(data);
        }

        else{
            byId("fill-all-fields").innerHTML = "Passwords do not match";
        }
    }else{
        byId("fill-all-fields").innerHTML = "You must fill in all fields";
    }
}

/*******************************************************************************
 * Function processResetPassword
 ******************************************************************************/
function processResetPassword() {

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        
		xhr.removeEventListener('readystatechange', processResetPassword, false);
		var myResponse = JSON.parse(this.response);
        byId("return-message").innerHTML = myResponse.message;	  
		
    } 
}

// Connect the main function to window load event
window.addEventListener("load", main, false); 
