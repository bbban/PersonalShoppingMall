import { Base } from '../../utils/Base.js'

class My extends Base {
  constructor() {
    super();
  }

  getUserInfo(cb) {
    var that = this;
    wx.login({
      success: function () {
        wx.getUserInfo({
          success: function (res) {
            typeof cb == "function" && cb(res.userInfo);

            //将用户昵称 提交到服务器
            // if (!that.onPay) {
            //   that._updateUserInfo(res.userInfo);
            // }

          },
          fail: function (res) {
            typeof cb == "function" && cb({
              avatarUrl: '../../imgs/icon/user@default.png',
              nickName: '零食小贩'
            });
          }
        });
      },

    })
  }

  

}
export {My};