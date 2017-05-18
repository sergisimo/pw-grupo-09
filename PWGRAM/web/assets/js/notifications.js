/**
 * Created by borjaperez on 15/5/17.
 */

/* ************* CONSTANTS ****************/
const NOTIFICATIONS = 'notifications';

/* ************* VARIABLES ****************/

/**
 * Singleton object with methods for accessing web elements
 * @type {{sharedInstance}}
 */
var WebManager = (function() {

    function WebManager() {

        this.notificationsDiv = document.getElementById(NOTIFICATIONS);
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

    eventMarkAsSeen: function(event) {

        event.preventDefault();

        console.log(event.target.id);

        var params = {
            'notificationID' : event.target.id.split('-')[1]
        };

        console.log(params);

        $.ajax({
            data:  params,
            url:   '/notificationSeen',
            type:  'POST',

            success: function (response) {
                location.reload();
            }
        });
    }
}


/* ************* METHODS ****************/

/**
 * Page stating point
 */
window.onload = function() {

    try {
        var childCount = WebManager.sharedInstance().notificationsDiv.childElementCount;
        WebManager.sharedInstance().notificationsDiv.removeChild(WebManager.sharedInstance().notificationsDiv.children[childCount - 1]);

        var seenButtons = document.getElementsByClassName('btn-primary');

        for (var i = 0; i < seenButtons.length; i++) {
            Listener.add(seenButtons[i], "click", Listener.eventMarkAsSeen, true);
        }
    }
    catch (err) {}
};