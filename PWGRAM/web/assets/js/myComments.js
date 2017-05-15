/**
 * Created by borjaperez on 13/5/17.
 */

const COMMENTS = 'comments';
const DELETE_COMMENT_BUTTON = 'deleteCommentButton';
const EDIT_COMMENT_BUTTON = 'editCommentButton';
const COMMENT_TEXT_AREA = 'commentTextArea';
const COMMENT_GROUP = 'commentGroup';

var targetPost = null;

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.commentDiv = document.getElementById(COMMENTS);
        this.removeCommentButton = document.getElementById(DELETE_COMMENT_BUTTON);
        this.editCommentButton = document.getElementById(EDIT_COMMENT_BUTTON);
        this.commentTextArea = document.getElementById(COMMENT_TEXT_AREA);
        this.commentGroup = document.getElementById(COMMENT_GROUP);
    }

    return {
        sharedInstance: function() {

            if (this.instance == null) this.instance = new WebManager();

            return this.instance;
        }
    };
})();

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

        $('#deleteCommentModal').modal('toggle');
        removeComment();
    }
}

function removeComment() {

    console.log('Removing comment: ' + targetPost);
}


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

window.onload = function() {

    var childCount = WebManager.sharedInstance().commentDiv.childElementCount;
    WebManager.sharedInstance().commentDiv.removeChild(WebManager.sharedInstance().commentDiv.children[childCount - 1]);

    Listener.add(WebManager.sharedInstance().editCommentButton, "click", Listener.eventEditComment, true);
    Listener.add(WebManager.sharedInstance().removeCommentButton, "click", Listener.eventRemoveComment, true);

    $('#editCommentModal').on('show.bs.modal', function (e) {
        var $invoker = $(e.relatedTarget);
        targetPost = $invoker[0].id.split('-')[1];
    });

    $('#deleteCommentModal').on('show.bs.modal', function (e) {
        var $invoker = $(e.relatedTarget);
        targetPost = $invoker[0].id.split('-')[1];
    });
};
