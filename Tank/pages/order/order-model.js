import {
  Base
} from '../../utils/Base.js'

class Order extends Base {

  constructor() {
    super();
    this._storageKeyName = 'newOrder';
  }

  doOrder(param, callBack) {
    var that = this;
    var allParams = {
      url: 'order',
      type: 'post',
      data: {
        products: param
      },
      sCallBack: function(data) {
        that.execSetStorageSync(true);
        callBack && callBack(data);
      },
      eCallBack: function() {}
    };
    this.request(allParams);
  }

  execSetStorageSync(data) {
    wx.setStorageSync(this._storageKeyName, data);
  };

  execPay(orderNumber, callBack) {
    var allParams = {
      url: 'pay/pre_order',
      type: 'post',
      data: {
        id: orderNumber
      },
      sCallBack: function(data) {
        var timeStamp = data.timeStamp;
        if (timeStamp) { //可以支付
          wx.requestPayment({
            'timeStamp': timeStamp.toString(),
            'nonceStr': data.nonceStr,
            'package': data.package,
            'signType': data.signType,
            'paySign': data.paySign,
            success: function() {
              callBack && callBack(2);
            },
            fail: function() {
              callBack && callBack(1);
            }
          });
        } else {
          callBack && callBack(0);
        }
      }
    };
    this.request(allParams);
  }

  getOrderInfoById(id, callBack) {
    var that = this;
    var allParams = {
      url: 'order/' + id,
      sCallBack: function(data) {
        callBack && callBack(data);
      },
      eCallBack: function() {

      }
    };
    this.request(allParams);
  }

  getOrders(pageIndex, callBack) {
    var allParmas = {
      url: 'order/by_user',
      data: { page: pageIndex },
      type: "post",
      sCallBack: function (data) {
        callBack && callBack(data);
      }
    };
    this.request(allParmas);
  }

  hasNewOrder(){
    var flag = wx.getStorageSync(this._storageKeyName);
    return false == true;
  }
}

export {
  Order
};