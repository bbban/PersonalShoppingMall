// pages/my/my.js
import {
  Address
} from '../../utils/address.js';
import {
  Order
} from '../order/order-model.js';
import {
  My
} from '../my/my-model.js';

var address = new Address();
var order = new Order();
var my = new My();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    pageIndex:1,
    orderArr:[],
    isLoadAll:true
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this._loadData();
    this._getAddressInfo();
  },

  _loadData: function() {
    var that = this;
    my.getUserInfo((data) => {
      that.setData({
        userInfo: data
      });

    });

    this._getOrders();
    // order.execSetStorageSync(false);  //更新标志位
  },

  _getAddressInfo: function() {
    var that = this;
    address.getAddress((addressInfo) => {
      that._bindAddressInfo(addressInfo);
    });
  },

  _bindAddressInfo: function(addressInfo) {
    this.setData({
      addressInfo: addressInfo
    });
  },

  _getOrders: function(callBack) {
    order.getOrders(this.data.pageIndex,(res)=>{
      var data = res.data;

      if(data.length>0){
        this.data.orderArr.push.apply(this.data.orderArr,data);
        this.setData({
          orderArr: this.data.orderArr
        });
      }else{
        this.data.isLoadAll =true;
      }
      callBack && callBack();
    });
  },

  onReachBottom:function(){
    if(!this.data.isLoadAll){
      this.data.pageIndex++;
      this._getOrders();
    }
  },

  showOrderDetailInfo:function(event){
    var id = order.getDataSet(event,'id');

    wx.navigateTo({
      url: '../order/order?from=order&id=' + id,
    })
  },

  rePay:function(event){
    var id = order.getDataSet(event,'id');
    var index = order.getDataSet(event,'index');
    this._execPay(id,index);
  },

  _execPay: function (id, index) {
    if (!order.onPay) {
      this.showTips('支付提示', '本产品仅是实验作品，支付系统已屏蔽', false);
      var flag = false;
      wx.navigateTo({
        url: '../pay-result/pay-result'
      });
      return;
    }
    var that = this;
    order.execPay(id, (statusCode) => {
      if (statusCode > 0) {
        var flag = statusCode == 2;

        //更新订单显示状态
        if (flag) {
          that.data.orderArr[index].status = 2;
          that.setData({
            orderArr: that.data.orderArr
          });
        }

        //跳转到 成功页面
        wx.navigateTo({
          url: '../pay-result/pay-result?id=' + id + '&flag=' + flag + '&from=my'
        });
      } else {
        that.showTips('支付失败', '商品已下架或库存不足');
      }
    });
  },

  showTips: function (title, content, flag) {
    wx.showModal({
      title: title,
      content: content,
      showCancel: false,
      success: function (res) {
      }
    });
  },


  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    wx.setNavigationBarTitle({
      title: '用户信息',
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    var newOrderFlag = order.hasNewOrder();
    if(newOrderFlag){
      this.refresh();
    }
    
  },

  refresh:function(){
    var that = this;
    this.data.orderArr = [];
    this._getOrders(()=>{
      that.data.isLoadAll =false;
      that.data.pageIndex = 1;
      order.execSetStorageSync(false);
    });
  },

  editAddress: function (event) {
    var that = this;
    wx.chooseAddress({
      success: function (res) {
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

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  }
})