(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-client-vipcard-recharge"],{"0ca2":function(t,n,a){"use strict";var e=a("53b4"),i=a.n(e);i.a},"0d4a":function(t,n,a){"use strict";a.r(n);var e=a("1dfe"),i=a("8e91");for(var o in i)["default"].indexOf(o)<0&&function(t){a.d(n,t,(function(){return i[t]}))}(o);a("0ca2");var c=a("f0c5"),s=Object(c["a"])(i["default"],e["b"],e["c"],!1,null,"4c61710a",null,!1,e["a"],void 0);n["default"]=s.exports},"1dfe":function(t,n,a){"use strict";a.d(n,"b",(function(){return i})),a.d(n,"c",(function(){return o})),a.d(n,"a",(function(){return e}));var e={dialogCouponshare:a("82cd").default},i=function(){var t=this,n=t.$createElement,a=t._self._c||n;return a("v-uni-view",{staticClass:"pd16_15"},[a("v-uni-view",{staticClass:"box over-hide"},[a("v-uni-view",{staticClass:"recharge-header"},[a("v-uni-image",{attrs:{src:t.statics.moneyRecharge}}),a("v-uni-view",{staticClass:"main pd24_20"},[a("v-uni-view",{staticClass:"ft16 cl-w9"},[t._v("当前余额")]),a("v-uni-view",{staticClass:"mt16 ftw600 ft32 cl-w"},[t._v(t._s(t.userinfo.money))])],1)],1),a("v-uni-navigator",{attrs:{url:"/pages/client/vipcard/moneylog"}},[a("v-uni-view",{staticClass:"pd16_15 flex alcenter space"},[a("v-uni-text",{staticClass:"ft14 ftw600 cl-main"},[t._v("余额明细")]),a("v-uni-text",{staticClass:"iconfont iconicon_arrow_circle ft20",style:{color:t.tempColor}})],1)],1)],1),a("v-uni-radio-group",{on:{change:function(n){arguments[0]=n=t.$handleEvent(n),t.changeNum.apply(void 0,arguments)}}},[a("v-uni-view",{staticClass:"flex wrap space mt16"},t._l(t.moneyList,(function(n,e){return a("v-uni-view",{key:e,staticClass:"box pd20_15 flex alcenter space",class:e>1?"mt16":"",staticStyle:{width:"330rpx"}},[a("v-uni-text",{staticClass:"ft16 cl-main ftw600"},[t._v("¥"+t._s(n.num))]),a("v-uni-radio",{attrs:{value:n.num,checked:t.num==n.num,color:t.tempColor}})],1)})),1)],1),a("v-uni-radio-group",{staticStyle:{display:"none"},on:{change:function(n){arguments[0]=n=t.$handleEvent(n),t.changetd.apply(void 0,arguments)}}},[a("v-uni-view",{staticClass:"mt16"},t._l(t.tdList,(function(n,e){return a("v-uni-view",{key:e,staticClass:"box pd20_15 flex alcenter space",class:e>=1?"mt16":""},[a("v-uni-text",{staticClass:"ft16 cl-main ftw600"},[t._v(t._s(n.name))]),a("v-uni-radio",{attrs:{value:n.num,checked:t.tdnum==n.num,color:t.tempColor}})],1)})),1)],1),t.getCoupon>0?a("v-uni-view",{staticClass:"mt16 tag-coupon",style:t.getTagStyle},[t._v("送价值"+t._s(t.getCoupon)+"元优惠券")]):t._e(),a("v-uni-view",{staticClass:"list-call",staticStyle:{"margin-top":"40upx"}},[a("v-uni-input",{staticClass:"sl-input tdadf",attrs:{type:"number",placeholder:"自定义金额"},model:{value:t.num,callback:function(n){t.num=n},expression:"num"}})],1),a("v-uni-view",{staticClass:"form-footer-h"},[a("v-uni-view",{staticClass:"form-footer form-footer-h"},[a("v-uni-view",{staticClass:"form-footer-main pd10_15"},[a("v-uni-button",{staticClass:"btn-big",staticStyle:{width:"100%",float:"right"},style:t.getBtnStyle,on:{click:function(n){arguments[0]=n=t.$handleEvent(n),t.paySuccess.apply(void 0,arguments)}}},[t._v("支付 "+t._s(t.num>0?"¥"+t.num:""))])],1)],1)],1),t.cancelShow?a("hFormAlert",{attrs:{title:"卡密充值",name:"cancel_desc",placeholder:"请输入卡密卡号"},on:{confirm:function(n){arguments[0]=n=t.$handleEvent(n),t.confirm.apply(void 0,arguments)},cancel:function(n){arguments[0]=n=t.$handleEvent(n),t.cancel.apply(void 0,arguments)}}}):t._e(),t.showCouponInvite?a("dialog-couponshare",{on:{closed:function(n){arguments[0]=n=t.$handleEvent(n),t.closedInvite.apply(void 0,arguments)}}}):t._e()],1)},o=[]},"53b4":function(t,n,a){var e=a("ca7f");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var i=a("4f06").default;i("07505362",e,!0,{sourceMap:!1,shadowMode:!1})},"8e91":function(t,n,a){"use strict";a.r(n);var e=a("e048"),i=a.n(e);for(var o in e)["default"].indexOf(o)<0&&function(t){a.d(n,t,(function(){return e[t]}))}(o);n["default"]=i.a},ca7f:function(t,n,a){var e=a("24fb");n=e(!1),n.push([t.i,".tag-coupon[data-v-4c61710a]{width:100%;height:%?80?%;border-radius:%?16?%;text-align:center;line-height:%?80?%;font-size:%?28?%}.recharge-header[data-v-4c61710a]{height:%?240?%;width:100%;position:relative}.recharge-header uni-image[data-v-4c61710a]{width:100%;height:%?240?%}.recharge-header .main[data-v-4c61710a]{position:absolute;width:100%;height:%?240?%;left:0;top:0}.tdadf[data-v-4c61710a]{border:1px solid #f8f8f8;padding:10px 10px;text-align:center;height:%?80?%;background:#fff;border-radius:8px;box-shadow:0 4px 20px 0 rgba(0,0,0,.04);font-size:%?30?%;color:#666}",""]),t.exports=n},e048:function(t,n,a){"use strict";a("7a82");var e=a("4ea4").default;Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var i=e(a("c7eb")),o=e(a("1da1"));a("e25e"),a("e9c4"),a("ac1f"),a("466d"),a("c975");var c=e(a("326f")),s={components:{hFormAlert:c.default},data:function(){return{num:"",moneyList:[{num:"30",coupon:0},{num:"50",coupon:0},{num:"100",coupon:0},{num:"200",coupon:0}],tdnum:"901",openid:"",tdList:[{num:"901",name:"微信H5(50-100-200)"},{num:"904",name:"支付宝H5(30-50-100-200)"}],numa:"",userinfo:"",showCouponInvite:!1,cancelShow:!1}},computed:{getCoupon:function(){for(var t in this.moneyList)if(this.moneyList[t].num==this.num)return this.moneyList[t].coupon;return 0}},onLoad:function(t){t.price&&(this.num=t.price)},onShow:function(){this.ongrzlTap()},onShareAppMessage:function(t){},onShareTimeline:function(t){},methods:{changeNum:function(t){this.num=parseInt(t.detail.value)},changetd:function(t){this.tdnum=t.detail.value},paykami:function(){this.cancelShow=!0},cancel:function(){this.cancelShow=!1},confirm:function(t){var n=this;if(console.log(t.cancel_desc),!t.cancel_desc)return this.cancelShow=!1,uni.showToast({title:"请输入卡密",icon:"none"}),!1;var a={};a.token=uni.getStorageSync("userinfo").token,a.uid=uni.getStorageSync("userinfo").id,a.crd=t.cancel_desc,uni.request({url:this.configs.webUrl+"/api/user/kami",data:a,success:function(t){1==t.data.code?(n.userinfo.money=t.data.data.userinfo.money,uni.showToast({title:t.data.msg,icon:"none"})):uni.showToast({title:t.data.msg,icon:"none"})},fail:function(t,n){}}),this.cancelShow=!1},paySuccess:function(){this.paySuccessa()},paywxxcx:function(){if(!this.num)return uni.showToast({title:"金额不对",icon:"none"}),!1;if(!this.openid)return uni.showToast({title:"Openid味加载",icon:"none"}),!1;uni.showLoading({title:"Loading"});var t={};t.token=uni.getStorageSync("userinfo").token,t.total=this.num,t.tdnum=this.tdnum,t.openid=this.openid,uni.request({url:this.configs.webUrl+"/api/paywx/paywxxcx",data:t,success:function(t){console.log(t.data),1==t.data.code?(uni.hideLoading(),uni.requestPayment({provider:"wxpay",appId:t.data.data.appId,timeStamp:t.data.data.timeStamp+"",nonceStr:t.data.data.nonceStr,package:t.data.data.package,signType:"MD5",paySign:t.data.data.sign,success:function(t){uni.showModal({title:"温馨提示",content:"支付成功",showCancel:!1,confirmText:"确定",success:function(t){t.confirm?this.ongrzlTap():t.cancel}}),console.log("success:"+JSON.stringify(t))},fail:function(t){uni.showModal({title:"支付失败",content:JSON.stringify(t),showCancel:!1,confirmText:"确定",success:function(t){t.confirm||t.cancel}}),console.log("fail:"+JSON.stringify(t))}})):(uni.hideLoading(),uni.showModal({title:"温馨提示",content:JSON.stringify(t.data),showCancel:!1,confirmText:"确定",success:function(t){t.confirm?uni.navigateBack():t.cancel}}))},fail:function(t,n){}})},paySuccessa:function(){if(!this.num)return uni.showToast({title:"金额不对",icon:"none"}),!1;uni.showLoading({title:"Loading"});var t={iswx:""},n=window.navigator.userAgent.toLowerCase();"micromessenger"==n.match(/MicroMessenger/i)&&(t.iswx="wxgzh"),t.token=uni.getStorageSync("userinfo").token,t.total=this.num,t.tdnum=this.tdnum,uni.request({url:this.configs.webUrl+"/api/paywx/paywxh5",data:t,success:function(t){console.log(t),1==t.data.code?(uni.hideLoading(),console.log(t.data.data),window.open(t.data.data,"_self")):(uni.hideLoading(),uni.showToast({title:t.data.msg,icon:"none"}))},fail:function(t,n){}})},ongrzlTap:function(){var t=this;return(0,o.default)((0,i.default)().mark((function n(){var a;return(0,i.default)().wrap((function(n){while(1)switch(n.prev=n.next){case 0:a={},a.token=uni.getStorageSync("userinfo").token,a.uid=uni.getStorageSync("userinfo").id,uni.request({url:t.configs.webUrl+"/api/user/index",data:a,success:function(n){if(1==n.data.code){var a=n.data.data;if(a.isLogin=!0,t.userinfo=a,t.jfdt=a.config.jfdt?a.config.jfdt:"",uni.setStorage({key:"userinfo",data:n.data.data}),n.data.data.avatar){var e=n.data.data.avatar;-1!=e.indexOf("data:image")?t.avatar="":-1!=e.indexOf("http")?t.avatar=n.data.data.avatar:t.avatar=t.configs.imgUrl+n.data.data.avatar}else t.avatar="";uni.setStorage({key:"avatar",data:t.avatar})}else uni.showToast({title:n.data.msg,icon:"none"})},fail:function(t,n){}});case 4:case"end":return n.stop()}}),n)})))()},closedInvite:function(){this.showCouponInvite=!1;var t=getCurrentPages();uni.navigateBack({delta:t.length})},wxlogin:function(){var t=this,n=this;uni.login({timeout:1e4,provider:"weixin",success:function(a){uni.request({url:t.configs.webUrl+"/api/user/getOpenid",method:"GET",data:{token:uni.getStorageSync("userinfo").token,code:a.code},success:function(t){console.log(t.data.data),1==t.data.code?n.openid=t.data.data.openid:uni.showModal({title:"温馨提示",content:JSON.stringify(t.data),showCancel:!1,confirmText:"确定",success:function(t){t.confirm?uni.navigateBack():t.cancel}})},fail:function(t){console.log(t)}})},fail:function(t){console.log(t)}})}}};n.default=s}}]);