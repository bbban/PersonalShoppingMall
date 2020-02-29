import { Base } from '../../utils/Base.js';

class Product extends Base{
  constructor(){
    super();
  }

  getDetailInfo(id,callBack){
    var param = {
      url: 'product/' + id,
      sCallBack: function (data) {
        callBack && callBack(data);
      }
    };
    this.request(param);
  }
};

export {Product};