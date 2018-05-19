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

    byId("forgotPassword-button").addEventListener('click', doForgotPassword, false);
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
 * Function doForgotPassword
 ******************************************************************************/
function doForgotPassword() {
	
	if (byId('email').value != ""){
        
        var data = JSON.stringify({"email": byId('email').value});
        xhr.addEventListener('readystatechange', processForgotPassword, false);
        xhr.open('POST', 'user/forgotpassword.php', true);
        xhr.setRequestHeader("Content-type", "application/json");
        xhr.send(data);
        
    } else {
		byId("result").innerHTML = "The email you entered was invalid. Please try again.";
	}
}

/*******************************************************************************
 * Function processForgotPassword
 ******************************************************************************/
function processForgotPassword() {

    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        
		xhr.removeEventListener('readystatechange', processForgotPassword, false);
        var myResponse = JSON.parse(this.response);
        byId("result").innerHTML = myResponse.message;	  
		
    } 
}

// Connect the main function to window load event
window.addEventListener("load", main, false); 