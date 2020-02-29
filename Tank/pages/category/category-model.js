import { Base } from '../../utils/Base.js';

class Category extends Base{

  getCatrgoryType(callBack) {
    var param = {
      url: 'category/all',
      sCallBack: function (data) {
        callBack && callBack(data);
      }
    }
    this.request(param);
  }

  getProductByCatrgory(id,callBack) {
    var param = {
      url: 'product/by_category?id=' + id,
      sCallBack: function (data) {
        callBack && callBack(data);
      }
    }
    this.request(param);
  }

}

export {Category};