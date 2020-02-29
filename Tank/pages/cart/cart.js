// pages/cart/cart.js
import {
  Cart
} from "cart-model.js";
var cart = new Cart();
Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {
    wx.setNavigationBarTitle({
      title: '购物车',
    })
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {
    var cartData = cart.getCartDataFromLocal();
    var cal = this._calcTotalAccountAndCounts(cartData)
    this.setData({
      selectedCounts: cal.selectedCounts,
      selectedTypeCounts: cal.selectedTypeCounts,
      account: cal.account,
      cartData: cartData
    });
  },

  onHide:function(){
    cart.exec(this.data.cartData);
  },

  _calcTotalAccountAndCounts: function(data) {
    var len = data.length;
    var account = 0;
    var selectedCounts = 0;
    var selectedTypeCounts = 0;
    let multiple = 100;
    for (let i = 0; i < len; i++) {
      if (data[i].selectStatus) {
        account += data[i].counts * multiple * Number(data[i].price) * multiple;
        selectedCounts += data[i].counts;
        selectedTypeCounts++;
      }

    }

    return {
      selectedCounts: selectedCounts,
      selectedTypeCounts: selectedTypeCounts,
      account: account / (multiple * multiple)
    }
  },

  toggleSelect: function(event) {
    var id = cart.getDataSet(event, 'id');
    var status = cart.getDataSet(event, 'status');
    var index = this._getProductIndexById(id);
    this.data.cartData[index].selectStatus = !status;
    this._resetCarData();
  },

  _resetCarData:function(){
    var newData = this._calcTotalAccountAndCounts(this.data.cartData);
    this.setData({
      selectedCounts: newData.selectedCounts,
      selectedTypeCounts: newData.selectedTypeCounts,
      account: newData.account,
      cartData: this.data.cartData
    });
  },

  toggleSelectAll: function(event) {
    var status = cart.getDataSet(event, 'status') == 'true';
    var data = this.data.cartData;
    var len = data.length;
    for(let i = 0;i<len;i++){
      data[i].selectStatus = !status;
    }
    this._resetCarData();

  },

  _getProductIndexById: function(id) {
    var data = this.data.cartData;
    var len = data.length;
    for (let i = 0; i < len; i++) {
      if (data[i].id == id) {
        return i;
      }
    }
  },

  changeCounts:function(event){
    var id = cart.getDataSet(event, 'id');
    var type = cart.getDataSet(event, 'type');
    var index = this._getProductIndexById(id);
    var counts = 1;
    if(type == 'add'){
      cart.addCounts(id);
    }else{
      counts = -1;
      cart.cutCounts(id);
    }

  this.data.cartData[index].counts +=counts;
  this._resetCarData();
  },

  delete:function(event){
    var id = cart.getDataSet(event, 'id');
    var index = this._getProductIndexById(id);
    this.data.cartData.splice(index,1);
    this._resetCarData();
    cart.sdelete(id);
  },

  submitOrder:function(event){
    wx.navigateTo({
      url: '../order/order?account=' + this.data.account + '&from=cart'
    });
  }
})