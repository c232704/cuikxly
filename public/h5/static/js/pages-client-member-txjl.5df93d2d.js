(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-client-member-txjl"],{"0927":function(t,i,a){"use strict";var e=a("ed52"),n=a.n(e);n.a},"092f":function(t,i,a){"use strict";a.r(i);var e=a("a139"),n=a("dadb");for(var o in n)["default"].indexOf(o)<0&&function(t){a.d(i,t,(function(){return n[t]}))}(o);a("0927");var s=a("f0c5"),l=Object(s["a"])(n["default"],e["b"],e["c"],!1,null,"39f75680",null,!1,e["a"],void 0);i["default"]=l.exports},"10cc":function(t,i,a){"use strict";a.r(i);var e=a("35e7"),n=a("c008");for(var o in n)["default"].indexOf(o)<0&&function(t){a.d(i,t,(function(){return n[t]}))}(o);a("9b02");var s=a("f0c5"),l=Object(s["a"])(n["default"],e["b"],e["c"],!1,null,"76b7c960",null,!1,e["a"],void 0);i["default"]=l.exports},"2e61":function(t,i,a){"use strict";a("7a82"),Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0;var e={name:"uni-load-more",props:{status:{type:String,default:"more"},showIcon:{type:Boolean,default:!0},color:{type:String,default:"#777777"},contentText:{type:Object,default:function(){return{contentdown:"上拉显示更多",contentrefresh:"正在加载...",contentnomore:"没有更多数据了"}}}},data:function(){return{}}};i.default=e},3372:function(t,i,a){var e=a("24fb");i=e(!1),i.push([t.i,".uni-load-more[data-v-76b7c960]{display:flex;flex-direction:row;height:%?80?%;align-items:center;justify-content:center}.uni-load-more__text[data-v-76b7c960]{font-size:%?28?%;color:#999}.uni-load-more__img[data-v-76b7c960]{height:24px;width:24px;margin-right:10px}.uni-load-more__img>uni-view[data-v-76b7c960]{position:absolute}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]{width:6px;height:2px;border-top-left-radius:1px;border-bottom-left-radius:1px;background:#999;position:absolute;opacity:.2;-webkit-transform-origin:50%;transform-origin:50%;-webkit-animation:load-data-v-76b7c960 1.56s ease infinite;animation:load-data-v-76b7c960 1.56s ease infinite}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(1){-webkit-transform:rotate(90deg);transform:rotate(90deg);top:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(2){-webkit-transform:rotate(180deg);transform:rotate(180deg);top:11px;right:0}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(3){-webkit-transform:rotate(270deg);transform:rotate(270deg);bottom:2px;left:9px}.uni-load-more__img>uni-view uni-view[data-v-76b7c960]:nth-child(4){top:11px;left:0}.load1[data-v-76b7c960],\n.load2[data-v-76b7c960],\n.load3[data-v-76b7c960]{height:24px;width:24px}.load2[data-v-76b7c960]{-webkit-transform:rotate(30deg);transform:rotate(30deg)}.load3[data-v-76b7c960]{-webkit-transform:rotate(60deg);transform:rotate(60deg)}.load1 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:0s;animation-delay:0s}.load2 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:.13s;animation-delay:.13s}.load3 uni-view[data-v-76b7c960]:nth-child(1){-webkit-animation-delay:.26s;animation-delay:.26s}.load1 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.39s;animation-delay:.39s}.load2 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.52s;animation-delay:.52s}.load3 uni-view[data-v-76b7c960]:nth-child(2){-webkit-animation-delay:.65s;animation-delay:.65s}.load1 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:.78s;animation-delay:.78s}.load2 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:.91s;animation-delay:.91s}.load3 uni-view[data-v-76b7c960]:nth-child(3){-webkit-animation-delay:1.04s;animation-delay:1.04s}.load1 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.17s;animation-delay:1.17s}.load2 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.3s;animation-delay:1.3s}.load3 uni-view[data-v-76b7c960]:nth-child(4){-webkit-animation-delay:1.43s;animation-delay:1.43s}@-webkit-keyframes load-data-v-76b7c960{0%{opacity:1}100%{opacity:.2}}",""]),t.exports=i},"35e7":function(t,i,a){"use strict";a.d(i,"b",(function(){return e})),a.d(i,"c",(function(){return n})),a.d(i,"a",(function(){}));var e=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("v-uni-view",{staticClass:"uni-load-more"},[a("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:"loading"===t.status&&t.showIcon,expression:"status === 'loading' && showIcon"}],staticClass:"uni-load-more__img"},[a("v-uni-view",{staticClass:"load1"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load2"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1),a("v-uni-view",{staticClass:"load3"},[a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}}),a("v-uni-view",{style:{background:t.color}})],1)],1),a("v-uni-text",{staticClass:"uni-load-more__text",style:{color:t.color}},[t._v(t._s("more"===t.status?t.contentText.contentdown:"loading"===t.status?t.contentText.contentrefresh:t.contentText.contentnomore))])],1)},n=[]},6216:function(t,i,a){var e=a("24fb");i=e(!1),i.push([t.i,".uni-title[data-v-39f75680]{color:#444;font-size:%?32?%;font-weight:400}.uni-text[data-v-39f75680]{font-size:%?28?%}.uni-h5[data-v-39f75680]{font-size:%?32?%;color:#3a3a3a;font-weight:500}",""]),t.exports=i},"9b02":function(t,i,a){"use strict";var e=a("a56f"),n=a.n(e);n.a},a139:function(t,i,a){"use strict";a.d(i,"b",(function(){return n})),a.d(i,"c",(function(){return o})),a.d(i,"a",(function(){return e}));var e={uniLoadMore:a("10cc").default},n=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("v-uni-view",{staticClass:"pd16_15"},[t._l(t.listData,(function(i,e){return[a("v-uni-view",{key:e+"_0",staticClass:"box pd16_15 alcenter space",staticStyle:{"margin-bottom":"30upx"}},[a("v-uni-view",{staticClass:"flex",staticStyle:{width:"100%"}},[a("v-uni-view",{staticStyle:{width:"60%"}},[a("v-uni-view",{staticClass:"ft14 cl-main"},[t._v("提现姓名："+t._s(i.name))]),a("v-uni-view",{staticClass:"mt8 ft12 cl-notice"},[t._v("帐号："+t._s(i.cord))]),a("v-uni-view",{staticClass:"mt8"},[a("v-uni-text",{staticClass:"ft12 cl-notice"},[t._v("手续费：")]),a("v-uni-text",{staticClass:"ft12 cl-main"},[t._v(t._s(i.sxf))]),a("v-uni-text",{staticClass:"ft12 cl-main",staticStyle:{float:"right"}},[t._v(t._s(i.createtime))])],1)],1),a("v-uni-view",{staticClass:"uni-triplex-right",staticStyle:{width:"40%","text-align":"right","line-height":"40upx"}},[a("v-uni-text",{staticClass:"uni-h5",staticStyle:{width:"100%",display:"block","font-size":"24upx"}},[t._v(t._s(i.type))]),a("v-uni-text",{staticClass:"uni-h5",staticStyle:{"font-size":"24upx",color:"#ff0000",width:"100%",display:"block"}},[t._v("￥"+t._s(i.money))]),1==i.iscl?a("v-uni-text",{staticClass:"uni-h5",staticStyle:{width:"100%",display:"block","font-size":"24upx"}},[t._v("未审核")]):t._e(),2==i.iscl?a("v-uni-text",{staticClass:"uni-h5",staticStyle:{width:"100%",display:"block","font-size":"24upx"}},[t._v("已审核")]):t._e(),3==i.iscl?a("v-uni-text",{staticClass:"uni-h5",staticStyle:{width:"100%",display:"block","font-size":"24upx"}},[t._v("已驳回")]):t._e()],1)],1),i.memoj?a("v-uni-view",{staticClass:"mt8",staticStyle:{width:"100%"}},[a("v-uni-text",{staticClass:"ft12 cl-notice"},[t._v("备注：")]),a("v-uni-text",{staticClass:"ft12 cl-main"},[t._v(t._s(i.memoj))])],1):t._e()],1)]})),a("uni-load-more",{attrs:{status:t.status,"content-text":t.contentText}})],2)},o=[]},a56f:function(t,i,a){var e=a("3372");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=a("4f06").default;n("3572b880",e,!0,{sourceMap:!1,shadowMode:!1})},c008:function(t,i,a){"use strict";a.r(i);var e=a("2e61"),n=a.n(e);for(var o in e)["default"].indexOf(o)<0&&function(t){a.d(i,t,(function(){return e[t]}))}(o);i["default"]=n.a},dadb:function(t,i,a){"use strict";a.r(i);var e=a("ed63"),n=a.n(e);for(var o in e)["default"].indexOf(o)<0&&function(t){a.d(i,t,(function(){return e[t]}))}(o);i["default"]=n.a},ed52:function(t,i,a){var e=a("6216");e.__esModule&&(e=e.default),"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var n=a("4f06").default;n("bed4ddbe",e,!0,{sourceMap:!1,shadowMode:!1})},ed63:function(t,i,a){"use strict";a("7a82");var e=a("4ea4").default;Object.defineProperty(i,"__esModule",{value:!0}),i.default=void 0,a("99af");var n=e(a("10cc")),o={components:{uniLoadMore:n.default},data:function(){return{listData:[],last_id:0,reload:!0,status:"more",contentText:{contentdown:"上拉加载更多",contentrefresh:"加载中",contentnomore:"没有数据了"}}},onReachBottom:function(){this.status="more",this.getList()},onLoad:function(){this.getList()},methods:{getList:function(){var t=this,i={};this.last_id>0&&(this.status="loading",i.offset=10*this.last_id,i._=(new Date).getTime()+""),i.limit=10,i.token=uni.getStorageSync("userinfo").token,uni.request({url:this.configs.webUrl+"/api/user/txinfo",data:i,success:function(i){if(console.log(i.data),i.data.total>0){var a=i.data.rows;t.listData=t.reload?a:t.listData.concat(a),t.reload=!1,t.last_id=t.last_id+1,i.data.total<10*t.last_id&&(t.status="")}else t.contentText.contentdown="没有数据"},fail:function(t,i){}})}}};i.default=o}}]);