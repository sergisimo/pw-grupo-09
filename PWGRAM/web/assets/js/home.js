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
const LOAD_MORE_VIEWED = 'loadMoreViewedButton';
const LOAD_MORE_RECENT = 'loadMoreRecentButton';

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
    ErrorCodeLoginSuccessful : 4
}

const USERNAME_PATTERN = /^[a-z0-9]+$/i;
const PASSWORD_PATTERN = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,12}$/;
const EMAIL_PATTERN = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;

const DEFAULT_PROFILE = 'defaultProfile.png';

/* ************* VARIABLES ****************/
var today;
var file;

var mostRecentCount = 5;
var mostViewedCount = 5;

var params = {};

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
        this.loadMoreRecentButton = document.getElementById(LOAD_MORE_RECENT);
        this.loadMoreViewedButton = document.getElementById(LOAD_MORE_VIEWED);
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

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().loginButton;
        button.appendChild(i);

        var params = {
            'username' : WebManager.sharedInstance().loginUsernameInput.value,
            'password' : WebManager.sharedInstance().loginPasswordInput.value
        };

        $.ajax({
            data:  params,
            url:   '../login',
            type:  'POST',

            success: function (response) {
                button.removeChild(button.children[button.childElementCount - 1]);
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

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().registerButton;
        button.appendChild(i);

        params['username'] = WebManager.sharedInstance().registerUsernameInput.value;
        params['birthdate'] = WebManager.sharedInstance().registerBirthdateInput.value;
        params['password'] = WebManager.sharedInstance().registerPasswordInput.value;
        params['email'] = WebManager.sharedInstance().registerEmailInput.value;

        console.log(params);

        var data = new FormData();
        data.append('file', file);

        $.ajax({
            data:  params,
            url:  '/register',
            type:  'POST',

            success: function (response) {
                if (response[0] == RegistrationErrorCode.ErrorCodeRegistrationSuccessful &&  params['defaultImage'] == false) {
                    $.ajax({
                        data:  data,
                        url:  '/uploadImage',
                        type:  'POST',
                        contentType: false,
                        processData: false,
                        cache: false,

                        success: function (response) {
                            button.removeChild(button.children[button.childElementCount - 1]);
                            createRegistrationErrorsForCodes(response);
                        }
                    })
                }
                else {
                    button.removeChild(button.children[button.childElementCount - 1]);
                    createRegistrationErrorsForCodes(response);
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

                    WebManager.sharedInstance().registerProfileImage.src = oFREvent.target.result;
                    params['imageName'] = file.name;
                    params['defaultImage'] = false;
                };
            }
                break;
            default:
                $(this).val('');
                alert("Only gif, jpg and png are allowed!");
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
    },

    eventPostComment: function(event) {

        event.preventDefault();

        var error = false;

        var postID = event.target.id.split('-')[2];

        var section = event.target.id.split('-')[0];

        console.log(section);

        var commentInput = document.getElementById(section + '-commentInput-' + postID);

        if (commentInput.value.length == 0) error = true;
        else error = false;

        createCommentError(error, postID, section);

        if (error) return;

        var params = {
            'imageID' : postID,
            'text' : commentInput.value
        }

        $.ajax({
            data:  params,
            url:   '/commentPost',
            type:  'POST',

            success: function (response) {
                location.reload();
            }
        });
    },

    eventLikePost: function(event) {

        event.preventDefault();

        var postID = event.target.id.split('-')[1];

        var params = {
            'imageID' : postID
        };

        $.ajax({
            data:  params,
            url:   '/likePost',
            type:  'POST',

            success: function (response) {
                location.reload();
            }
        });
    },

    eventUnlikePost: function(event) {

        event.preventDefault();

        var postID = event.target.id.split('-')[1];

        var params = {
            'imageID' : postID
        };

        $.ajax({
            data:  params,
            url:   '/unlikePost',
            type:  'POST',

            success: function (response) {
                location.reload();
            }
        });
    },

    eventLoadMoreRecent: function(event) {

        event.preventDefault();

        var params = {
            'count' : mostRecentCount
        };

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().loadMoreRecentButton;
        button.appendChild(i);

        $.ajax({
            data:  params,
            url:   '/getMoreMostRecent',
            type:  'POST',

            success: function (response) {
                button.removeChild(button.children[button.childElementCount - 1]);
                appendMostRecent(response);
            }
        });
    },

    eventLoadMoreViewed: function(event) {

        event.preventDefault();

        var params = {
            'count' : mostViewedCount
        };

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().loadMoreViewedButton;
        button.appendChild(i);

        $.ajax({
            data:  params,
            url:   '/getMoreMostViews',
            type:  'POST',

            success: function (response) {
                button.removeChild(button.children[button.childElementCount - 1]);
                appendMostViewed(response);
            }
        });
    }
}

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


/* ************* METHODS ****************/

function appendMostViewed(posts) {

    console.log(posts);

    var active = posts['active'];

    var col = "<div class=\"col-lg-4 col-md-6 col-sm-12 mb-4\">$CARD$$POSTED_INFO$$LIKE_ACTION$$INFO$$ADD_COMMENT$</div>";
    var card = "<div class=\"card mb-3\">$ROW$$IMAGE$</div>";
    var row = "<div class=\"row\">$COL2$</div>";
    var col2 = "<div class=\"col\">$CARD_HEADER$</div>";
    var cardHeader = "<div class=\"card-header card-info text-white\">$CARD_TITLE$</div>";

    //

    var imageContainer = "<div class=\"text-center\">$IMAGE$</div>";
    var image = "<img class=\"card-img-top img-fluid\" src=\"$IMG_SRC$\" alt=\"...\" id=\"postImage\">";

    //

    var imagePostedInfoContainer = "<div class=\"text-left mt-2\">$POSTED_INFO$</div>";
    var imagePostedInfo = "<p>Posted by <h7 class=\"font-weight-bold text-primary\"><a href=\"$USER_PROFILE$\">$USERNAME$</a></h7> on $POST_DATE$</p>";

    //

    var imageLikeContainer = "<div class=\"text-left mt-2\">$LIKE_ACTION$</div>";
    var unlikeImage = "<p><a id=\"postLikedAction-$POST_ID$\" class=\"ml-1 likeAction\" href=''><img class=\"mr-2\" id=\"postLiked-$POST_ID$\" src=\"assets/images/heart-selected.png\" width=\"25\"></a><span id=\"likeTitle\">Dislike</span></p>";
    var likeImage = "<p><a id=\"postLikedAction-$POST_ID$\" class=\"ml-1 unlikeAction\" href=''><img class=\"mr-2\" id=\"postLiked-$POST_ID$\" src=\"assets/images/heart.png\" width=\"25\"></a><span id=\"likeTitle\">Like</span></p>";

    //

    var imageInfoContainer = "<div class=\"text-left\">$INFO$</div>";
    var imageInfo = "<p class=\"text-muted\">$LIKES$ like(s), $VISITS$ visit(s)</p>";

    //

    var commentContainer = "<hr><div class=\"form-group mt-3\" id=\"top-addCommentGroup-$POST_ID$\">$POST_CONTENT$</div>";
    var commentRow = "<div class=\"row\"><div class=\"col-sm-9 mb-2\">$INPUT$</div><div class=\"col-sm-2 float-right\">$BUTTON$</div></div>";

    var input = "<input type=\"email\" class=\"form-control\" id=\"top-commentInput-$POST_ID$\" aria-describedby=\"emailHelp\" placeholder=\"Type comment\">";
    var button = "<button type=\"submit\" class=\"btn btn-primary btn-md postButton\" id=\"top-postCommentButton-$POST_ID$\">Post</button>";

    for (var i = 0; i < posts['posts'].length; i++) {
        var cardTitle = "<h7><a href=" + posts['posts'][i]['postPage'] + ">" + posts['posts'][i]['title'] + "</a></h7>";

        var src = posts['posts'][i]['src'].replace(' ', '/');

        var imageAux = image.replace('$IMG_SRC$', src);
        var imageContainerAux = imageContainer.replace('$IMAGE$', imageAux);

        var imagePostedInfoAux = imagePostedInfo.replace('$USER_PROFILE$', posts['posts'][i]['userProfile']);
        imagePostedInfoAux = imagePostedInfoAux.replace('$USERNAME$', posts['posts'][i]['username']);
        imagePostedInfoAux = imagePostedInfoAux.replace('$POST_DATE$', posts['posts'][i]['postDate']);

        var imagePostedInfoContainerAux = imagePostedInfoContainer.replace('$POSTED_INFO$', imagePostedInfoAux);

        cardHeader = cardHeader.replace('$CARD_TITLE$', cardTitle);
        col2 = col2.replace('$CARD_HEADER$', cardHeader);
        row = row.replace('$COL2$', col2);
        card = card.replace('$ROW$', row);
        card = card.replace('$IMAGE$', imageContainerAux);
        col = col.replace('$CARD$', card);
        col = col.replace('$POSTED_INFO$', imagePostedInfoContainerAux);

        if (active && posts['posts'][i]['liked'] == true) {
            var unlikeImageAux = unlikeImage.replace('$POST_ID$', posts['posts'][i]['id']).replace('$POST_ID$', posts['posts'][i]['id']);

            var imageLikeContainerAux = imageLikeContainer.replace('$LIKE_ACTION$', unlikeImageAux);
            col = col.replace('$LIKE_ACTION$', imageLikeContainerAux);
        }
        else if (active && posts['posts'][i]['liked'] == false) {
            var likeImageAux = likeImage.replace('$POST_ID$', posts['posts'][i]['id']).replace('$POST_ID$', posts['posts'][i]['id']);
            var imageLikeContainerAux = imageLikeContainer.replace('$LIKE_ACTION$', likeImageAux);
            col = col.replace('$LIKE_ACTION$', imageLikeContainerAux);
        }

        var imageInfoAux = imageInfo.replace('$LIKES$', posts['posts'][i]['likes']);
        imageInfoAux = imageInfoAux.replace('$VISITS$', posts['posts'][i]['visits']);

        var imageInfoContainerAux = imageInfoContainer.replace('$INFO$', imageInfoAux);

        col = col.replace('$INFO$', imageInfoContainerAux);

        if (active && posts['posts'][i]['userCanComment']) {
            var buttonAux = button.replace('$POST_ID$', posts['posts'][i]['id']);
            var inputAux = input.replace('$POST_ID$', posts['posts'][i]['id']);
            var commentRowAux = commentRow.replace('$INPUT$', inputAux);
            commentRowAux = commentRowAux.replace('$BUTTON$', buttonAux);
            var commentContainerAux = commentContainer.replace('$POST_CONTENT$', commentRowAux);
            commentContainerAux = commentContainerAux.replace('$POST_ID$', posts['posts'][i]['id']);

            col = col.replace('$ADD_COMMENT$', commentContainerAux);
        }
        else {
            col = col.replace('$ADD_COMMENT$', '');
        }

        $( "#mostViewedDiv" ).append(col);
    }

    if (posts['allLoaded']) {
        WebManager.sharedInstance().loadMoreViewedButton.parentNode.removeChild(WebManager.sharedInstance().loadMoreViewedButton);
    }
    else {
        mostViewedCount += 5;
    }

    try {
        var likeActions = document.getElementsByClassName('likeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventUnlikePost, true);
        }
    }
    catch (err) {}

    try {
        var likeActions = document.getElementsByClassName('unlikeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventLikePost, true);
        }
    }
    catch (err) {}

    try {
        var postButtons = document.getElementsByClassName('postButton');

        for (var i = 0; i < postButtons.length; i++) {
            Listener.add(postButtons[i], "click", Listener.eventPostComment, true);
        }
    }
    catch (err) {}
}

function appendMostRecent(posts) {

    console.log(posts);

    var active = posts['active'];

    var col = "<div class=\"col-lg-4 col-md-6 col-sm-12 mb-4\">$CARD$$POSTED_INFO$$LIKE_ACTION$$INFO$$LAST_COMMENT$$ADD_COMMENT$</div>";
    var card = "<div class=\"card mb-3\">$ROW$$IMAGE$</div>";
    var row = "<div class=\"row\">$COL2$</div>";
    var col2 = "<div class=\"col\">$CARD_HEADER$</div>";
    var cardHeader = "<div class=\"card-header card-info text-white\">$CARD_TITLE$</div>";

    //

    var imageContainer = "<div class=\"text-center\">$IMAGE$</div>";
    var image = "<img class=\"card-img-top img-fluid\" src=\"$IMG_SRC$\" alt=\"...\" id=\"postImage\">";

    //

    var imagePostedInfoContainer = "<div class=\"text-left mt-2\">$POSTED_INFO$</div>";
    var imagePostedInfo = "<p>Posted by <h7 class=\"font-weight-bold text-primary\"><a href=\"$USER_PROFILE$\">$USERNAME$</a></h7> on $POST_DATE$</p>";

    //

    var imageLikeContainer = "<div class=\"text-left mt-2\">$LIKE_ACTION$</div>";
    var unlikeImage = "<p><a id=\"postLikedAction-$POST_ID$\" class=\"ml-1 likeAction\" href=''><img class=\"mr-2\" id=\"postLiked-$POST_ID$\" src=\"assets/images/heart-selected.png\" width=\"25\"></a><span id=\"likeTitle\">Dislike</span></p>";
    var likeImage = "<p><a id=\"postLikedAction-$POST_ID$\" class=\"ml-1 unlikeAction\" href=''><img class=\"mr-2\" id=\"postLiked-$POST_ID$\" src=\"assets/images/heart.png\" width=\"25\"></a><span id=\"likeTitle\">Like</span></p>";

    //

    var imageInfoContainer = "<div class=\"text-left\">$INFO$</div>";
    var imageInfo = "<p class=\"text-muted\">$LIKES$ like(s)</p>";

    //

    var lastCommentContainer = "<hr><div class=\"text-left mb-2\"><h7 class=\"font-weight-bold text-muted\">$COMMENT_USERNAME$</h7><q>$COMMENT_CONTENT$</q></div>";

    //

    var commentContainer = "<hr><div class=\"form-group mt-3\" id=\"bottom-addCommentGroup-$POST_ID$\">$POST_CONTENT$</div>";
    var commentRow = "<div class=\"row\"><div class=\"col-sm-9 mb-2\">$INPUT$</div><div class=\"col-sm-2 float-right\">$BUTTON$</div></div>";

    var input = "<input type=\"email\" class=\"form-control\" id=\"bottom-commentInput-$POST_ID$\" aria-describedby=\"emailHelp\" placeholder=\"Type comment\">";
    var button = "<button type=\"submit\" class=\"btn btn-primary btn-md postButton\" id=\"bottom-postCommentButton-$POST_ID$\">Post</button>";

    for (var i = 0; i < posts['posts'].length; i++) {
        var cardTitle = "<h7><a href=" + posts['posts'][i]['postPage'] + ">" + posts['posts'][i]['title'] + "</a></h7>";

        var src = posts['posts'][i]['src'].replace(' ', '/');

        var imageAux = image.replace('$IMG_SRC$', src);
        var imageContainerAux = imageContainer.replace('$IMAGE$', imageAux);

        var imagePostedInfoAux = imagePostedInfo.replace('$USER_PROFILE$', posts['posts'][i]['userProfile']);
        imagePostedInfoAux = imagePostedInfoAux.replace('$USERNAME$', posts['posts'][i]['username']);
        imagePostedInfoAux = imagePostedInfoAux.replace('$POST_DATE$', posts['posts'][i]['postDate']);

        var imagePostedInfoContainerAux = imagePostedInfoContainer.replace('$POSTED_INFO$', imagePostedInfoAux);

        cardHeader = cardHeader.replace('$CARD_TITLE$', cardTitle);
        col2 = col2.replace('$CARD_HEADER$', cardHeader);
        row = row.replace('$COL2$', col2);
        card = card.replace('$ROW$', row);
        card = card.replace('$IMAGE$', imageContainerAux);
        col = col.replace('$CARD$', card);
        col = col.replace('$POSTED_INFO$', imagePostedInfoContainerAux);

        if (active && posts['posts'][i]['liked'] == true) {
            var unlikeImageAux = unlikeImage.replace('$POST_ID$', posts['posts'][i]['id']).replace('$POST_ID$', posts['posts'][i]['id']);

            var imageLikeContainerAux = imageLikeContainer.replace('$LIKE_ACTION$', unlikeImageAux);
            col = col.replace('$LIKE_ACTION$', imageLikeContainerAux);
        }
        else if (active && posts['posts'][i]['liked'] == false) {
            var likeImageAux = likeImage.replace('$POST_ID$', posts['posts'][i]['id']).replace('$POST_ID$', posts['posts'][i]['id']);
            var imageLikeContainerAux = imageLikeContainer.replace('$LIKE_ACTION$', likeImageAux);
            col = col.replace('$LIKE_ACTION$', imageLikeContainerAux);
        }

        var imageInfoAux = imageInfo.replace('$LIKES$', posts['posts'][i]['likes']);

        var imageInfoContainerAux = imageInfoContainer.replace('$INFO$', imageInfoAux);

        col = col.replace('$INFO$', imageInfoContainerAux);

        if (posts['posts'][i]['lastComment'] != null) {
            var lastCommentContainerAux = lastCommentContainer.replace('$COMMENT_USERNAME$', posts['posts'][i]['lastComment']['username']);
            lastCommentContainerAux = lastCommentContainerAux.replace('$COMMENT_CONTENT$', posts['posts'][i]['lastComment']['content']);
            col = col.replace('$LAST_COMMENT$', lastCommentContainerAux);
        }

        if (active && posts['posts'][i]['userCanComment']) {
            var buttonAux = button.replace('$POST_ID$', posts['posts'][i]['id']);
            var inputAux = input.replace('$POST_ID$', posts['posts'][i]['id']);
            var commentRowAux = commentRow.replace('$INPUT$', inputAux);
            commentRowAux = commentRowAux.replace('$BUTTON$', buttonAux);
            var commentContainerAux = commentContainer.replace('$POST_CONTENT$', commentRowAux);
            commentContainerAux = commentContainerAux.replace('$POST_ID$', posts['posts'][i]['id']);

            col = col.replace('$ADD_COMMENT$', commentContainerAux);
        }
        else {
            col = col.replace('$ADD_COMMENT$', '');
        }

        $( "#mostRecentDiv" ).append(col);
    }

    if (posts['allLoaded']) {
        WebManager.sharedInstance().loadMoreRecentButton.parentNode.removeChild(WebManager.sharedInstance().loadMoreRecentButton);
    }
    else {
        mostViewedCount += 5;
    }

    try {
        var likeActions = document.getElementsByClassName('likeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventUnlikePost, true);
        }
    }
    catch (err) {}

    try {
        var likeActions = document.getElementsByClassName('unlikeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventLikePost, true);
        }
    }
    catch (err) {}

    try {
        var postButtons = document.getElementsByClassName('postButton');

        for (var i = 0; i < postButtons.length; i++) {
            Listener.add(postButtons[i], "click", Listener.eventPostComment, true);
        }
    }
    catch (err) {}
}

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

        $("#login").on("hidden.bs.modal", function () {
            location.reload();
        });
    }
}

function createCommentError(error, postID, section) {

    var commentGroup = document.getElementById(section + '-addCommentGroup-' + postID);
    var commentInput = document.getElementById(section + '-commentInput-' + postID);

    if (error) {
        commentInput.className = "form-control form-control-danger";
        commentGroup.className = "form-group has-danger";
    }
    else {
        commentInput.className = "form-control form-control-success";
        commentGroup.className = "form-group has-success";
    }
}

/**
 * Page stating point
 */
window.onload = function() {

    today = moment().format("YYYY-MM-DD");

    params['imageName'] = DEFAULT_PROFILE;
    params['defaultImage'] = true;

    Listener.add(WebManager.sharedInstance().loginButton, "click", Listener.eventLogin, true);
    Listener.add(WebManager.sharedInstance().registerButton, "click", Listener.eventRegister, true);
    Listener.add(WebManager.sharedInstance().registerImageButton, "change", Listener.eventSelectImage, true);
    Listener.add(WebManager.sharedInstance().registerUsernameInput, "input", Listener.eventValidateUsername, true);
    Listener.add(WebManager.sharedInstance().registerBirthdateInput, "input", Listener.eventValidateBirthdate, true);
    Listener.add(WebManager.sharedInstance().registerPasswordInput, "input", Listener.eventValidatePassword, true);
    Listener.add(WebManager.sharedInstance().registerConfirmPasswordInput, "input", Listener.eventValidateConfirmPassword, true);
    Listener.add(WebManager.sharedInstance().registerEmailInput, "input", Listener.eventValidateEmail, true);

    try {
        var postButtons = document.getElementsByClassName('postButton');

        for (var i = 0; i < postButtons.length; i++) {
            Listener.add(postButtons[i], "click", Listener.eventPostComment, true);
        }
    }
    catch (err) {}

    try {
        var likeActions = document.getElementsByClassName('likeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventUnlikePost, true);
        }
    }
    catch (err) {}

    try {
        var likeActions = document.getElementsByClassName('unlikeAction');

        for (var i = 0; i < likeActions.length; i++) {
            Listener.add(likeActions[i], "click", Listener.eventLikePost, true);
        }
    }
    catch (err) {}

    try {
        Listener.add(WebManager.sharedInstance().loadMoreRecentButton, "click", Listener.eventLoadMoreRecent, true);
        Listener.add(WebManager.sharedInstance().loadMoreViewedButton, "click", Listener.eventLoadMoreViewed, true);
    }
    catch (err) {}
};