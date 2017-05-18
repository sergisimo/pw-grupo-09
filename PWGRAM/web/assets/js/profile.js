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
 * Page stating point
 */
window.onload = function() {

    try {
        for (var i = 0; i < WebManager.sharedInstance().dropdown.childElementCount; i++) {
            var a = WebManager.sharedInstance().dropdown.children[i];
            Listener.add(a, "click", Listener.eventChangeSortType, true);
        }
    }
    catch (err) {}

    try {
        Listener.add(WebManager.sharedInstance().updateButton, "click", Listener.eventUpdateInfo, true);
        Listener.add(WebManager.sharedInstance().imageButton, "change", Listener.eventSelectImage, true);

        params['username'] = WebManager.sharedInstance().usernameInput.value;
        params['birthdate'] = WebManager.sharedInstance().birthdateInput.value;
        params['imageSrc'] = WebManager.sharedInstance().profileImage.src;
    }
    catch (err) {}
};