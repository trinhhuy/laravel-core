angular
    .module('controllers.productSaleprice', [])
    .controller('ProductSalepriceController', ProductSalepriceController);

ProductSalepriceController.$inject = ['$scope', '$http', '$window'];

/* @ngInject */
function ProductSalepriceController($scope, $http, $window) {
    $scope.productIsLoaded = false;

    function productSalepriceForm() {
        this.price = 0;
        this.stores = {
            1: false,
            2: false,
            3: false
        };
        this.regions = {
            1: false,
            2: false,
            3: false
        };
        this.errors = [];
        this.disabled = false;
        this.successful = false;
    };

    $scope.productSalepriceForm = new productSalepriceForm();

    $scope.updateMargin = function(){
        if (BEST_PRICE == 0)
            $scope.productMargin = 'Chưa có giá nhập';
        else
            $scope.productMargin = 'Lợi nhuận : ' + (($scope.productSalepriceForm.price / BEST_PRICE - 1 ) * 100).toFixed(2) + ' %';
    };

    $scope.updateMargin();

    $scope.getProduct = function () {
        $http.get('/api/products/' + PRODUCT_ID)
            .then(function (response) {
                $scope.product = response.data;

                $scope.productIsLoaded = true;
            });
    };

    $scope.getProduct();

    $scope.update = function () {
        $scope.productSalepriceForm.errors = [];
        $scope.productSalepriceForm.disabled = true;
        $scope.productSalepriceForm.successful = false;

        $http.put('/products/' + PRODUCT_ID + '/saleprice', $scope.productSalepriceForm)
            .then(function () {
                $scope.productSalepriceForm.successful = true;
                $scope.productSalepriceForm.disabled = false;
            })
            .catch(function (response) {
                if (typeof response.data === 'object') {
                    $scope.productSalepriceForm.errors = _.flatten(_.toArray(response.data));
                } else {
                    $scope.productSalepriceForm.errors = ['Something went wrong. Please try again.'];
                }
                $scope.productSalepriceForm.disabled = false;
            });
    };
}
