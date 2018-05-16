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

	byId("resetPassword-button").addEventListener('click', doResetPassword, false);
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

    if (byId('email').value != "" && byId('newpass1').value != "" && byId('newpass2').value != '' && byId('resettoken').value != '') {
		
		// For later: encrypt the json data before sending it. Only possible if we can use additional libraries such as crypto.js
        var data = JSON.stringify(
		{ 
			"email": byId('email').value, 
			"newpass1": byId('newpass1').value, 
			"newpass2": byId('newpass2').value, 
			"token": byId('resettoken').value 
		});
        xhr.addEventListener('readystatechange', processResetPassword, false);
        xhr.open('POST', 'user/resetpassword.php', true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.send(data);
    }
}

/*******************************************************************************
 * Function processResetPassword
 ******************************************************************************/
function processResetPassword() {

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        
		xhr.removeEventListener('readystatechange', processResetPassword, false);
        var myResponse = JSON.parse(this.responseText);
        byId("result").innerHTML = myResponse.message;	  
		
    } 
}

// Connect the main function to window load event
window.addEventListener("load", main, false); 
