(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-client-index"],{"014e":function(t,a,i){var e=i("10d2");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=i("4f06").default;n("3c757fcc",e,!0,{sourceMap:!1,shadowMode:!1})},"10d2":function(t,a,i){var e=i("24fb");a=e(!1),a.push([t.i,".tab-nav-plus[data-v-95c974ee]{height:%?80?%\n\t/* margin-bottom: 20upx; */}.tab-nav-plus-main[data-v-95c974ee]{width:100%;height:%?80?%;padding:%?14?% %?30?% %?0?% %?32?%\n\t/* background: #FFFFFF; */\n\t/* border-top: 0rpx solid #E4E6ED; */}.tab-nav-plus-main.fixed[data-v-95c974ee]{position:fixed;left:0;z-index:20;background:#fff;box-shadow:%?0?% %?4?% %?16?% %?0?% rgba(0,0,0,.08)}.tab-nav-plus-scroll[data-v-95c974ee]{width:100%;white-space:nowrap;height:%?76?%}.tab-nav-plus-scroll .item[data-v-95c974ee]{height:%?60?%;line-height:%?60?%;display:inline-block;margin-right:%?60?%;position:relative}.tab-nav-plus-scroll .item[data-v-95c974ee]:last-child{margin-right:0}.tab-nav-plus-scroll .item .tit[data-v-95c974ee]{text-align:center;height:%?48?%;line-height:%?48?%;font-size:%?28?%;font-weight:500}.tab-nav-plus-scroll .item .tit.on[data-v-95c974ee]{font-size:%?28?%;font-weight:600}.tab-nav-plus-main .tab-nav-plus-scroll .item .bd[data-v-95c974ee]{position:absolute;z-index:1;left:calc(50% - %?18?%);bottom:0;width:%?36?%;height:%?12?%;border-radius:%?6?% %?6?% %?0?% %?0?%}.tab-nav-plus-main.fixed  .tab-nav-plus-scroll .item .bd[data-v-95c974ee]{background:#fff}",""]),t.exports=a},2041:function(t,a,i){var e=i("72a4");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=i("4f06").default;n("d52d3c48",e,!0,{sourceMap:!1,shadowMode:!1})},2603:function(t,a,i){"use strict";i.r(a);var e=i("979e"),n=i("8e80");for(var s in n)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return n[t]}))}(s);i("54aa");var o=i("f0c5"),c=Object(o["a"])(n["default"],e["b"],e["c"],!1,null,"95c974ee",null,!1,e["a"],void 0);a["default"]=c.exports},3190:function(t,a,i){"use strict";i("7a82"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var e={data:function(){return{hyh:0,navLock:!1,datasa:[],listjq:[],listbz:[],showdyxx:!0,dataconfig:[],banners:[],datainfo:[],dataindex:[],hotdata:[],givedata:[],typetab:[{name:"推荐短剧",id:1},{name:"剧情介绍",id:2},{name:"剧情壁纸",id:3}],selecttype:0,scrollTop:0,type:1,mbgColor:this.$mbgColor}},computed:{},onPageScroll:function(t){var a=this;if(t.scrollTop>44)0==this.navLock&&(this.navLock=!0,uni.setNavigationBarColor({frontColor:"#000000",backgroundColor:"#FFFFFF",complete:function(){a.navLock=!1}}));else if(0==this.navLock){this.navLock=!0;uni.setNavigationBarColor({frontColor:"#000000",backgroundColor:this.mbgColor,complete:function(){a.navLock=!1}})}},onShareAppMessage:function(t){},onShareTimeline:function(t){},onLoad:function(t){},onShow:function(){this.getList()},methods:{changeIndex:function(t){this.typetab[t].id&&(this.type=this.typetab[t].id),console.log(this.type),this.selecttype=t},huanyihuan:function(){this.hyh<12&&(this.hyh=this.hyh+3),this.hyh>=12&&(this.hyh=0)},getList:function(){var t=this,a=this;uni.request({url:this.configs.webUrl+"/api/video/indexdata",data:{},success:function(i){uni.setStorage({key:"config",data:i.data.config}),t.banners=i.data.config.banner,i.data.config.name&&uni.setNavigationBarTitle({title:i.data.config.name}),i.data.config.mbgColor&&(t.mbgColor=i.data.config.mbgColor,uni.setNavigationBarColor({frontColor:"#000000",backgroundColor:a.mbgColor,complete:function(){t.navLock=!1}})),t.listbz=i.data.listbz,t.listjq=i.data.listjq,t.datasa=i.data.new,t.hotdata=i.data.hotdata,t.givedata=i.data.givedata},fail:function(t,a){}})},saoma:function(){},detail:function(t,a){var i=1;uni.getStorageSync("userinfo")&&(i=uni.getStorageSync("userinfo").id),uni.navigateTo({url:"/pages/client/tuan/detail?vid="+t+"&mid="+a+"&fxpid="+i})},linkTo:function(t){if(0==this.isLogin)this.showLogin=!0;else{var a=t.currentTarget.dataset.link;uni.navigateTo({url:a})}},exchange:function(t){if(0==this.isLogin)this.showLogin=!0;else{var a=t.currentTarget.dataset.id;uni.navigateTo({url:"/pages/client/integral/exchange?id="+a})}}}};a.default=e},"3ccc":function(t,a,i){"use strict";i.d(a,"b",(function(){return n})),i.d(a,"c",(function(){return s})),i.d(a,"a",(function(){return e}));var e={subTabvs:i("2603").default,homeJqjs:i("7a79").default,homeBanner:i("1e1d").default,homeDefault:i("6023").default},n=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("v-uni-view",[i("sub-tabvs",{attrs:{tabs:t.typetab,selectIndex:t.selecttype,scrollTop:t.scrollTop},on:{change:function(a){arguments[0]=a=t.$handleEvent(a),t.changeIndex.apply(void 0,arguments)}}}),1==t.selecttype?i("v-uni-view",[i("home-jqjs",{attrs:{datasa:t.listjq}})],1):t._e(),2==t.selecttype?i("v-uni-view",{staticClass:"flex space pd10_15",staticStyle:{display:"flex","flex-wrap":"wrap"}},[t._l(t.listbz,(function(t,a){return[i("v-uni-view",{key:a+"_0",staticStyle:{width:"49%","margin-bottom":"20upx"}},[i("v-uni-image",{staticStyle:{height:"500upx",width:"100%","border-radius":"18upx"},attrs:{mode:"aspectFill",src:t.img}})],1)]}))],2):t._e(),0==t.selecttype?i("v-uni-view",[i("v-uni-view",{staticClass:"home-header pd0_15 mt10",style:"background: "+t.mbgColor+";"},[i("v-uni-view",{staticClass:"home-mendian flex alcenter space plr15 cl-w9",staticStyle:{display:"none"}},[i("v-uni-navigator",{staticStyle:{width:"calc(100% - 80rpx)"},attrs:{url:"/pages/client/tuan/ss"}},[i("v-uni-view",{staticClass:"flex alcenter",staticStyle:{width:"calc(100% - 80rpx)"}},[i("v-uni-text",{staticClass:"iconfont  iconsousuo ft14 mr10"}),i("v-uni-text",{staticClass:"text-over ft14",staticStyle:{width:"calc(100% - 60rpx)"}},[t._v("请输入搜索内容")])],1)],1)],1),i("v-uni-view",{},[i("home-banner",{attrs:{banners:t.banners}})],1)],1),i("v-uni-view",{staticClass:"integral-mall-main plr15",staticStyle:{"margin-top":"30upx"}},[i("v-uni-view",{staticClass:"integal-mall-menu flex pt10 pb10"},[i("v-uni-view",{staticClass:"col2 text-center",attrs:{"data-link":"/pages/client/tuan/zjjl"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.linkTo.apply(void 0,arguments)}}},[i("v-uni-view",[i("v-uni-image",{staticStyle:{width:"100rpx",height:"100rpx"},attrs:{src:t.statics.zhuico[0]}})],1),i("v-uni-view",{staticClass:"ft14 ftw600"},[t._v("追剧")])],1),i("v-uni-view",{staticClass:"col2 text-center bd-left",attrs:{"data-link":"/pages/client/tuan/ss"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.linkTo.apply(void 0,arguments)}}},[i("v-uni-view",[i("v-uni-image",{staticStyle:{width:"100rpx",height:"100rpx"},attrs:{src:t.statics.zhuico[6]}})],1),i("v-uni-view",{staticClass:"ft14 ftw600"},[t._v("最新")])],1),i("v-uni-view",{staticClass:"col2 text-center bd-left",attrs:{"data-link":"/pages/client/tuan/ss?selectIndex=3"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.linkTo.apply(void 0,arguments)}}},[i("v-uni-view",[i("v-uni-image",{staticStyle:{width:"100rpx",height:"100rpx"},attrs:{src:t.statics.zhuico[2]}})],1),i("v-uni-view",{staticClass:"ft14 ftw600"},[t._v("排行")])],1),i("v-uni-view",{staticClass:"col2 text-center bd-left",attrs:{"data-link":"/pages/client/tuan/ss?selectIndex=2"},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.linkTo.apply(void 0,arguments)}}},[i("v-uni-view",[i("v-uni-image",{staticStyle:{width:"100rpx",height:"100rpx"},attrs:{src:t.statics.zhuico[5]}})],1),i("v-uni-view",{staticClass:"ft14 ftw600"},[t._v("免费")])],1)],1),t.hotdata[1]?i("v-uni-view",{staticClass:"mt24"},[i("v-uni-view",{staticClass:"flex alcenter space"},[i("v-uni-view",{staticClass:"flex alcenter"},[i("v-uni-image",{staticStyle:{width:"40rpx",height:"40rpx"},attrs:{src:t.statics.zhuico[0]}}),i("v-uni-text",{staticClass:"ft16 ftw600 cl-main ml15"},[t._v("本周热门")])],1),i("v-uni-view",{staticClass:"ft14 cl-notice",on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.huanyihuan()}}},[t._v("换一换")])],1),i("v-uni-view",{staticClass:"mt16 flex space"},[t._l(t.hotdata,(function(a,e){return e>=t.hyh&&e<t.hyh+3?[i("v-uni-view",{key:e+"_0",staticClass:"box pb10",staticStyle:{width:"31%",position:"relative","border-radius":"20rpx"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.detail(a.id,0)}}},[i("v-uni-image",{staticClass:"integral-mall-goods",attrs:{mode:"aspectFill",src:a.img}}),i("v-uni-view",{staticClass:"mt8 plr10 ft14 ftw400 text-center text-over cl-main"},[t._v(t._s(a.name))])],1)]:t._e()}))],2)],1):t._e(),t.givedata[3]?i("v-uni-view",{staticClass:"mt24"},[i("v-uni-view",{staticClass:"flex alcenter space"},[i("v-uni-view",{staticClass:"flex alcenter"},[i("v-uni-image",{staticStyle:{width:"40rpx",height:"40rpx"},attrs:{src:t.statics.zhuico[2]}}),i("v-uni-text",{staticClass:"ft16 ftw600 cl-main ml15"},[t._v("排行榜")])],1),i("v-uni-view",{staticClass:"ft14 cl-notice"},[t._v("每周热剧TOP3")])],1),i("v-uni-view",{staticClass:"mt16 flex space"},[t._l(t.givedata,(function(a,e){return e<3?[i("v-uni-view",{key:e+"_0",staticClass:"integral-mall-coupon",staticStyle:{width:"31%"},on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.detail(a.id,0)}}},[i("v-uni-view",{staticClass:"top"},[i("v-uni-view",{staticClass:"flex center"},[i("v-uni-view",{staticClass:"coupon-value"},[i("v-uni-image",{staticClass:"integral-tuan-l",staticStyle:{height:"280upx"},attrs:{mode:"aspectFill",src:a.img}}),i("v-uni-view",{staticClass:"ft14 plr10 cl-main ftw400 text-center text-over mt10"},[t._v(t._s(a.name))])],1)],1)],1),i("v-uni-view",{staticClass:"y-l"}),i("v-uni-view",{staticClass:"y-r"})],1)]:t._e()}))],2)],1):t._e()],1),i("home-default",{attrs:{datasa:t.datasa}})],1):t._e()],1)},s=[]},"54aa":function(t,a,i){"use strict";var e=i("014e"),n=i.n(e);n.a},5567:function(t,a,i){"use strict";i("7a82"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0;var e={props:["datasa"],data:function(){return{isLogin:!1,showBirthday:!1,showLogin:!1,showQrcode:!1,showCouponShareGet:!1}},created:function(){},methods:{detail:function(t,a){uni.navigateTo({url:"/pages/client/tuan/detail?vid="+t+"&mid="+a})},showLoginAct:function(){this.showLogin=!0},showLoginCouponShareGet:function(){this.showLogin=!0},loginYes:function(){}}};a.default=e},"5e7d":function(t,a,i){"use strict";var e=i("c32b"),n=i.n(e);n.a},"6d23":function(t,a,i){"use strict";i("7a82"),Object.defineProperty(a,"__esModule",{value:!0}),a.default=void 0,i("a9e3"),i("ac1f"),i("e25e");var e={props:{scrollTop:{type:Number,default:0},selectIndex:{type:Number,default:0},tabs:{type:Array,default:function(){return new Array}}},data:function(){return{myTop:150}},computed:{isFixed:function(){return this.scrollTop>=this.myTop},getLeft:function(){var t=0;for(var a in this.tabs){if(this.selectIndex<a)break;var i=0;i=this.selectIndex==a?32*this.tabs[a].name.length:28*this.tabs[a].name.length,t+=i}return t+=60*this.selectIndex,t>375?uni.upx2px(t-375):0}},created:function(){var t=this;setTimeout((function(){var a=uni.createSelectorQuery().in(t);a.select(".tab-nav-plus").boundingClientRect((function(a){t.myTop=a.top})).exec()}),500)},methods:{tabClick:function(t){var a=parseInt(t.currentTarget.dataset.index);this.$emit("change",a)}}};a.default=e},"72a4":function(t,a,i){var e=i("24fb");a=e(!1),a.push([t.i,".home-header[data-v-bd6cc2f8]{\n\t/* height: 300rpx; */width:100%;position:relative;border-radius:%?0?% %?0?% %?48?% %?48?%}.home-main[data-v-bd6cc2f8]{width:100%;position:relative;margin-top:%?-156?%;padding:0 %?30?%}.home-mendian[data-v-bd6cc2f8]{width:100%;height:%?84?%;background:hsla(0,0%,100%,.1);border-radius:%?42?%}.integral-mall-header[data-v-bd6cc2f8]{position:relative;height:%?320?%}.integral-mall-header .bg[data-v-bd6cc2f8]{width:100%;height:%?320?%}.integral-mall-header .main[data-v-bd6cc2f8]{position:absolute;left:0;top:0;width:100%;height:%?320?%}.swiper-integral[data-v-bd6cc2f8]{height:%?32?%;width:100%}.integral-mall-main[data-v-bd6cc2f8]{position:relative\n\t/* margin-top: -104rpx; */}.integal-mall-menu[data-v-bd6cc2f8]{width:100%;height:%?190?%;background:#fff;border-radius:%?20?%}.integral-tuan-l[data-v-bd6cc2f8]{width:100%;height:%?280?%;background:#f2f2f2;border-radius:%?16?%}.integral-mall-coupon[data-v-bd6cc2f8]{background:#fff;position:relative;border-radius:%?16?%;overflow:hidden}.integral-mall-coupon  .top[data-v-bd6cc2f8]{padding:%?0?% %?0?% %?24?% %?0?%;border-bottom:%?2?% dashed #fec675}.integral-mall-coupon  .y-l[data-v-bd6cc2f8],.integral-mall-coupon  .y-r[data-v-bd6cc2f8]{width:%?20?%;height:%?20?%;border-radius:%?10?%;background:#f5f6fa;position:absolute;z-index:2;top:%?284?%}.integral-mall-coupon  .y-l[data-v-bd6cc2f8]{left:%?-10?%}.integral-mall-coupon  .y-r[data-v-bd6cc2f8]{right:%?-10?%}.integral-mall-coupon   .coupon-value[data-v-bd6cc2f8]{width:100%}.integral-mall-coupon  .coupon-value uni-image[data-v-bd6cc2f8]{width:100%}.integral-mall-coupon  .coupon-value .num[data-v-bd6cc2f8]{width:100%;height:%?64?%;display:flex;justify-content:center;align-items:center;position:absolute;left:0;top:0}.integral-mall-goods[data-v-bd6cc2f8]{width:100%;height:%?280?%;background:#f2f2f2;border-radius:%?16?%}.titleNview-placing[data-v-bd6cc2f8]{height:0;padding-top:44px;box-sizing:initial}",""]),t.exports=a},"772c":function(t,a,i){var e=i("24fb");a=e(!1),a.push([t.i,'.showStyle[data-v-d7d72fba]::before{content:"";position:"absolute";top:0;right:0;bottom:0;left:0;background-color:rgba(31,33,41,.4);z-index:-1;-webkit-backdrop-filter:blur(80px);backdrop-filter:blur(80px)}.showStyle[data-v-d7d72fba]{position:relative}.home-header[data-v-d7d72fba]{height:%?304?%;width:100%;position:relative;background:#363b4d;border-radius:%?0?% %?0?% %?48?% %?48?%}.home-main[data-v-d7d72fba]{width:100%;position:relative;margin-top:%?-156?%;padding:0 %?30?%}.home-mendian[data-v-d7d72fba]{width:100%;height:%?84?%;background:hsla(0,0%,100%,.1);border-radius:%?42?%}.tuan-product-l[data-v-d7d72fba]{width:%?200?%;height:%?260?%;border-radius:%?16?%;background:#f2f2f2}.tuan-product-r[data-v-d7d72fba]{width:calc(100% - %?200?%);height:%?260?%}.btn-vip-adviser[data-v-d7d72fba]{width:%?200?%;height:%?60?%;border-radius:%?20?%;display:flex;justify-content:center;align-items:center;font-size:%?28?%;\n\t/* font-weight: 600; */background:#f60;color:#fff}.integral-mall-goods[data-v-d7d72fba]{width:100%;height:%?280?%;background:#f2f2f2;border-radius:%?16?%}',""]),t.exports=a},"7a79":function(t,a,i){"use strict";i.r(a);var e=i("85a6"),n=i("c405");for(var s in n)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return n[t]}))}(s);i("5e7d");var o=i("f0c5"),c=Object(o["a"])(n["default"],e["b"],e["c"],!1,null,"d7d72fba",null,!1,e["a"],void 0);a["default"]=c.exports},"85a6":function(t,a,i){"use strict";i.d(a,"b",(function(){return n})),i.d(a,"c",(function(){return s})),i.d(a,"a",(function(){return e}));var e={dialogCouponshareget:i("a35d").default},n=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("v-uni-view",[i("v-uni-view",{staticClass:"pd16_15",staticStyle:{"margin-bottom":"80upx"}},[i("v-uni-view",{},[t._l(t.datasa,(function(a,e){return[i("v-uni-view",{key:e+"_0",staticClass:"flex alcenter mb16 showStyle",on:{click:function(i){arguments[0]=i=t.$handleEvent(i),t.detail(a.id,0)}}},[i("v-uni-image",{staticClass:"tuan-product-l",attrs:{mode:"aspectFill",src:a.img}}),i("v-uni-view",{staticClass:"tuan-product-r pl15"},[i("v-uni-view",{staticClass:"ft16 ftw600 cl-main text-over1"},[t._v(t._s(a.name))]),i("v-uni-view",{staticClass:"mt5"},[i("v-uni-text",{staticClass:"ft14 cl-main text-over4",staticStyle:{"line-height":"50upx"}},[t._v(t._s(a.story))])],1)],1)],1)]}))],2)],1),i("v-uni-view",{staticClass:"home-main",staticStyle:{height:"50upx"}}),t.showCouponShareGet?i("dialog-couponshareget",{on:{loginAct:function(a){arguments[0]=a=t.$handleEvent(a),t.showLoginCouponShareGet.apply(void 0,arguments)},closed:function(a){arguments[0]=a=t.$handleEvent(a),t.showCouponShareGet=!1}}}):t._e()],1)},s=[]},"8e80":function(t,a,i){"use strict";i.r(a);var e=i("6d23"),n=i.n(e);for(var s in e)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return e[t]}))}(s);a["default"]=n.a},"979e":function(t,a,i){"use strict";i.d(a,"b",(function(){return e})),i.d(a,"c",(function(){return n})),i.d(a,"a",(function(){}));var e=function(){var t=this,a=t.$createElement,i=t._self._c||a;return i("v-uni-view",{staticClass:"tab-nav-plus"},[i("v-uni-view",{staticClass:"tab-nav-plus-main",class:t.isFixed?"fixed":""},[i("v-uni-scroll-view",{staticClass:"tab-nav-plus-scroll",attrs:{"scroll-left":t.getLeft,"scroll-with-animation":!0,"scroll-x":!0}},t._l(t.tabs,(function(a,e){return i("v-uni-view",{key:e,staticClass:"item",attrs:{"data-index":e},on:{click:function(a){arguments[0]=a=t.$handleEvent(a),t.tabClick.apply(void 0,arguments)}}},[i("v-uni-view",{staticClass:"tit",class:t.selectIndex==e?"on":"",style:{color:t.selectIndex==e?"#FF6600":"#353535"}},[t._v(t._s(a.name))])],1)})),1)],1)],1)},n=[]},"9c6b":function(t,a,i){"use strict";i.r(a);var e=i("3190"),n=i.n(e);for(var s in e)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return e[t]}))}(s);a["default"]=n.a},a247:function(t,a,i){"use strict";var e=i("2041"),n=i.n(e);n.a},c32b:function(t,a,i){var e=i("772c");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=i("4f06").default;n("4b476952",e,!0,{sourceMap:!1,shadowMode:!1})},c405:function(t,a,i){"use strict";i.r(a);var e=i("5567"),n=i.n(e);for(var s in e)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return e[t]}))}(s);a["default"]=n.a},c91b:function(t,a,i){"use strict";i.r(a);var e=i("3ccc"),n=i("9c6b");for(var s in n)["default"].indexOf(s)<0&&function(t){i.d(a,t,(function(){return n[t]}))}(s);i("a247");var o=i("f0c5"),c=Object(o["a"])(n["default"],e["b"],e["c"],!1,null,"bd6cc2f8",null,!1,e["a"],void 0);a["default"]=c.exports}}]);