/**
 * Created by borjaperez on 10/5/17.
 */

/* ************* CONSTANTS ****************/
const LOGIN_BUTTON = 'loginButton';
const USERNAME_GROUP = 'usernameGroup';
const USERNAME_INPUT = 'usernameInput';
const PASSWORD_GROUP = 'passwordGroup';
const PASSWORD_INPUT = 'passwordInput';
const LOGIN_MODAL = 'loginModal';

const REGISTER_BUTTON = 'registerButton';
const REGISTER_IMAGE_BUTTON = 'selectImageButton';
const REGISTER_PROFILE_IMAGE = 'profileImage';
const REGISTER_USERNAME_GROUP = 'registerUsernameGroup';
const REGISTER_USERNAME_INPUT = 'registerUsernameInput';
const REGISTER_BIRTHDATE_GROUP = 'registerBirthdateGroup';
const REGISTER_BIRTHDATE_INPUT = 'registerBirthdateInput';
const REGISTER_PASSWORD_GROUP = 'registerPasswordGroup';
const REGISTER_PASSWORD_INPUT = 'registerPasswordInput';
const REGISTER_CONFIRM_PASSWORD_GROUP = 'registerConfirmPasswordGroup';
const REGISTER_CONFIRM_PASSWORD_INPUT = 'registerConfirmPasswordInput';
const REGISTER_EMAIL_GROUP = 'registerEmailGroup';
const REGISTER_EMAIL_INPUT = 'registerEmailInput';
const REGISTRATION_MODAL = 'registrationModal';

const RegistrationErrorCode = {
    ErrorCodeUsername : 0,
    ErrorCodeBirthdate : 1,
    ErrorCodePassword : 2,
    ErrorCodeConfirmPassword : 3,
    ErrorCodeEmail : 4,
    ErrorCodeUsernameUnavailable : 5,
    ErrorCodeEmailUnavailable : 6,
    ErrorCodeRegistrationSuccessful: 7
}

const LoginErrorCode = {
    ErrorCodeUsername : 0,
    ErrorCodePassword: 1,
    ErrorCodeNotFound : 2,
    ErrorCodeNotConfirmed : 3,
    ErrorCodeLoginSuccessful : 4,
}

const USERNAME_PATTERN = /^[a-z0-9]+$/i;
const PASSWORD_PATTERN = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,12}$/;
const EMAIL_PATTERN = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

/* ************* VARIABLES ****************/
var profileImageHref = 'assets/images/defaultProfile.png';

var today;

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.loginButton = document.getElementById(LOGIN_BUTTON);
        this.loginUsernameGroup = document.getElementById(USERNAME_GROUP);
        this.loginUsernameInput = document.getElementById(USERNAME_INPUT);
        this.loginPasswordGroup = document.getElementById(PASSWORD_GROUP);
        this.loginPasswordInput = document.getElementById(PASSWORD_INPUT);
        this.loginModal = document.getElementById(LOGIN_MODAL);
        this.registerButton = document.getElementById(REGISTER_BUTTON);
        this.registerImageButton = document.getElementById(REGISTER_IMAGE_BUTTON);
        this.registerProfileImage = document.getElementById(REGISTER_PROFILE_IMAGE);
        this.registerUsernameGroup = document.getElementById(REGISTER_USERNAME_GROUP);
        this.registerUsernameInput = document.getElementById(REGISTER_USERNAME_INPUT);
        this.registerBirthdateGroup = document.getElementById(REGISTER_BIRTHDATE_GROUP);
        this.registerBirthdateInput = document.getElementById(REGISTER_BIRTHDATE_INPUT);
        this.registerPasswordGroup = document.getElementById(REGISTER_PASSWORD_GROUP);
        this.registerPasswordInput = document.getElementById(REGISTER_PASSWORD_INPUT);
        this.registerConfirmPasswordGroup = document.getElementById(REGISTER_CONFIRM_PASSWORD_GROUP);
        this.registerConfirmPasswordInput = document.getElementById(REGISTER_CONFIRM_PASSWORD_INPUT);
        this.registerEmailGroup = document.getElementById(REGISTER_EMAIL_GROUP);
        this.registerEmailInput = document.getElementById(REGISTER_EMAIL_INPUT);
        this.registrationModal = document.getElementById(REGISTRATION_MODAL);
    }

    return {
        sharedInstance: function() {

            if (this.instance == null) this.instance = new WebManager();

            return this.instance;
        }
    };
})();

