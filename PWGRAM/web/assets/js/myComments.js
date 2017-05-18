/**
 * Created by borjaperez on 13/5/17.
 */

/* ************* CONSTANTS ****************/
const COMMENTS = 'comments';
const COMMENT_TEXT_AREA = 'commentTextArea';
const COMMENT_GROUP = 'commentGroup';
const DELETE_BUTTON = 'deleteCommentButton';
const EDIT_BUTTON = 'editCommentButton';

/* ************* VARIABLES ****************/
var targetPost = null;

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.commentDiv = document.getElementById(COMMENTS);
        this.commentTextArea = document.getElementById(COMMENT_TEXT_AREA);
        this.commentGroup = document.getElementById(COMMENT_GROUP);
        this.editButton = document.getElementById(EDIT_BUTTON);
        this.deleteButton = document.getElementById(DELETE_BUTTON);
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

    add: function (object, event, callback, capture) {

        object.addEventListener(event, callback, capture);
    },

    eventEditComment: function(event) {

        event.preventDefault();

        if (commentValidFormat()) {
            var params = {
                'content' : WebManager.sharedInstance().commentTextArea.value,
                'id' : targetPost
            };

            console.log(params);

            $('#editCommentModal').modal('toggle');
        }
    },

    eventRemoveComment: function(event) {

        event.preventDefault();

        var params = {
            'imageID' : targetPost
        };

        console.log(params);

        $.ajax({
            data:  params,
            url:   '/uncommentPost',
            type:  'POST',

            success: function (response) {
                $('#deleteCommentModal').modal('toggle');

                $("#deleteCommentModal").on("hidden.bs.modal", function () {
                    location.reload();
                });
            }
        });
    }
}


/* ************* METHODS ****************/

/**
 * Removes a post comment
 */
function removeComment() {

    console.log('Removing comment: ' + targetPost);
}

/**
 * Validates the the format of the new comment
 * @returns {boolean} true if format is correct, false otherwise
 */
function commentValidFormat() {

    if (WebManager.sharedInstance().commentTextArea.value.length == 0) {
        WebManager.sharedInstance().commentTextArea.className = "form-control form-control-danger";
        WebManager.sharedInstance().commentGroup.className = "form-group has-danger";

        return false;
    }
    else {
        WebManager.sharedInstance().commentTextArea.className = "form-control form-control-success";
        WebManager.sharedInstance().commentGroup.className = "form-group has-success";

        return true;
    }
}

/**
 * Page stating point
 */
window.onload = function() {

    try {
        var childCount = WebManager.sharedInstance().commentDiv.childElementCount;
        WebManager.sharedInstance().commentDiv.removeChild(WebManager.sharedInstance().commentDiv.children[childCount - 1]);

        Listener.add(WebManager.sharedInstance().editButton, "click", Listener.eventEditComment, true);
        Listener.add(WebManager.sharedInstance().deleteButton, "click", Listener.eventRemoveComment, true);
    }
    catch (err) {}

    $('#editCommentModal').on('show.bs.modal', function (e) {
        var $invoker = $(e.relatedTarget);
        targetPost = $invoker[0].id.split('-')[1];
    });

    $('#deleteCommentModal').on('show.bs.modal', function (e) {
        var $invoker = $(e.relatedTarget);
        targetPost = $invoker[0].id.split('-')[1];
    });
};
