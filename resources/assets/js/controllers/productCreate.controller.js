angular
    .module('controllers.productCreate', [
        'directives.fileread', 'directives.select2'
    ])
    .controller('ProductCreateController', ProductCreateController);

ProductCreateController.$inject = ['$scope', '$http', '$window'];

/* @ngInject */
function ProductCreateController($scope, $http, $window) {

    function productForm() {
        this.category_id = '';
        this.manufacturer_id = '';
        this.color_id = '';
        this.type = 'simple';
        this.parent_id = '0';
        this.name = '';
        this.code = '';
        this.source_url = '';
        this.image = {};
        this.description = '';
        this.status = true;
        this.attributes = {};
        this.errors = [];
        this.disabled = false;
        this.successful = false;
    };

    $scope.productForm = new productForm();

    $scope.getCategories = function () {
        $http.get('/api/categories')
            .then(function (response) {
                $scope.categories = response.data;
            });
    };

    $scope.getManufacturers = function () {
        $http.get('/api/manufacturers')
            .then(function (response) {
                $scope.manufacturers = response.data;
            });
    };

    $scope.getColors = function () {
        $http.get('/api/colors')
            .then(function (response) {
                $scope.colors = response.data;
            });
    };

    $scope.getProductConfigurables = function () {
        $http.get('/api/products/configurable')
            .then(function (response) {
                $scope.productConfigurables = response.data;
            });
    };

    $scope.refreshData = function () {
        categoryId = $scope.productForm.category_id ? $scope.productForm.category_id : 0;

        $http.get('/api/categories/' + categoryId + '/attributes')
            .then(function (response) {
                $scope.attributes = response.data;

                _.each($scope.attributes, function (attribute) {
                    $scope.productForm.attributes[attribute.slug] = '';
                });
            });
    };

    $scope.getCategories();
    $scope.getManufacturers();
    $scope.getColors();
    $scope.getProductConfigurables();
    $scope.refreshData();

    $scope.addProduct = function () {
        $scope.productForm.errors = [];
        $scope.productForm.disabled = true;
        $scope.productForm.successful = false;
        $http({
            method  : 'POST',
            url     : '/products',
            processData: false,
            transformRequest: function (data) {
                var formData = new FormData();
                for ( var key in data ) {
                    formData.append(key, data[key]);
                }
                return formData;
            },
            data : $scope.productForm,
            headers: {
                'Content-Type': undefined
            }
        }).success(function(response){
            $scope.productForm.successful = true;
            $scope.productForm.disabled = false;

            $window.location.href = '/products';
        }).catch(function (response) {
            if (typeof response.data === 'object') {
                $scope.productForm.errors = _.flatten(_.toArray(response.data));
            } else {
                $scope.productForm.errors = ['Something went wrong. Please try again.'];
            }
            $scope.productForm.disabled = false;
        });
    };
}