/**
 * Object variable for adding listener events
 * @type {{add: Listener.add, eventEditComment: Listener.eventEditComment, eventRemoveComment: Listener.eventRemoveComment}}
 */
var Listener = {

    add: function(object, event, callback, capture) {

        object.addEventListener(event, callback, capture);
    },

    eventLogin: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        // validate username
        if (WebManager.sharedInstance().loginUsernameInput.value.length == 0) errorCodes.push(LoginErrorCode.ErrorCodeUsername);

        // validate password
        if (WebManager.sharedInstance().loginPasswordInput.value.length == 0) errorCodes.push(LoginErrorCode.ErrorCodePassword);

        createLoginErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        createLoginErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        var params = {
            'username' : WebManager.sharedInstance().loginUsernameInput.value,
            'password' : WebManager.sharedInstance().loginPasswordInput.value
        };

        $.ajax({
            data:  params,
            url:   '../login',
            type:  'POST',

            success: function (response) {
                console.log(response);
                createLoginErrorsForCodes(response);
            }
        })
    },

    eventRegister: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        // validate username
        if (USERNAME_PATTERN.test(WebManager.sharedInstance().registerUsernameInput.value) == false ||
            WebManager.sharedInstance().registerUsernameInput.value.length > 20) {
            errorCodes.push(RegistrationErrorCode.ErrorCodeUsername);
        }

        // validate date
        if (moment(WebManager.sharedInstance().registerBirthdateInput.value, "YYYY-MM-DD", true).isValid() == false ||
            moment(WebManager.sharedInstance().registerBirthdateInput.value).isAfter(today)) {
            errorCodes.push(RegistrationErrorCode.ErrorCodeBirthdate);
        }

        // validate password
        if (PASSWORD_PATTERN.test(WebManager.sharedInstance().registerPasswordInput.value) == false) {
            errorCodes.push(RegistrationErrorCode.ErrorCodePassword);
        }

        if ((WebManager.sharedInstance().registerPasswordInput.value !== WebManager.sharedInstance().registerConfirmPasswordInput.value) ||
            WebManager.sharedInstance().registerConfirmPasswordInput.value.length === 0) {
            errorCodes.push(RegistrationErrorCode.ErrorCodeConfirmPassword);
        }

        // validate email
        if (EMAIL_PATTERN.test(WebManager.sharedInstance().registerEmailInput.value) == false) {
            errorCodes.push(RegistrationErrorCode.ErrorCodeEmail);
        }

        createRegistrationErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        var params = {
            'username' : WebManager.sharedInstance().registerUsernameInput.value,
            'birthdate' : WebManager.sharedInstance().registerBirthdateInput.value,
            'password' : WebManager.sharedInstance().registerPasswordInput.value,
            'email' : WebManager.sharedInstance().registerEmailInput.value,
            'profileImage' : profileImageHref
        };

        $.ajax({
             data:  params,
             url:  '/register',
             type:  'POST',

             success: function (response) {
                 createRegistrationErrorsForCodes(response);
             }
         })
    },

    eventSelectImage: function(event) {

        event.preventDefault();

        var val = $(this).val();

        if (!val) return;

        switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()) {
            case 'gif': case 'jpg': case 'png': {
                var oFReader = new FileReader();
                profileImageHref = this.files[0];

                oFReader.readAsDataURL(profileImageHref);
                oFReader.onload = function (oFREvent) {
                    WebManager.sharedInstance().registerProfileImage.src = oFREvent.target.result;
                };
            }
                break;
            default:
                $(this).val('');
                alert("not an image");
                break;
        }
    },

    eventValidateUsername: function(event) {

        event.preventDefault();

        if (USERNAME_PATTERN.test(WebManager.sharedInstance().registerUsernameInput.value) == false ||
            WebManager.sharedInstance().registerUsernameInput.value.length > 20) {
            createUsernameIndicator(false);
        }
        else createUsernameIndicator(true);
    },

    eventValidateBirthdate: function(event) {

        event.preventDefault();

        if (moment(WebManager.sharedInstance().registerBirthdateInput.value, "YYYY-MM-DD", true).isValid() == false ||
            moment(WebManager.sharedInstance().registerBirthdateInput.value).isAfter(today)) {
            createBirthdateIndicator(false);
        }
        else {
            createBirthdateIndicator(true);
        }
    },

    eventValidatePassword: function(event) {

        event.preventDefault();

        if (PASSWORD_PATTERN.test(WebManager.sharedInstance().registerPasswordInput.value) == false) {
            createPasswordIndicator(false);
        }
        else createPasswordIndicator(true);
    },

    eventValidateConfirmPassword: function(event) {

        event.preventDefault();

        if ((WebManager.sharedInstance().registerPasswordInput.value !== WebManager.sharedInstance().registerConfirmPasswordInput.value) ||
            WebManager.sharedInstance().registerConfirmPasswordInput.value.length === 0) {
            createConfirmPasswordIndicator(false);
        }
        else createConfirmPasswordIndicator(true);
    },

    eventValidateEmail: function(event) {

        event.preventDefault();

        if (EMAIL_PATTERN.test(WebManager.sharedInstance().registerEmailInput.value) == false) createEmailIndicator(false);
        else createEmailIndicator(true);
    }
}


