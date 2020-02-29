 // pages/home/home.js
import {Home} from 'home-model.js'

var home = new Home();

Page({
  data: {

  },

  onLoad:function(){
    this._loadData();
  },

  _loadData:function(){
    var id = 1;
    home.getBannerData(id,(res)=>{
      this.setData({
        BannerArray:res
      })
    });

    home.getThemeData((res) =>{
      this.setData({
        ThemeArray: res
      })
    });

    home.getProductorData((res) => {
      this.setData({
        ProductsArray: res
      })
    });
  },

  onProductsItemTap:function(event){
    var id = home.getDataSet(event,'id');
    wx.navigateTo({
      url: '../product/product?id='+ id,
      success: function(res) {},
      fail: function(res) {},
      complete: function(res) {},
    })
  },

  onThemesItemTap: function (event) {
    var id = home.getDataSet(event, 'id');
    var name = home.getDataSet(event, 'name');
    wx.navigateTo({
      url: '../theme/theme?id=' + id + '&name=' + name,
      success: function (res) { },
      fail: function (res) { },
      complete: function (res) { },
    })
  }

})