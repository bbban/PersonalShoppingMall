import { Base } from '../../utils/Base.js';
class Theme extends Base {
  constructor() {
    super();
  }

  /*商品*/
  getProductsData(id, callBack) {
    var param = {
      url: 'theme/' + id,
      sCallBack: function (data) {
        callBack && callBack(data);
      }
    };
    this.request(param);
  }
};

export { Theme };