/* ************* METHODS ****************/

/**
 * Creates visual errors upon an array containing validation results for registration
 * @param errorCodes array containing error's ID
 */
function createRegistrationErrorsForCodes(errorCodes) {

    createUsernameIndicator(errorCodes.indexOf(RegistrationErrorCode.ErrorCodeUsername) == -1);
    createBirthdateIndicator(errorCodes.indexOf(RegistrationErrorCode.ErrorCodeBirthdate) == -1);
    createPasswordIndicator(errorCodes.indexOf(RegistrationErrorCode.ErrorCodePassword) == -1);
    createConfirmPasswordIndicator(errorCodes.indexOf(RegistrationErrorCode.ErrorCodeConfirmPassword) == -1);
    createEmailIndicator(errorCodes.indexOf(RegistrationErrorCode.ErrorCodeEmail) == -1);

    if (errorCodes.indexOf(RegistrationErrorCode.ErrorCodeUsernameUnavailable) != -1) {
        var div = document.createElement('div');
        div.className = 'alert alert-danger alert-dismissible fade show';
        div.setAttribute('role', 'alert');
        div.setAttribute("id", "registrationAlert");

        var button = document.createElement("button");
        button.className = "close";
        button.setAttribute("data-dismiss", "alert");
        button.setAttribute("aria-label", "Close");

        var span = document.createElement("span");
        span.setAttribute("aria-hidden", "true");
        span.innerHTML = "&times;";

        var message = document.createElement('h7');
        message.innerHTML = 'The username you choose is already in use.';

        button.appendChild(span);
        div.appendChild(button);
        div.appendChild(message);

        var modal = WebManager.sharedInstance().registrationModal;
        modal.insertBefore(div, modal.children[0]);

        $("#registrationAlert").fadeTo(4000, 500).slideUp(500, function(){
            $("#registrationAlert").slideUp(500);
        });
    }
    else if (errorCodes.indexOf(RegistrationErrorCode.ErrorCodeEmailUnavailable) != -1) {
        var div = document.createElement('div');
        div.className = 'alert alert-danger alert-dismissible fade show';
        div.setAttribute('role', 'alert');
        div.setAttribute("id", "registrationAlert");

        var button = document.createElement("button");
        button.className = "close";
        button.setAttribute("data-dismiss", "alert");
        button.setAttribute("aria-label", "Close");

        var span = document.createElement("span");
        span.setAttribute("aria-hidden", "true");
        span.innerHTML = "&times;";

        var message = document.createElement('h7');
        message.innerHTML = 'The email you entered is already linked to another account.';

        button.appendChild(span);
        div.appendChild(button);
        div.appendChild(message);

        var modal = WebManager.sharedInstance().registrationModal;
        modal.insertBefore(div, modal.children[0]);

        $("#registrationAlert").fadeTo(4000, 500).slideUp(500, function(){
            $("#registrationAlert").slideUp(500);
        });
    }
    else if (errorCodes.indexOf(RegistrationErrorCode.ErrorCodeRegistrationSuccessful) != -1) {
        var div = document.createElement('div');
        div.className = 'alert alert-success alert-dismissible fade show';
        div.setAttribute('role', 'alert');
        div.setAttribute("id", "registrationAlert");

        var button = document.createElement("button");
        button.className = "close";
        button.setAttribute("data-dismiss", "alert");
        button.setAttribute("aria-label", "Close");

        var span = document.createElement("span");
        span.setAttribute("aria-hidden", "true");
        span.innerHTML = "&times;";

        var strong = document.createElement('strong');
        strong.innerHTML = 'Registration successful! ';

        var message = document.createElement('h7');
        message.innerHTML = 'An activation link has been sent to your email account. Click the link in order to have full access to PWGram.';

        var span = document.createElement('span');
        span.appendChild(strong);
        span.appendChild(message);

        button.appendChild(span);
        div.appendChild(button);
        div.appendChild(span);

        var modal = WebManager.sharedInstance().registrationModal;
        modal.insertBefore(div, modal.children[0]);
    }
}

