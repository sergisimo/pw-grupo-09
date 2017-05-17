/**
 * Created by borjaperez on 11/5/17.
 */

const UPDATE_BUTTON = 'postButton';
const IMAGE_BUTTON = 'selectImageButton';
const PROFILE_IMAGE = 'postImage';
const USERNAME_GROUP = 'usernameGroup';
const USERNAME_INPUT = 'usernameInput';
const BIRTHDATE_GROUP = 'birthdateGroup';
const BIRTHDATE_INPUT = 'birthdateInput';
const PASSWORD_GROUP = 'passwordGroup';
const PASSWORD_INPUT = 'passwordInput';

const UpdateErrorCode = {
    ErrorCodeUsername : 0,
    ErrorCodeBirthdate : 1,
    ErrorCodePassword : 2
}
var file;

var params = {};

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.updateButton = document.getElementById(UPDATE_BUTTON);
        this.imageButton = document.getElementById(IMAGE_BUTTON);
        this.profileImage = document.getElementById(PROFILE_IMAGE);
        this.usernameGroup = document.getElementById(USERNAME_GROUP);
        this.usernameInput = document.getElementById(USERNAME_INPUT);
        this.birthdateGroup = document.getElementById(BIRTHDATE_GROUP);
        this.birthdateInput = document.getElementById(BIRTHDATE_INPUT);
        this.passwordGroup = document.getElementById(PASSWORD_GROUP);
        this.passwordInput = document.getElementById(PASSWORD_INPUT);
    }

    return {
        sharedInstance: function() {

            if (this.instance == null) this.instance = new WebManager();

            return this.instance;
        }
    };
})();

/**
 * Utilities object with helper methods
 * @type {{createAlert: Utilities.createAlert}}
 */
var Utilities = {

    createLoadingIndicator: function() {

        var i = document.createElement('i');
        i.className = 'fa fa-spinner fa-pulse fa-2x align-middle ml-3';

        return i;
    }
}

/**
 * Function object for adding listeners with calbacks to elements
 * @type {{add: Listener.add, eventSendMessage: Listener.eventSendMessage}}
 */
var Listener = {

    add: function(object, event, callback, capture) {

        object.addEventListener(event, callback, capture);
    },

    eventUpdateInfo: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        var today = moment().format("YYYY-MM-DD");

        var usernamePattern = /^[a-z0-9]+$/i;
        var passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,12}$/;

        // validate username
        if (WebManager.sharedInstance().usernameInput.value.length != 0 && (usernamePattern.test(WebManager.sharedInstance().usernameInput.value) == false ||
            WebManager.sharedInstance().usernameInput.value.length > 20)) {
            errorCodes.push(UpdateErrorCode.ErrorCodeUsername);
        }

        // validate date
        if (WebManager.sharedInstance().birthdateInput.value.length != 0 && (moment(WebManager.sharedInstance().birthdateInput.value, "YYYY-MM-DD", true).isValid() == false ||
            moment(WebManager.sharedInstance().birthdateInput.value).isAfter(today))) {
            errorCodes.push(UpdateErrorCode.ErrorCodeBirthdate);
        }

        // validate password
        if (WebManager.sharedInstance().passwordInput.value.length != 0 && passwordPattern.test(WebManager.sharedInstance().passwordInput.value) == false) {
            errorCodes.push(UpdateErrorCode.ErrorCodePassword);
        }

        createUpdateErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().updateButton;
        button.appendChild(i);

        params['username'] = WebManager.sharedInstance().usernameInput.value;
        params['birthdate'] = WebManager.sharedInstance().birthdateInput.value;
        params['password'] = WebManager.sharedInstance().passwordInput.value;

        console.log(params);

        var data = new FormData();
        data.append('file', file);

        $.ajax({
            data:  params,
            url:   '/updateUser',
            type:  'POST',

            success: function (response) {
                if (params['imageName'] != null) {
                    $.ajax({
                        data:  data,
                        url:  '/uploadImage',
                        type:  'POST',
                        contentType: false,
                        processData: false,
                        cache: false,

                        success: function (response) {
                            button.removeChild(button.children[button.childElementCount - 1]);
                            location.reload();
                        }
                    })
                }
                else {
                    button.removeChild(button.children[button.childElementCount - 1]);
                    location.reload();
                }
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
            file = this.files[0];

            oFReader.readAsDataURL(file);
            oFReader.onload = function (oFREvent) {
                params['imageName'] = file.name;
                WebManager.sharedInstance().profileImage.src = oFREvent.target.result;
            };
        }
            break;
            default:
                $(this).val('');
                alert("not an image");
                break;
        }
    }
}

