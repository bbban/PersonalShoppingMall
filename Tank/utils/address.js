import {
  Base
} from 'Base.js';
import {
  Config
} from 'config.js';

class Address extends Base {
  constructor() {
    super();
  }

  setAddressInfo(res) {
    var province = res.provinceName || res.province;
    var city = res.cityName || res.city;
    var country = res.countyName || res.country;
    var detail = res.detailInfo || res.detail;

    var totalDetail = city + country + detail;

    if (!this.isCenterCity(province)) {
      totalDetail = province + totalDetail;
    };
    return totalDetail;

  }

  isCenterCity(name) {
    var centerCitys = ['北京市', '天津市', '上海市', '重庆市'],
      flag = centerCitys.indexOf(name) >= 0;
    return flag;
  }

  submitAddress(data, callBack) {
    data = this._setUpAddress(data);
    var param = {
      url: 'address',
      type: 'post',
      data: data,
      sCallBack: function(res) {
        callBack && callBack(true, res);
      },
      eCallBack(res) {
        callBack && callBack(false, res);
      }
    };
    this.request(param);
  }

  _setUpAddress(res, callback) {
    var formData = {
      name: res.userName,
      province: res.provinceName,
      city: res.cityName,
      country: res.countyName,
      mobile: res.telNumber,
      detail: res.detailInfo
    };
    return formData;
  }

  getAddress(callBack) {
    var that = this;
    var param = {
      url: 'address',
      sCallBack: function (res) {
        if (res) {
          res.totalDetail = that.setAddressInfo(res);
          callBack && callBack(res);
        }
      }
    };
    this.request(param);
  }


}

export {
  Address
}