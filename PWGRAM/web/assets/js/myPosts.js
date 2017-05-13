/**
 * Created by borjaperez on 13/5/17.
 */

const DELETE_BUTTON = 'removePostButton';

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.removePostButton = document.getElementById(DELETE_BUTTON);
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

    }
}

window.onload = function() {

    Listener.add(WebManager.sharedInstance().removePostButton, "click", Listener.eventRemovePost, true);
}