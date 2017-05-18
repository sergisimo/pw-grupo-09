/**
 * Created by borjaperez on 11/5/17.
 */

/* ************* CONSTANTS ****************/
const NEW_IMAGE = 'postImage';
const RADIO = 'privateCheck';
const TITLE_GROUP = 'titleGroup';
const TITLE_INPUT = 'titleInput';
const SELECT_IMAGE = 'selectImageButton';
const POST_IMAGE_BUTTON = 'postButton';
const IMAGE_GROUP = 'imageGroup';
const POST_LIKED = 'postLiked';
const POST_LIKED_ACTION = 'postLikedAction';
const LIKE_TITLE = 'likeTitle';
const POST_COMMENT_BUTTON = 'postCommentButton';
const COMMENT_INPUT = 'commentInput';
const COMMENT_GROUP = 'addCommentGroup';

const AddPostErrorCode = {
    ErrorCodeImage : 0,
    ErrorCodeTitle: 1
}


/* ************* VARIABLES ****************/
var params = {
    'title' : null,
    'imagePath' : null,
    'private' : null
};

var postLiked = null;
var imageSelected = false;
var postID = null;
var file;

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.postImage = document.getElementById(NEW_IMAGE);
        this.radioButton = document.getElementById(RADIO);
        this.titleInput = document.getElementById(TITLE_INPUT);
        this.titleGroup = document.getElementById(TITLE_GROUP);
        this.selectImageButton = document.getElementById(SELECT_IMAGE);
        this.postImageButton = document.getElementById(POST_IMAGE_BUTTON);
        this.imageGroup = document.getElementById(IMAGE_GROUP);
        this.postLikedImage = document.getElementById(POST_LIKED);
        this.postLikedAction = document.getElementById(POST_LIKED_ACTION);
        this.likeTitle = document.getElementById(LIKE_TITLE);
        this.postCommentButton = document.getElementById(POST_COMMENT_BUTTON);
        this.commentInput = document.getElementById(COMMENT_INPUT);
        this.commentGroup = document.getElementById(COMMENT_GROUP);
        this.updatePostButton = document.getElementsByClassName('updateButtonClass')[0];
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
 * Object variable for adding listener events
 * @type {{add: Listener.add, eventEditComment: Listener.eventEditComment, eventRemoveComment: Listener.eventRemoveComment}}
 */
