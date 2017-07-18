/**
 * Created by jpatton on 7/7/2017.
 */
dispatchLoaded = false;
$(function() {
    if($('#lightDispatchContainer').length) { // This cryptic boolean expression is true if the selector exists
        $('#loading').show();
        $("#lightDispatchContainer").load("/dispatch/dispatch/heavy-dispatch" + window.location.search, function() {
            initializeDispatch();

            $("#loading").hide();
            dispatchLoaded = true;
            checkAndProcessDispatchAndNavBarLoading();
        });
    } else {
        dispatchLoaded = true;
        checkAndProcessDispatchAndNavBarLoading();
    }
});

function checkAndProcessDispatchAndNavBarLoading() {
    if(dispatchLoaded && navBarLoaded) {
        $("#loading").hide();
    }
}