/**
 * Creates visual errors for username input
 * @param validFormat boolean determining wheter there's an error or not
 */
function createUsernameIndicator(validFormat) {

    if (!validFormat) {
        WebManager.sharedInstance().registerUsernameInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().registerUsernameGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().registerUsernameGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Username can only contain alphanumeric characters and cannot exceed 20 characters';

            WebManager.sharedInstance().registerUsernameGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().registerUsernameInput.className = "form-control form-control-success";
        WebManager.sharedInstance().registerUsernameGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().registerUsernameGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().registerUsernameGroup.childNodes;
            WebManager.sharedInstance().registerUsernameGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors for birthdate input
 * @param validFormat boolean determining wheter there's an error or not
 */
function createBirthdateIndicator(validFormat) {

    if (!validFormat) {
        WebManager.sharedInstance().registerBirthdateInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().registerBirthdateGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().registerBirthdateGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Birthdate format has to be YYYY-MM-DD and it cannot be a future date';

            WebManager.sharedInstance().registerBirthdateGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().registerBirthdateInput.className = "form-control form-control-success";
        WebManager.sharedInstance().registerBirthdateGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().registerBirthdateGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().registerBirthdateGroup.childNodes;
            WebManager.sharedInstance().registerBirthdateGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors for passowrd input
 * @param validFormat boolean determining wheter there's an error or not
 */
function createPasswordIndicator(validFormat) {

    if (!validFormat) {
        WebManager.sharedInstance().registerPasswordInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().registerPasswordGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().registerPasswordGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Password must contain between 6 and 12 characters, both uppercase and lowercase letters and at least one number';

            WebManager.sharedInstance().registerPasswordGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().registerPasswordInput.className = "form-control form-control-success";
        WebManager.sharedInstance().registerPasswordGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().registerPasswordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().registerPasswordGroup.childNodes;
            WebManager.sharedInstance().registerPasswordGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors for confirm password input
 * @param validFormat boolean determining wheter there's an error or not
 */
function createConfirmPasswordIndicator(validFormat) {

    if (!validFormat) {
        WebManager.sharedInstance().registerConfirmPasswordInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().registerConfirmPasswordGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().registerConfirmPasswordGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Content does not match typed password';

            WebManager.sharedInstance().registerConfirmPasswordGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().registerConfirmPasswordInput.className = "form-control form-control-success";
        WebManager.sharedInstance().registerConfirmPasswordGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().registerConfirmPasswordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().registerConfirmPasswordGroup.childNodes;
            WebManager.sharedInstance().registerConfirmPasswordGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors for email input
 * @param validFormat boolean determining wheter there's an error or not
 */
function createEmailIndicator(validFormat) {

    if (!validFormat) {
        WebManager.sharedInstance().registerEmailInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().registerEmailGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().registerEmailGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Wrong email format';

            WebManager.sharedInstance().registerEmailGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().registerEmailInput.className = "form-control form-control-success";
        WebManager.sharedInstance().registerEmailGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().registerEmailGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().registerEmailGroup.childNodes;
            WebManager.sharedInstance().registerEmailGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors upon an array containing validation results for login
 * @param errorCodes array containing error's ID
 */
function createLoginErrorsForCodes(errorCodes) {

    if (errorCodes.indexOf(LoginErrorCode.ErrorCodeUsername) != -1) {
        WebManager.sharedInstance().loginUsernameInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().loginUsernameGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().loginUsernameGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'This field cannot be left empty';

            WebManager.sharedInstance().loginUsernameGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().loginUsernameInput.className = "form-control";
        WebManager.sharedInstance().loginUsernameGroup.className = "form-group";

        if (WebManager.sharedInstance().loginUsernameGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().loginUsernameGroup.childNodes;
            WebManager.sharedInstance().loginUsernameGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (errorCodes.indexOf(LoginErrorCode.ErrorCodePassword) != -1) {
        WebManager.sharedInstance().loginPasswordInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().loginPasswordGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().loginPasswordGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'This field cannot be left empty';

            WebManager.sharedInstance().loginPasswordGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().loginPasswordInput.className = "form-control";
        WebManager.sharedInstance().loginPasswordGroup.className = "form-group";

        if (WebManager.sharedInstance().loginPasswordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().loginPasswordGroup.childNodes;
            WebManager.sharedInstance().loginPasswordGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (errorCodes.indexOf(LoginErrorCode.ErrorCodeNotFound) != -1) {
        var div = document.createElement('div');
        div.className = 'alert alert-danger alert-dismissible fade show';
        div.setAttribute('role', 'alert');
        div.setAttribute("id", "loginAlert");

        var button = document.createElement("button");
        button.className = "close";
        button.setAttribute("data-dismiss", "alert");
        button.setAttribute("aria-label", "Close");

        var span = document.createElement("span");
        span.setAttribute("aria-hidden", "true");
        span.innerHTML = "&times;";

        var message = document.createElement('h7');
        message.innerHTML = 'Invalid login credentials';

        button.appendChild(span);
        div.appendChild(button);
        div.appendChild(message);

        var modal = WebManager.sharedInstance().loginModal;
        modal.insertBefore(div, modal.children[0]);

        $("#loginAlert").fadeTo(3000, 500).slideUp(500, function(){
            $("#loginAlert").slideUp(500);
        });
    }
    else if (errorCodes.indexOf(LoginErrorCode.ErrorCodeNotConfirmed) != -1) {
        var div = document.createElement('div');
        div.className = 'alert alert-warning alert-dismissible fade show';
        div.setAttribute('role', 'alert');
        div.setAttribute("id", "loginAlert");

        var button = document.createElement("button");
        button.className = "close";
        button.setAttribute("data-dismiss", "alert");
        button.setAttribute("aria-label", "Close");

        var span = document.createElement("span");
        span.setAttribute("aria-hidden", "true");
        span.innerHTML = "&times;";

        var message = document.createElement('h7');
        message.innerHTML = 'You have not activated your account yet. Click the link sent to your email account in order to activate it.';

        button.appendChild(span);
        div.appendChild(button);
        div.appendChild(message);

        var modal = WebManager.sharedInstance().loginModal;
        modal.insertBefore(div, modal.children[0]);
    }
    else if (errorCodes.indexOf(LoginErrorCode.ErrorCodeLoginSuccessful) != -1) {
        $('#login').modal('toggle');
    }
}

/**
 * Page stating point
 */
window.onload = function() {

    today = moment().format("YYYY-MM-DD");

    Listener.add(WebManager.sharedInstance().loginButton, "click", Listener.eventLogin, true);
    Listener.add(WebManager.sharedInstance().registerButton, "click", Listener.eventRegister, true);
    Listener.add(WebManager.sharedInstance().registerImageButton, "change", Listener.eventSelectImage, true);
    Listener.add(WebManager.sharedInstance().registerUsernameInput, "input", Listener.eventValidateUsername, true);
    Listener.add(WebManager.sharedInstance().registerBirthdateInput, "input", Listener.eventValidateBirthdate, true);
    Listener.add(WebManager.sharedInstance().registerPasswordInput, "input", Listener.eventValidatePassword, true);
    Listener.add(WebManager.sharedInstance().registerConfirmPasswordInput, "input", Listener.eventValidateConfirmPassword, true);
    Listener.add(WebManager.sharedInstance().registerEmailInput, "input", Listener.eventValidateEmail, true);
};