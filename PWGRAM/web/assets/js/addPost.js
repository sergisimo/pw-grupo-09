/**
 * Created by borjaperez on 11/5/17.
 */

const NEW_IMAGE = 'postImage';
const RADIO = 'privateCheck';
const TITLE_GROUP = 'titleGroup';
const TITLE_INPUT = 'titleInput';
const SELECT_IMAGE = 'selectImageButton';
const POST_IMAGE_BUTTON = 'postButton';
const IMAGE_GROUP = 'imageGroup';

const AddPostErrorCode = {
    ErrorCodeImage : 0,
    ErrorCodeTitle: 1
}

var profileImageHref = null;

var private = false;

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
    }

    return {
        sharedInstance: function() {

            if (this.instance == null) this.instance = new WebManager();

            return this.instance;
        }
    };
})();

/**
 * Function object for adding listeners with calbacks to elements
 * @type {{add: Listener.add, eventSendMessage: Listener.eventSendMessage}}
 */
var Listener = {

    add: function(object, event, callback, capture) {

        object.addEventListener(event, callback, capture);
    },

    eventPostImage: function(event) {

        event.preventDefault();

        var errorCodes = new Array();

        // validate username
        if (WebManager.sharedInstance().titleInput.value.length == 0) errorCodes.push(AddPostErrorCode.ErrorCodeTitle);

        // validate image
        if (profileImageHref == null) errorCodes.push(AddPostErrorCode.ErrorCodeImage);

        createPostErrorsForCodes(errorCodes);

        if (errorCodes.length > 0) return;

        var params = {
            'username' : WebManager.sharedInstance().loginUsernameInput.value,
            'password' : WebManager.sharedInstance().loginPasswordInput.value
        };

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

    eventChangePrivacy: function (event) {

        event.preventDefault();

        private = !private;
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
                WebManager.sharedInstance().postImage.src = oFREvent.target.result;
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

window.onload = function() {

    try {
        Listener.add(WebManager.sharedInstance().radioButton, "change", Listener.eventChangePrivacy, true);
        Listener.add(WebManager.sharedInstance().selectImageButton, "change", Listener.eventSelectImage, true);
        Listener.add(WebManager.sharedInstance().postImageButton, "click", Listener.eventPostImage, true);
    }
    catch (err) {}
};
