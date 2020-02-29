import {Base} from '../../utils/Base.js';
class Home extends Base{

  constructor() {
    super();
  }

  getBannerData(id, callBack){
   var params = {
     url:'banner/' + id,
     sCallBack:function(res){
       callBack && callBack(res.items);
     }
   }
   this.request(params);
  }

  getThemeData(callBack){
    var params = {
      url: 'theme?ids=1,2,3',
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    }
    this.request(params);
  }

  getProductorData(callBack) {
    var param = {
      url: 'product/recent',
      sCallBack: function (res) {
        callBack && callBack(res);
      }
    };
    this.request(param);
  }
}

export {Home};