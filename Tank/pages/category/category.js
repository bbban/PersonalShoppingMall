// pages/category/category.js
// category-model
import { Category } from 'category-model.js';
var category = new Category();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    currentMenuIndex: 0,
    loadedData: {}
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadData();
  },

  _loadData: function () {

    category.getCatrgoryType((categoryData) => {

      this.setData({
        categoryTypeArray: categoryData
      });

      category.getProductByCatrgory(categoryData[0].id, (data) => {
        var dataObj = {
          products: data,
          topImgUrl: categoryData[0].img.url,
          title: categoryData[0].name
        }
        this.setData({
          categoryProducts: dataObj
        });
        this.data.loadedData[0] = dataObj;
      });

    });
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    wx.setNavigationBarTitle({
      title: '商品分类',
    })
  },

  islodadedData: function (index) {
    if (this.data.loadedData[index]) {
      return true;
    }
    return false;
  },

  changeCategory: function (event) {
    var index = category.getDataSet(event, 'index');
    var id = category.getDataSet(event, 'id');
    if (!this.islodadedData(index)) {
      category.getProductByCatrgory(id, (data) => {
        var dataObj = {
          products: data,
          topImgUrl: this.data.categoryTypeArray[index].img.url,
          title: this.data.categoryTypeArray[index].name
        }
        this.setData({
          categoryProducts: dataObj,
          currentMenuIndex: index
        });
        this.data.loadedData[index] = dataObj
      });
    }
    else {
      this.setData({
        categoryProducts: this.data.loadedData[index],
        currentMenuIndex: index
      });
    }

  },
  onProductsItemTap: function (event) {
    var id = category.getDataSet(event, 'id');
    wx, wx.navigateTo({
      url: '../product/product?id=' + id,
      success: function (res) { },
      fail: function (res) { },
      complete: function (res) { },
    })
  },
})