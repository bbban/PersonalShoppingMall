// pages/order/order.js
import {
  Order
} from '../order/order-model.js';
import {
  Cart
} from '../cart/cart-model.js';
import {
  Address
} from '../../utils/address.js';


var cart = new Cart();
var address = new Address();
var order = new Order();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id: null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var from = options.from;
    if (from == 'cart') {
      this._fromCart(options.account);
    } else {
      var id = options.id;
      this._fromOrder(options.id);
    }
  },

  _bindAddressInfo: function(addressInfo) {
    this.setData({
      addressInfo: addressInfo
    });
  },

  _fromCart: function(account) {
    var productsArray;
    this.data.account = account;
    productsArray = cart.getOrderDataFromLocal(true);
    this.setData({
      productsArr: productsArray,
      account: account,
      orderStatus: 0
    });
    address.getAddress((res) => {
      this._bindAddressInfo(res);
    })
  },

  pay: function(events) {
    if (!this.data.addressInfo) {
      this.showTips('下单提示', '请填写您的收货地址');
      return;
    }
    if (this.data.orderStatus == 0) {
      this._firstTimePay();
    } else {
      this._oneMoresTimePay();
    }
  },

  _oneMoresTimePay: function() {
    this._execPay(this.data.id);
  },

  _firstTimePay: function() {
    var orderInfo = [];
    var procuctInfo = this.data.productsArr;
    var order = new Order();
    for (let i = 0; i < procuctInfo.length; i++) {
      orderInfo.push({
        product_id: procuctInfo[i].id,
        count: procuctInfo[i].counts
      });
    }

    var that = this;
    order.doOrder(orderInfo, (data) => {
      if (data.pass) {
        //更新订单状态
        var id = data.order_id;
        that.data.id = id;
        // that.data.fromCartFlag = false;

        //开始支付
        that._execPay(id);
      } else {
        that._orderFail(data); // 下单失败
      }
    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    wx.setNavigationBarTitle({
      title: '订单详情',
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    if (this.data.id) {
      this._fromOrder(this.data.id);
    }


  },

  _fromOrder: function(id) {
    console.log(id);
    if (id) {
      var that = this;
      //下单后，支付成功或者失败后，点左上角返回时能够更新订单状态 所以放在onshow中
      // var id = this.data.id;
      order.getOrderInfoById(id, (data) => {
        that.setData({
          orderStatus: data.status,
          productsArr: data.snap_items,
          account: data.total_price,
          basicInfo: {
            orderTime: data.create_time,
            orderNo: data.order_no
          },
        });

        // 快照地址
        var addressInfo = data.snap_address;
        addressInfo.totalDetail = address.setAddressInfo(addressInfo);
        that._bindAddressInfo(addressInfo);
      });
    }
  },

  editAddress: function(event) {
    var that = this;
    wx.chooseAddress({
      success: function(res) {
        var addressInfo = {
          name: res.userName,
          mobile: res.telNumber,
          totalDetail: address.setAddressInfo(res)
        }
        that._bindAddressInfo(addressInfo)

        address.submitAddress(res, (flag) => {
          if (!flag) {
            that.showTips("提示信息", "地址信息更新失败");
          }
        });
      }
    })
  },

  _execPay: function(id) {
    if (!order.onPay) {
      this.showTips('支付提示', '本产品仅是实验作品，支付系统已屏蔽', false);
      this.deleteProducts(); //将已经下单的商品从购物车删除
      var flag = false;
      wx.navigateTo({
        url: '../pay-result/pay-result'
      });
    } else {
      var that = this;
      order.execPay(id, (statusCode) => {
        if (statusCode != 0) {
          that.deleteProducts(); //将已经下单的商品从购物车删除   当状态为0时，表示
          var flag = statusCode == 2;
          wx.navigateTo({
            url: '../pay-result/pay-result?id=' + id + '&flag=' + flag + '&from=order'
          });
        }
      });
    }

  },

  deleteProducts: function() {
    var ids = [];
    var arr = this.data.productsArr;
    for (let i = 0; i < arr.length; i++) {
      ids.push(arr[i].id);
    }
    cart.sdelete(ids);
  },

  _orderFail: function(data) {
    var nameArr = [],
      name = '',
      str = '',
      pArr = data.pStatusArray;
    for (let i = 0; i < pArr.length; i++) {
      if (!pArr[i].haveStock) {
        name = pArr[i].name;
        if (name.length > 15) {
          name = name.substr(0, 12) + '...';
        }
        nameArr.push(name);
        if (nameArr.length >= 2) {
          break;
        }
      }
    }
    str += nameArr.join('、');
    if (nameArr.length > 2) {
      str += ' 等';
    }
    str += ' 缺货';
    wx.showModal({
      title: '下单失败',
      content: str,
      showCancel: false,
      success: function(res) {

      }
    });
  },

  showTips: function(title, content, flag) {
    wx.showModal({
      title: title,
      content: content,
      showCancel: false,
      success: function(res) {
        if (flag) {
          wx.switchTab({
            url: '/pages/my/my'
          });
        }
      }
    });
  },

})