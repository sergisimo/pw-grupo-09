/**
 * Created by borjaperez on 13/5/17.
 */

const REMOVE_POST_BUTTON = 'removePostButton';

var postToDelete = null;

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.removePostButton = document.getElementById(REMOVE_POST_BUTTON);
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

    eventRemovePost: function(event) {

        event.preventDefault();

        $('#deletePostModal').modal('toggle');
        removePost();
    }
}

function removePost() {

    console.log('Removing post: ' + postToDelete);
}

window.onload = function() {

    Listener.add(WebManager.sharedInstance().removePostButton, "click", Listener.eventRemovePost, true);

    $('#deletePostModal').on('show.bs.modal', function (e) {
        var $invoker = $(e.relatedTarget);
        postToDelete = $invoker[0].id.split('-')[1];
    });
}