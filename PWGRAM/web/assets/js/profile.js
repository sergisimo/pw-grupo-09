/**
 * Created by borjaperez on 11/5/17.
 */

/* ************* CONSTANTS ****************/
const UPDATE_BUTTON = 'postButton';
const IMAGE_BUTTON = 'selectImageButton';
const PROFILE_IMAGE = 'postImage';
const USERNAME_GROUP = 'usernameGroup';
const USERNAME_INPUT = 'usernameInput';
const BIRTHDATE_GROUP = 'birthdateGroup';
const BIRTHDATE_INPUT = 'birthdateInput';
const PASSWORD_GROUP = 'passwordGroup';
const PASSWORD_INPUT = 'passwordInput';
const SORT_DROPDOWN = 'sortDropDown';
const SORT_TITLE = 'sortTitle';

const UpdateErrorCode = {
    ErrorCodeUsername : 0,
    ErrorCodeBirthdate : 1,
    ErrorCodePassword : 2
}

const SortType = {
    SortTypeLikes : 0,
    SortTypeComments : 1,
    SortTypeViews : 2,
    SortTypeDate : 3
}

const SORT_TITLES = ['Posts by likes count', 'Posts by comments count', 'Posts by view count','Posts by creation date'];


/* ************* VARIABLES ****************/
var params = {
    'username' : null,
    'birthdate' : null,
    'password' : null,
    'imageSrc' : null
};

var sortParams = {
    'sortType' : SortType.SortTypeDate
}

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
        this.dropdown = document.getElementById(SORT_DROPDOWN);
        this.sortTitle = document.getElementById(SORT_TITLE);
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

    eventUpdateInfo: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        var today = moment().format("YYYY-MM-DD");

        var usernamePattern = /^[a-z0-9]+$/i;
        var passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,12}$/;

        // validate username
        if (usernamePattern.test(WebManager.sharedInstance().usernameInput.value) == false ||
            WebManager.sharedInstance().usernameInput.value.length > 20) {
            errorCodes.push(UpdateErrorCode.ErrorCodeUsername);
        }

        // validate date
        if (moment(WebManager.sharedInstance().birthdateInput.value, "YYYY-MM-DD", true).isValid() == false ||
            moment(WebManager.sharedInstance().birthdateInput.value).isAfter(today)) {
            errorCodes.push(UpdateErrorCode.ErrorCodeBirthdate);
        }

        // validate password
        if (WebManager.sharedInstance().passwordInput.value.length > 0 && passwordPattern.test(WebManager.sharedInstance().passwordInput.value) == false) {
            errorCodes.push(UpdateErrorCode.ErrorCodePassword);
        }

        createUpdateErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        if (WebManager.sharedInstance().passwordInput.value.length > 0) {
            params['password'] = WebManager.sharedInstance().passwordInput.value;
        }

        console.log(params);

        /*$.ajax({
         data:  params,
         url:   URL,
         type:  'POST',

         success: function (response) {
         console.log(response);
         }
         })*/
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
                WebManager.sharedInstance().profileImage.src = oFREvent.target.result;
            };
        }
            break;
            default:
                $(this).val('');
                alert("not an image");
                break;
        }
    },

    eventChangeSortType: function(event) {

        event.preventDefault();

        var newSortType = event.srcElement.id.split('-')[1];
        sortParams['sortType'] = newSortType;

        WebManager.sharedInstance().sortTitle.innerHTML = SORT_TITLES[newSortType];
    }
}


/* ************* METHODS ****************/

/**
 * Creates visual errors upon an array containing validation results for updated fields
 * @param errorCodes array containing error's ID
 */
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
    else {
        WebManager.sharedInstance().birthdateInput.className = "form-control form-control-success";
        WebManager.sharedInstance().birthdateGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().birthdateGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().birthdateGroup.childNodes;
            WebManager.sharedInstance().birthdateGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (WebManager.sharedInstance().passwordInput.value.length > 0) {
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
        else {
            WebManager.sharedInstance().passwordInput.className = "form-control form-control-success";
            WebManager.sharedInstance().passwordGroup.className = "form-group has-success";

            if (WebManager.sharedInstance().passwordGroup.childElementCount == 3) {
                var childs = WebManager.sharedInstance().passwordGroup.childNodes;
                WebManager.sharedInstance().passwordGroup.removeChild(childs[childs.length - 1]);
            }
        }
    }
    else {
        WebManager.sharedInstance().passwordInput.className = "form-control";
        WebManager.sharedInstance().passwordGroup.className = "form-group";

        if (WebManager.sharedInstance().passwordGroup.childElementCount == 3) {
            var childs = WebManager.sharedInstance().passwordGroup.childNodes;
            WebManager.sharedInstance().passwordGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Page stating point
 */
window.onload = function() {

    try {
        for (var i = 0; i < WebManager.sharedInstance().dropdown.childElementCount; i++) {
            var a = WebManager.sharedInstance().dropdown.children[i];
            Listener.add(a, "click", Listener.eventChangeSortType, true);
        }
    }
    catch (err) {
        console.log(err);
    }

    try {
        Listener.add(WebManager.sharedInstance().updateButton, "click", Listener.eventUpdateInfo, true);
        Listener.add(WebManager.sharedInstance().imageButton, "change", Listener.eventSelectImage, true);

        params['username'] = WebManager.sharedInstance().usernameInput.value;
        params['birthdate'] = WebManager.sharedInstance().birthdateInput.value;
        params['imageSrc'] = WebManager.sharedInstance().profileImage.src;
    }
    catch (err) {}
};