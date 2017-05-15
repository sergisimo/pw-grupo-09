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


/* ************* METHODS ****************/

/**
 * Page stating point
 */
window.onload = function() {

    var childCount = WebManager.sharedInstance().notificationsDiv.childElementCount;
    WebManager.sharedInstance().notificationsDiv.removeChild(WebManager.sharedInstance().notificationsDiv.children[childCount - 1]);
};