function createUpdateErrorsForCodes(errorCodes) {

    if (errorCodes.indexOf(UpdateErrorCode.ErrorCodeUsername) != -1) {
        WebManager.sharedInstance().usernameInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().usernameGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().usernameGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Username can only contain alphanumeric characters and cannot exceed 20 characters';

            WebManager.sharedInstance().usernameGroup.appendChild(small);
        }
    }
    else if (WebManager.sharedInstance().usernameInput.value.length == 0) {
        WebManager.sharedInstance().usernameInput.className = "form-control";
        WebManager.sharedInstance().usernameGroup.className = "form-group";

        if (WebManager.sharedInstance().usernameGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().usernameGroup.childNodes;
            WebManager.sharedInstance().usernameGroup.removeChild(childs[childs.length - 1]);
        }
    }
    else {
        WebManager.sharedInstance().usernameInput.className = "form-control form-control-success";
        WebManager.sharedInstance().usernameGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().usernameGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().usernameGroup.childNodes;
            WebManager.sharedInstance().usernameGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (errorCodes.indexOf(UpdateErrorCode.ErrorCodeBirthdate) != -1) {
        WebManager.sharedInstance().birthdateInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().birthdateGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().birthdateGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Birthdate format has to be YYYY-MM-DD and it cannot be a future date';

            WebManager.sharedInstance().birthdateGroup.appendChild(small);
        }
    }
    else if (WebManager.sharedInstance().birthdateInput.value.length == 0) {
        WebManager.sharedInstance().birthdateInput.className = "form-control";
        WebManager.sharedInstance().birthdateGroup.className = "form-group";

        if (WebManager.sharedInstance().birthdateGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().birthdateGroup.childNodes;
            WebManager.sharedInstance().birthdateGroup.removeChild(childs[childs.length - 1]);
        }
    }
    else {
        WebManager.sharedInstance().birthdateInput.className = "form-control form-control-success";
        WebManager.sharedInstance().birthdateGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().birthdateGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().birthdateGroup.childNodes;
            WebManager.sharedInstance().birthdateGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (errorCodes.indexOf(UpdateErrorCode.ErrorCodePassword) != -1) {
        WebManager.sharedInstance().passwordInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().passwordGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().passwordGroup.childElementCount == 2) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'Password must contain between 6 and 12 characters, both uppercase and lowercase letters and at least one number';

            WebManager.sharedInstance().passwordGroup.appendChild(small);
        }
    }
    else if (WebManager.sharedInstance().passwordInput.value.length == 0) {
        WebManager.sharedInstance().passwordInput.className = "form-control";
        WebManager.sharedInstance().passwordGroup.className = "form-group";

        if (WebManager.sharedInstance().passwordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().passwordGroup.childNodes;
            WebManager.sharedInstance().passwordGroup.removeChild(childs[childs.length - 1]);
        }
    }
    else {
        WebManager.sharedInstance().passwordInput.className = "form-control form-control-success";
        WebManager.sharedInstance().passwordGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().passwordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().passwordGroup.childNodes;
            WebManager.sharedInstance().passwordGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

window.onload = function() {

    Listener.add(WebManager.sharedInstance().updateButton, "click", Listener.eventUpdateInfo, true);
    Listener.add(WebManager.sharedInstance().imageButton, "change", Listener.eventSelectImage, true);

    params['imageName'] = null;
};