var Listener = {

    add: function(object, event, callback, capture) {

        object.addEventListener(event, callback, capture);
    },

    eventPostImage: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        // validate title
        if (WebManager.sharedInstance().titleInput.value.length == 0) errorCodes.push(AddPostErrorCode.ErrorCodeTitle);

        // validate image selection
        if (imageSelected == false) errorCodes.push(AddPostErrorCode.ErrorCodeImage);

        createPostErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        params['title'] = WebManager.sharedInstance().titleInput.value;

        if (params['private']) params['private'] = 1;
        else params['private'] = 0;

        var data = new FormData();
        data.append('file', file);

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().postImageButton;
        button.appendChild(i);

        $.ajax({
            data:  params,
            url:   '/uploadPost',
            type:  'POST',

            success: function (response) {
                console.log(response);

                $.ajax({
                    data:  data,
                    url:  '/uploadPostImage',
                    type:  'POST',
                    contentType: false,
                    processData: false,
                    cache: false,

                    success: function (response) {
                        console.log(response);
                        button.removeChild(button.children[button.childElementCount - 1]);
                        window.location.href = '/';
                    }
                })
            }
         });
    },

    eventChangePrivacy: function (event) {

        event.preventDefault();

        params['private'] = !params['private'];
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
                    WebManager.sharedInstance().postImage.src = oFREvent.target.result;
                    imageSelected = true;
                    params['imagePath'] = file.name;
                };
            }

                break;
            default:
                $(this).val('');

                break;
        }
    },

    eventLikePost: function(event) {

        event.preventDefault();

        postLiked = !postLiked;

        var params = {
            'imageID' : postID
        }

        if (postLiked) {
            $.ajax({
                data:  params,
                url:   '/likePost',
                type:  'POST',

                success: function (response) {
                    location.reload();
                    console.log(response);
                }
            });
        }
        else {
            $.ajax({
                data:  params,
                url:   '/unlikePost',
                type:  'POST',

                success: function (response) {
                    location.reload();
                    console.log(response);
                }
            });
        }
    },

    eventPostComment: function(event) {

        event.preventDefault();

        var error = false;

        if (WebManager.sharedInstance().commentInput.value.length == 0) error = true;
        else error = false;

        createCommentError(error);

        if (error) return;

        var params = {
            'imageID' : postID,
            'text' : WebManager.sharedInstance().commentInput.value
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

    eventUpdatePost: function(event) {

        event.preventDefault();

        var targetPostID = WebManager.sharedInstance().updatePostButton.id.split('-')[1];

        params['id'] = targetPostID;

        params['title'] = WebManager.sharedInstance().titleInput.value;

        if (params['private']) params['private'] = 1;
        else params['private'] = 0;

        var data = new FormData();
        data.append('file', file);

        var i = Utilities.createLoadingIndicator();
        var button = WebManager.sharedInstance().updatePostButton;
        button.appendChild(i);

        $.ajax({
            data:  params,
            url:   '/editPost',
            type:  'POST',

            success: function (response) {
                console.log(response);

                if (params['imagePath'] != null) {
                    $.ajax({
                        data:  data,
                        url:  '/uploadPostImage',
                        type:  'POST',
                        contentType: false,
                        processData: false,
                        cache: false,

                        success: function (response) {
                            button.removeChild(button.children[button.childElementCount - 1]);
                            console.log(response);
                        }
                    })
                }
                else {
                    button.removeChild(button.children[button.childElementCount - 1]);
                }
            }
        });
    }
}


/* ************* METHODS ****************/

/**
 * Creates visual errors upon an array containing validation results for the new post
 * @param errorCodes array containing error's ID
 */
function createPostErrorsForCodes(errorCodes) {

    if (errorCodes.indexOf(AddPostErrorCode.ErrorCodeTitle) != -1) {
        WebManager.sharedInstance().titleInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().titleGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().titleGroup.childElementCount == 3) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'This field cannot be left empty';

            WebManager.sharedInstance().titleGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().titleInput.className = "form-control form-control-success";
        WebManager.sharedInstance().titleGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().titleGroup.childElementCount == 4) {
            var childs = WebManager.sharedInstance().titleGroup.childNodes;
            WebManager.sharedInstance().titleGroup.removeChild(childs[childs.length - 1]);
        }
    }

    if (errorCodes.indexOf(AddPostErrorCode.ErrorCodeImage) != -1) {
        if (WebManager.sharedInstance().imageGroup.childElementCount == 1) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'You have to select an image';

            WebManager.sharedInstance().imageGroup.appendChild(small);
        }
    }
    else {
        if (WebManager.sharedInstance().imageGroup.childElementCount == 2) {
            var childs = WebManager.sharedInstance().imageGroup.childNodes;
            WebManager.sharedInstance().imageGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Creates visual errors upon a boolean containing validation results for the new comment
 * @param error boolean that determines if there's a format error or not
 */
function createCommentError(error) {

    if (error) {
        WebManager.sharedInstance().commentInput.className = "form-control form-control-danger";
        WebManager.sharedInstance().commentGroup.className = "form-group has-danger";

        if (WebManager.sharedInstance().commentGroup.childElementCount == 1) {
            var small = document.createElement('small');
            small.className = 'form-text text-danger';
            small.innerHTML = 'This field cannot be left empty';

            WebManager.sharedInstance().commentGroup.appendChild(small);
        }
    }
    else {
        WebManager.sharedInstance().commentInput.className = "form-control form-control-success";
        WebManager.sharedInstance().commentGroup.className = "form-group has-success";

        if (WebManager.sharedInstance().commentGroup.childElementCount == 2) {
            var childs = WebManager.sharedInstance().commentGroup.childNodes;
            WebManager.sharedInstance().commentGroup.removeChild(childs[childs.length - 1]);
        }
    }
}

/**
 * Page stating point
 */
window.onload = function() {

    try {
        Listener.add(WebManager.sharedInstance().radioButton, "change", Listener.eventChangePrivacy, true);
        Listener.add(WebManager.sharedInstance().selectImageButton, "change", Listener.eventSelectImage, true);
        Listener.add(WebManager.sharedInstance().postImageButton, "click", Listener.eventPostImage, true);
    }
    catch (err) {}

    try {
        params['title'] = WebManager.sharedInstance().titleInput.value;
    }
    catch (err) {}

    try {
        Listener.add(WebManager.sharedInstance().postLikedAction, "click", Listener.eventLikePost, true);

        if (document.getElementsByClassName('img-liked').length == 0) {
            var likeDiv = document.getElementsByClassName('img-unliked')[0];

            postID = likeDiv.className.split(' ')[2].split('-')[1];

            postLiked = false;

        }
        else {
            var likeDiv = document.getElementsByClassName('img-liked')[0];

            postID = likeDiv.className.split(' ')[2].split('-')[1];

            postLiked = true;
        }
    }
    catch (err) {}

    console.log(postLiked);

    //params['imagePath'] = WebManager.sharedInstance().postImage.src;

    try {
        params['private'] = WebManager.sharedInstance().radioButton.checked;
    }
    catch (err) {
        params['private'] = false;
    }

    try {
        if (WebManager.sharedInstance().postImageButton.innerHTML == 'Update') imageSelected = true;
    }
    catch (err) {}

    try {
        Listener.add(WebManager.sharedInstance().postCommentButton, "click", Listener.eventPostComment, true);
    }
    catch (err) {}

    try {
        Listener.add(WebManager.sharedInstance().updatePostButton, "click", Listener.eventUpdatePost, true);
    }
    catch (err) {}

    console.log(params);
};
