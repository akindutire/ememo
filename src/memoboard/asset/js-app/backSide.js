var BackSide = angular.module('app', [ 'ngSanitize', 'ngFileUpload', 'ngImgCrop', ]);

BackSide.controller('BackSideCtrl', function($scope, $http, $compile, Upload, $sce, $location, $timeout, $interval, $window, $rootScope) {

    /**
     * Configurations
     */

    // Configure toastr
    $scope.setToastrOptions = (function() {
        toastr.options.positionClass = 'toast-top-right';
        toastr.options.closeButton = true;
        toastr.options.showMethod = 'slideDown';
        toastr.options.hideMethod = 'slideUp';
        toastr.options.newestOnTop = false;
        toastr.options.progressBar = false;
        toastr.options.timeOut = 0;
    })();

    /** States and Models */
    $scope.states = {};
    $scope.models = {};

    /** Procedures */
    $scope.sendMemo = function(e){

        e.preventDefault();

        item = e.target;
        url = item.getAttribute('data-url');

        toastr.info('Processing');

        data = {
            itoID:$scope.models.memoTo,
            ref:$scope.models.memoFromRef,
            subject:$scope.models.memoSubject,
        };

        data.body = tinymce.get('MemoMessage').getContent();


        data = JSON.stringify(data);
        Promise = $http.post(url, data);

        Promise.then(
            function(response){

                console.log(response.data);

                if(response.data.status === true)
                    toastr.success(response.data.msg);
                else
                    toastr.error(response.data.msg);


            },
            function(status){}
        );
    };

    $scope.createMemoTemplate = function (e){

        e.preventDefault();

        item = e.target;
        url = item.getAttribute('data-url');

        toastr.info('Processing');

        data = {
            templateName:$scope.models.templateName
        };

        data.templateString = tinymce.get('TemplateString').getContent();

        console.log(data.templateString);

        data = JSON.stringify(data);
        Promise = $http.post(url, data);

        Promise.then(
            function(response){

                console.log(response.data);

                if(response.data.status === true)
                    toastr.success(response.data.msg);
                else
                    toastr.error(response.data.msg);


            },
            function(status){}
        );

    };

    $scope.updateBodyForState = function(){
        $scope.states.TmpMsgBody = tinymce.get('MemoMessage').getContent();

        url = $scope.states.remoteReceipientDetailsUrl;
        data = JSON.stringify({ recipient_id : $scope.models.memoTo });
        Promise = $http.post(url, data);

        Promise.then(
            function (response) {

                $scope.states.receipientDetails = {};
                if (response.data.status === true)
                    $scope.states.receipientDetails = response.data.msg;

            },
            function (status) {
                // console.log(status);
            }
        );
    };

    $scope.createUser = function(e){

        e.preventDefault();

        item = e.target;
        url = item.getAttribute('data-url');

        toastr.info('Processing');

        data = {
            fullname:$scope.models.userFullName,
            username:$scope.models.userName,
            password:$scope.models.userPass,
        };


        data = JSON.stringify(data);
        Promise = $http.post(url, data);

        Promise.then(
            function(response){
                console.log(response.data);
                if(response.data.status === true)
                    toastr.success(response.data.msg);
                else
                    toastr.error(response.data.msg);


            },
            function(status){}
        );
    };

});