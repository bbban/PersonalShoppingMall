// pages/product/product.js
import {
  Product
} from "product-model.js";
import {
  Cart
} from "../cart/cart-model.js";

var cart = new Cart();
var product = new Product();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    id: null,
    countsArray: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    productCount: 1,
    tabsItem: ['商品详情', '产品参数', '售后保障'],
    currentTabsIndex: 0,
    cartTotalCounts: cart.getCartTotalCounts(false)
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    var id = options.id;
    this.data.id = id;
    this._loadData();
  },

  _loadData: function() {
    product.getDetailInfo(this.data.id, (data) => {
      this.setData({
        cartTotalCounts: cart.getCartTotalCounts(false),
        product: data
      });
    })
  },

  onReady: function() {
    wx.setNavigationBarTitle({
      title: '商品详情',
    })
  },

  bindPickChange: function(event) {
    this.setData({
      productCount: this.data.countsArray[event.detail.value],
    });
  },

  onTabsItemTap: function(event) {
    var index = product.getDataSet(event, 'index');
    this.setData({
      currentTabsIndex: index
    });
  },

  onAddingToCartTap: function(events) {
    if (this.data.isFly) {
      return;
    }
    this._flyToCartEffect(events);
    this.addToCart();
  },
  onCartTap:function(event){
    wx.switchTab({
      url: '/pages/cart/cart',
    })
  },

  addToCart: function() {
    var tempObj = {};
    var keys = ['id', 'name', 'main_img_url', 'price'];
    for (var key in this.data.product) {
      if (keys.indexOf(key) >= 0) {
        tempObj[key] = this.data.product[key];
      }
    }
    cart.add(tempObj, this.data.productCount);
  },

  _flyToCartEffect: function(events) {
    //获得当前点击的位置，距离可视区域左上角
    var touches = events.touches[0];
    var diff = {
        x: '25px',
        y: 25 - touches.clientY + 'px'
      },
      style = 'display: block;-webkit-transform:translate(' + diff.x + ',' + diff.y + ') rotate(350deg) scale(0)'; //移动距离
    this.setData({
      isFly: true,
      translateStyle: style
    });
    var that = this;
    setTimeout(() => {
      that.setData({
        isFly: false,
        translateStyle: '-webkit-transform: none;', //恢复到最初状态
        isShake: true,
      });
      setTimeout(() => {
        var counts = that.data.cartTotalCounts + that.data.productCount;
        that.setData({
          isShake: false,
          cartTotalCounts: counts
        });
      }, 200);
    }, 1000);
  },


})