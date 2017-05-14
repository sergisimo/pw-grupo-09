/**
 * Created by borjaperez on 13/5/17.
 */

const COMMENTS = 'comments';
const DELETE_COMMENT_BUTTON = 'deleteCommentButton';
const EDIT_COMMENT_BUTTON = 'editCommentButton';

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

        $('#editCommentModal').modal('toggle');
        editComment();
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


function editComment() {

    console.log('Editing comment: ' + targetPost);
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
