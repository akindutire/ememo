var FrontSide = angular.module('app', [ 'ngSanitize' ]);

FrontSide.controller('FrontSideCtrl', function($scope, $http, $sce, $location, $compile) {

    /**
     * Configurations
     */

    // Configure toastr
    $scope.setToastrOptions = (function() {
        toastr.options.positionClass = 'toast-top-right';
        toastr.options.closeButton = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.hideMethod = 'slideUp';
        //toastr.options.newestOnTop = false;
        toastr.options.progressBar = false;
        toastr.options.timeOut = 0;
    })();

    /** States and Models */
    $scope.states = {};
    $scope.models = {};

    /** Procedures */
    $scope.login = function(e){

        e.preventDefault();

        item = e.target;
        url = item.getAttribute('data-url');

        toastr.info('Processing');

        data = {
            username:$scope.models.loginUsername,
            password:$scope.models.loginPassword
        };

        data = JSON.stringify(data);
        Promise = $http.post(url, data);

        Promise.then(
            function(response){

                if (response.data.status == true)
                        window.location = $scope.states.dashboardUri;
                else
                    toastr.error(response.data.msg);

            },
            function(status){}
        );
    };

});