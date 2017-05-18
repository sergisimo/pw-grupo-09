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

    eventChangeSortType: function(event) {

        event.preventDefault();

        var newSortType = event.srcElement.id.split('-')[1];

        var currentLocation = window.location.href;
        currentLocation = currentLocation.substr(0, currentLocation.length - 1) + newSortType;

        window.location.href = currentLocation;
    }
}


/* ************* METHODS ****************/


/**
 * Page stating point
 */
window.onload = function() {

    var currentLocation = window.location.href;

    var sortType = currentLocation.substring(currentLocation.length - 1);

    WebManager.sharedInstance().sortTitle.innerHTML = SORT_TITLES[sortType];

    for (var i = 0; i < WebManager.sharedInstance().dropdown.childElementCount; i++) {
            var a = WebManager.sharedInstance().dropdown.children[i];
            Listener.add(a, "click", Listener.eventChangeSortType, true);
        }
};